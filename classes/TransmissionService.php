<?php
/**
 * PrestaShop-Afterbuy-Shopschnittstelle
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU Affero General Public License v3.0
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/agpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to mail@eschsoftware.de so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Ihr Projektname to newer
 * versions in the future.
 *
 *  @author Michael Esch - EschSoftware <mail@eschsoftware.de>
 *  @copyright  2023
 *  @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License v3.0
 */

require_once(_PS_ROOT_DIR_ . "/modules/esafterbuyshop/classes/InterfaceABShop.php");

class TransmissionService
{

    const FSABSHOP_PARTNER_ID = "esafterbuyshop-partner-id";
    const FSABSHOP_PARTNER_PASS = "esafterbuyshop-partner-pass";
    const FSABSHOP_USER_ID = "esafterbuyshop-user-id";

    public function transferUntransferedOrders()
    {
        $id_orders = $this->findUntransferedOrders();

        foreach ($id_orders as $id_order) {
            list($success, $error_msg) = $this->sendOrderToAfterBuy($id_order);
            if ($success) {
                $this->insertIntoTransferedTable($id_order);
            } else {
                $this->insertIntoTransferedTable($id_order, 'untransfered', $error_msg);
            }
        }
    }

    protected function findUntransferedOrders()
    {
        $installationTimeStr = Configuration::get("installation-time");
        $query               = "SELECT o.id_order FROM `"
                               . _DB_PREFIX_ . "orders` o WHERE o.date_add >= '" . $installationTimeStr
                               . "' AND o.id_order NOT IN (SELECT id_order FROM `" . _DB_PREFIX_ . "fs_abshop_order` WHERE status = 'transfered')";
        $results   = Db::getInstance()->executeS($query, true, false);
        $id_orders = array();

        foreach ($results as $row) {
            $id_orders[] = $row['id_order'];
        }

        return $id_orders;
    }

    protected function insertIntoTransferedTable($id_order, $status = 'transfered', $error_msg = '')
    {
        DB::getInstance()->query(
            "INSERT INTO `"
            . _DB_PREFIX_ . "fs_abshop_order` (id_order, status, afterbuy_error_response) values (" . $id_order
            . ", '".$status."', '".$error_msg."') ON DUPLICATE KEY UPDATE status = '".$status."', afterbuy_error_response = '".$error_msg."'"
        );
    }

    private function findAfterBuyCombinationsId($id_product, $id_combination)
    {
        try {
            $query   = sprintf("SELECT id_ab_combination FROM `" . _DB_PREFIX_ . "fsabxml_products` WHERE id_combination = %d AND id_product = %d", $id_combination, $id_product);
            $results = Db::getInstance()->executeS($query);
            if ($results) {
                foreach ($results as $result) {
                    return $result['id_ab_combination'];
                }
            }
        } catch (PrestaShopDatabaseException $e) {
            // skip
        }

        return null;
    }

    protected function sendOrderToAfterBuy($id_order)
    {
        $abs_param_helper = new InterfaceABShop();

        $order = new Order((int) $id_order);

        $abs_param_helper->params[InterfaceABShop::PARAM_ACTION]     = InterfaceABShop::PARAM_ACTION_VALUES[0];
        $abs_param_helper->params[InterfaceABShop::PARAM_PARTNER_ID] = Configuration::get(self::FSABSHOP_PARTNER_ID);
        $abs_param_helper->params[InterfaceABShop::PARAM_PARTNER_PASS]
            = Configuration::get(self::FSABSHOP_PARTNER_PASS);
        $abs_param_helper->params[InterfaceABShop::PARAM_USER_ID]    = Configuration::get(self::FSABSHOP_USER_ID);
        $abs_param_helper->params[InterfaceABShop::PARAM_NOFEEDBACK] = 2;
        $articleIdent = Configuration::get('esafterbuyshop-abident');
        if (!isset($articleIdent)) {
            $articleIdent = "1";
        }
        $abs_param_helper->params[InterfaceABShop::PARAM_ARTIKELERKENNUNG] = $articleIdent;

        $this->setOrder($abs_param_helper, $order);
        $this->setProducts($abs_param_helper, $order);
        $this->setCustomer($abs_param_helper, $order);

        return $abs_param_helper->send();
    }

    protected function setOrder(InterfaceABShop $helper, $order)
    {

        $carrier                                                = new Carrier((int) $order->id_carrier);
        $helper->params[InterfaceABShop::PARAM_VERSANDART]    = $carrier->name;
        $helper->params[InterfaceABShop::PARAM_VERSANDKOSTEN] = $this->afterbuyFloatFormat($order->total_shipping);
        $helper->params[InterfaceABShop::PARAM_ZAHLART]       = $order->payment;
        $helper->params[InterfaceABShop::PARAM_VERSANDGRUPPE] = "shop";
        $helper->params[InterfaceABShop::PARAM_REFERENCE] = $order->reference;
        $payments                                               = $order->getOrderPayments();
        if ($payments) {
            $helper->params[InterfaceABShop::PARAM_SETPAY] = true;
            $payment = $payments[0];
            $helper->params[InterfaceABShop::PARAM_PAYMENTSTATUS] = $payments->payment_method;
            $helper->params[InterfaceABShop::PARAM_PAYMENTTRANSACTIONID] = $payments[0]->transaction_id;
            $currency = new Currency($payment->id_currency);
            if ($currency) {
                $helper->params[InterfaceABShop::PARAM_SOLDCURRENCY] = $currency->iso_code;
            }
        }
        $helper->params[InterfaceABShop::PARAM_VID] = $order->reference;
    }

    protected function setProducts(InterfaceABShop $helper, $order)
    {
        $lang_id = Configuration::get('PS_LANG_DEFAULT');

        $products                                         = $order->getProducts();
        $helper->params[InterfaceABShop::PARAM_POS_ANZ] = sizeof($products);
        $psident = Configuration::get('esafterbuyshop-psident');

        $i = 1;
        foreach ($products as $product_data) {
            $stammId = null;
            $id_product           = $product_data['id_product'];
            $id_product_attribute = $product_data['product_attribute_id'];
            $combination          = null;
            if ($id_product_attribute != 0) {
                $combination = new Combination((int) $id_product_attribute);
            }
            $product = new Product((int) $id_product);

            $tax_rate = (1 + ($product_data['tax_rate']) / 100);
            if ($combination == null) {
                $weight    = $this->afterbuyFloatFormat($product_data['weight']);
                $reference = $product_data['reference'];
                $name      = $product->name[$lang_id];
                if ($psident == 'id') {
                    $stammId = $id_product;
                } elseif ($psident == 'ean') {
                    $stammId = $product->ean13;
                } else {
                    //fallback
                    $stammId = $reference;
                }

                $key = 'esafterbuyshop-alternativeArticleReference1';
                $extArticleRef1 = $this->getExternalArticleReference($key, $id_product, $product->ean13, $reference);
                $key = 'esafterbuyshop-alternativeArticleReference2';
                $extArticleRef2 = $this->getExternalArticleReference($key, $id_product, $product->ean13, $reference);

                $reference = $this->handleArtNr($product);
            } else {
                $weight    = $this->afterbuyFloatFormat($combination->weight);
                $reference = $combination->reference;
                $name      = $product->name[$lang_id];

                $combinationId = $this->findAfterBuyCombinationsId($id_product, $combination->id);

                if ($psident == 'id') {
                    $stammId = $combinationId;
                } elseif ($psident == 'ean') {
                    $stammId = $combination->ean13;
                } else {
                    //fallback
                    $stammId = $reference;
                }

                $key = 'esafterbuyshop-alternativeArticleReference1';
                $extArticleRef1 = $this->getExternalArticleReference($key, $combinationId, $combination->ean13, $reference);
                $key = 'esafterbuyshop-alternativeArticleReference2';
                $extArticleRef2 = $this->getExternalArticleReference($key, $combinationId, $combination->ean13, $reference);

                $reference = $this->handleArtNr($combination);
            }
            $roundedPrice   = $this->afterbuyFloatFormat(round($product_data['product_price'] * $tax_rate, 2));


            $helper->params[InterfaceABShop::PARAM_PREFIX_ARTIKELGEWICHT . $i] = $weight;
            $helper->params[InterfaceABShop::PARAM_PREFIX_ARTIKELEPREIS . $i]  = $roundedPrice;
            $helper->params[InterfaceABShop::PARAM_PREFIX_ARTIKELNAME . $i]    = $name;
            $helper->params[InterfaceABShop::PARAM_PREFIX_ARTIKELNR . $i]      = $reference;
            $helper->params[InterfaceABShop::PARAM_PREFIX_ARTIKELSTAMMID . $i] = $stammId;
            $helper->params[InterfaceABShop::PARAM_PREFIX_ARITKELMWST . $i]    = $product_data['tax_rate'];
            $helper->params[InterfaceABShop::PARAM_PREFIX_ARTIKELMENGE . $i]   = $product_data['product_quantity'];

            if ($extArticleRef1 != null) {
                $helper->params[InterfaceABShop::PARAM_PREFIX_ALTERNARTIKELNR1 . $i] = $extArticleRef1;
            }
            if ($extArticleRef2 != null) {
                $helper->params[InterfaceABShop::PARAM_PREFIX_ALTERNARTIKELNR2 . $i] = $extArticleRef2;
            }

            $i ++;
        }
    }

    private function handleArtNr($product)
    {
        $artnrtransfer = Configuration::get('esafterbuyshop-artnrtransfer');
        if ($artnrtransfer == 'id') {
            return $product->id;
        } else {
            return $product->reference;
        }
    }

    protected function getExternalArticleReference($key, $id, $ean, $reference)
    {
        $option = Configuration::get($key);
//        Configuration::updateValue('esafterbuyshop-alternativeArticleReference1', $alternativeArticleReference1);
//        Configuration::updateValue('esafterbuyshop-alternativeArticleReference2', $alternativeArticleReference2);
        if (!isset($option) || $option == '' || $option == 'nothing') {
            return null;
        }

        if ($option == 'reference') {
            return $reference;
        } elseif ($option == 'id') {
            return $id;
        } else {
            // ean
            return $ean;
        }
    }

    protected function setCustomer(InterfaceABShop $helper, $order)
    {
        $lang_id = Configuration::get('PS_LANG_DEFAULT');

        $customer                                               = $order->getCustomer();
        $helper->params[InterfaceABShop::PARAM_KBENUTZERNAME] = $customer->email;
        $gender                                                 = new Gender((int) $customer->id_gender);
        $helper->params[InterfaceABShop::PARAM_KANREDE]       = $gender->name[$lang_id];


        $invoice_address                                    = new Address((int) $order->id_address_invoice);
        $helper->params[InterfaceABShop::PARAM_KVORNAME]  = $invoice_address->firstname;
        $helper->params[InterfaceABShop::PARAM_KNACHNAME] = $invoice_address->lastname;
        $helper->params[InterfaceABShop::PARAM_KFIRMA]    = $invoice_address->company;
        $helper->params[InterfaceABShop::PARAM_KSTRASSE]  = $invoice_address->address1;
        $helper->params[InterfaceABShop::PARAM_KSTRASSE2] = $invoice_address->address2;
        $helper->params[InterfaceABShop::PARAM_KPLZ]      = $invoice_address->postcode;
        $helper->params[InterfaceABShop::PARAM_KORT]      = $invoice_address->city;

        $invoice_state                                        = new State((int) $invoice_address->id_state);
        $helper->params[InterfaceABShop::PARAM_KBUNDESLAND] = $invoice_state->name;
        $helper->params[InterfaceABShop::PARAM_KTELEFON]    = $invoice_address->phone;

        $invoice_country                                = new Country((int) $invoice_address->id_country);
        $helper->params[InterfaceABShop::PARAM_KLAND] = $invoice_country->name[$lang_id];

        $helper->params[InterfaceABShop::PARAM_KEMAIL]    = $customer->email;
        $helper->params[InterfaceABShop::PARAM_KBIRTHDAY] = $this->afterbuyDateFormat($customer->birthday);

        $abweichende = $order->id_address_delivery != $order->id_address_invoice;
        $helper->params[InterfaceABShop::PARAM_LIEFERANSCHRIFT] = $this->afterbuyBooleanFormat($abweichende);
        if ($abweichende) {
            $delivery_address                                    = new Address((int) $order->id_address_delivery);
            $helper->params[InterfaceABShop::PARAM_KLFIRMA]    = $delivery_address->company;
            $helper->params[InterfaceABShop::PARAM_KLVORNAME]  = $delivery_address->firstname;
            $helper->params[InterfaceABShop::PARAM_KLNACHNAME] = $delivery_address->lastname;
            $helper->params[InterfaceABShop::PARAM_KLSTRASSE]  = $delivery_address->address1;
            $helper->params[InterfaceABShop::PARAM_KLSTRASSE2] = $delivery_address->address2;
            $helper->params[InterfaceABShop::PARAM_KLPLZ]      = $delivery_address->postcode;
            $helper->params[InterfaceABShop::PARAM_KLORT]      = $delivery_address->city;
            $helper->params[InterfaceABShop::PARAM_KLTELEFON]  = $delivery_address->phone;

            $delivery_country                                = new Country((int) $delivery_address->id_country);
            $helper->params[InterfaceABShop::PARAM_KLLAND] = $delivery_country->name[$lang_id];
        }

        $helper->params[InterfaceABShop::PARAM_KUNDENERKENNUNG] = 1;
    }

    protected function afterbuyFloatFormat($number)
    {
        return number_format($number, 2, ",", "");
    }

    /**
     * @param $date - Y-m-d
     *
     * Eine Datum im Format DD.MM.YYYY HH:MM:SS.
     * Beispiel: 01.01.1970 01:00:00
     *
     */
    protected function afterbuyDateFormat($date)
    {
        return date("d.m.Y H:i:s", strtotime($date));
    }

    protected function afterbuyBooleanFormat($bool)
    {
        return $bool ? "1" : "0";
    }
}
