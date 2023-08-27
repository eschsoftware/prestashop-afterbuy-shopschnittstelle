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

class InterfaceABShop
{
    const PARAM_ACTION = "action";
    const PARAM_ACTION_VALUES = array("new");
    const PARAM_PARTNER_ID = "PartnerID";
    const PARAM_PARTNER_PASS = "PartnerPass";
    const PARAM_USER_ID = "UserID";
    const PARAM_POS_ANZ = "PosAnz";
    const PARAM_KBENUTZERNAME = "Kbenutzername";
    const PARAM_KANREDE = "Kanrede";
    const PARAM_KFIRMA = "KFirma";
    const PARAM_KVORNAME = "KVorname";
    const PARAM_KNACHNAME = "KNachname";
    const PARAM_KSTRASSE = "KStrasse";
    const PARAM_KSTRASSE2 = "KStrasse2";
    const PARAM_KPLZ = "KPLZ";
    const PARAM_KORT = "KOrt";
    const PARAM_KBUNDESLAND = "KBundesland";
    const PARAM_KTELEFON = "Ktelefon";
    const PARAM_KFAX = "Kfax";
    const PARAM_KEMAIL = "Kemail";
    const PARAM_KLAND = "KLand";
    const PARAM_KBIRTHDAY = "KBirthday";
    const PARAM_LIEFERANSCHRIFT = "Lieferanschrift";
    const PARAM_KLFIRMA = "KLFirma";
    const PARAM_KLVORNAME = "KLVorname";
    const PARAM_KLNACHNAME = "KLNachname";
    const PARAM_KLSTRASSE = "KLStrasse";
    const PARAM_KLSTRASSE2 = "KLStrasse2";
    const PARAM_KLPLZ = "KLPLZ";
    const PARAM_KLORT = "KLOrt";
    const PARAM_KLLAND =  "KLLand";
    const PARAM_KLTELEFON = "KLTelefon";
    const PARAM_HAENDLER = "Haendler";
    const PARAM_PREFIX_ARTIKELNR = "Artikelnr_";
    const PARAM_PREFIX_ALTERNARTIKELNR1 = "AlternArtikelNr1_";
    const PARAM_PREFIX_ALTERNARTIKELNR2 = "AlternArtikelNr2_";
    const PARAM_PREFIX_ARTIKELNAME = "Artikelname_";
    const PARAM_PREFIX_ARTIKELEPREIS = "ArtikelEpreis_";
    const PARAM_PREFIX_ARITKELMWST = "ArtikelMwSt_";
    const PARAM_PREFIX_ARTIKELMENGE = "ArtikelMenge_";
    const PARAM_PREFIX_ARTIKELGEWICHT = "ArtikelGewicht_";
    const PARAM_PREFIX_ARTIKELLINK = "ArtikelLink_";
    const PARAM_PREFIX_ATTRIBUTE = "Attribute_";
    const PARAM_PREFIX_ARTIKELSTAMMID = "ArtikelStammID_";
    const PARAM_KOMMENTAR = "Kommentar";
    const PARAM_USECOMPLWEIGHT = "UseComplWeight";
    const PARAM_BUYDATE = "BuyDate";
    const PARAM_BESTANDART = "Bestandart";
    const PARAM_VERSANDART = "Versandart";
    const PARAM_VERSANDKOSTEN = "Versandkosten";
    const PARAM_ZAHLARTENAUFSCHLAG = "ZahlartenAufschlag";
    const PARAM_ZAHLART = "Zahlart";
    const PARAM_ZFUNKTIONSID = "ZFunktionsID";
    const PARAM_BANKNAME = "Bankname";
    const PARAM_BLZ = "BLZ";
    const PARAM_KONTONUMMER = "Kontonummer";
    const PARAM_KONTOINHABER = "Kontoinhaber";
    const PARAM_USSTID = "UsStID0";
    const PARAM_NOFEEDBACK = "NoFeedback";
    const PARAM_NOVERSANDCALC = "NoVersandCalc";
    const PARAM_VERSANDGRUPPE = "Versandgruppe";
    const PARAM_MWSTNICHTAUSWEISEN = "MwStNichtAusweisen";
    const PARAM_MARKIERUNGID = "MarkierungID";
    const PARAM_EKUNDENNR = "EKundenNr";
    const PARAM_KUNDENERKENNUNG = "Kundenerkennung";
    const PARAM_NOEBAYNAMEAKTU = "NoeBayNameAktu";
    const PARAM_ARTIKELERKENNUNG = "Artikelerkennung";
    const PARAM_VMEMO = "VMemo";
    const PARAM_VID = "VID";
    const PARAM_SOLDCURRENCY = "SoldCurrency";
    const PARAM_SETPAY = "SetPay";
    const PARAM_PAYDATE = "PayDate";
    const PARAM_CHECKVID = "CheckVID";
    const PARAM_CHECKPACKSTATION = "CheckPackstation";
    const PARAM_OVERRIDEMARKID = "OverrideMarkID";
    const PARAM_BILLSAFETRANSACTIONID = "BillsafeTransactionID";
    const PARAM_BILLSAFEORDERNUMBER = "BillsafeOrdernumber";
    const PARAM_BIC = "BIC";
    const PARAM_IBAN = "IBAN";
    const PARAM_REFERENCE = "reference";
    const PARAM_PAYMENTSTATUS = "PaymentStatus";
    const PARAM_PAYMENTTRANSACTIONID = "PaymentTransactionId";

    public $params = array();

    public function __construct()
    {
        $this->params = array();
    }

    private function getInvalidErrors()
    {
        $errors = array();
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_ACTION, true, 3, self::PARAM_ACTION_VALUES));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PARTNER_ID, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PARTNER_PASS, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_USER_ID, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_POS_ANZ, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KBENUTZERNAME, true, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KANREDE, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KFIRMA, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KVORNAME, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KNACHNAME, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KSTRASSE, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KSTRASSE2, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KPLZ, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KORT, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KBUNDESLAND, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KTELEFON, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KFAX, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLAND, true, 10));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KORT, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KBUNDESLAND, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KTELEFON, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KFAX, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KEMAIL, true, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLAND, true, 10));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KBIRTHDAY, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_LIEFERANSCHRIFT, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLFIRMA, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLVORNAME, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLNACHNAME, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLSTRASSE, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLSTRASSE2, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLPLZ, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLORT, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLLAND, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KLTELEFON, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_HAENDLER, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELNR, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ALTERNARTIKELNR1, false, 100));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ALTERNARTIKELNR2, false, 20));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELNAME, true, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELEPREIS, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARITKELMWST, true));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELMENGE, true, 10));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELGEWICHT, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELLINK, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ATTRIBUTE, false, 10));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PREFIX_ARTIKELSTAMMID, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KOMMENTAR, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_USECOMPLWEIGHT, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BUYDATE, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BESTANDART, false, 7));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_VERSANDART, false, 150));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_VERSANDKOSTEN, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_ZAHLARTENAUFSCHLAG, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_ZAHLART, false, 150));
        $errors = array_merge($errors, $this->isParamValid(
            self::PARAM_ZFUNKTIONSID,
            false,
            null,
            array(
                /*Überweisung*/ 1,
                /*Bar/Abholung*/ 2,
                /*Nachnahme*/ 4,
                /*PayPal*/ 5,
                /*Überweisung/Rechnung*/ 6,
                /*Bankeinzug*/ 7,
                /*Click&Buy*/ 9,
                /*Expresskauf/Bonicheck*/ 11,
                /*Sofortüberweisung*/ 12,
                /*Nachnahme/Bonicheck*/ 13,
                /*Ebay Express*/ 14,
                /*Moneybookers*/ 15,
                /*Kreditkarte*/ 16,
                /*Lastschrift*/ 17,
                /*Billsafe*/ 18,
                /*Kreditkartenzahlung*/ 19,
                /*Ideal*/ 20,
                /*Carte Bleue*/ 21,
                /*Onlineüberweisung*/ 23,
                /*Giropay*/ 24,
                /*Dankort*/ 25,
                /*EPS*/ 26,
                /*Przelewy24*/ 27,
                /*Carta Si:*/ 28,
                /*Postepay*/ 29,
                /*Nordea Solo Sweden*/ 30,
                /*Nordea Solo Finland*/ 31,
                /*Billsafe Ratenkauf*/ 34
            )
        ));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BANKNAME, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BLZ, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KONTONUMMER, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KONTOINHABER, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_USSTID, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_NOFEEDBACK, false, null, array(0,1,2)));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_NOVERSANDCALC, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_VERSANDGRUPPE, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_MWSTNICHTAUSWEISEN, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_MARKIERUNGID, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_EKUNDENNR, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_KUNDENERKENNUNG, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_NOEBAYNAMEAKTU, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_ARTIKELERKENNUNG, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_VMEMO, false, 255));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_VID, false, 40));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_SOLDCURRENCY, false, 3));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_SETPAY, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PAYDATE, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_CHECKVID, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_CHECKPACKSTATION, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_OVERRIDEMARKID, false));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BILLSAFETRANSACTIONID, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BILLSAFEORDERNUMBER, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_BIC, false, 75));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_IBAN, false, 75));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_REFERENCE, false, 50));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PAYMENTSTATUS, false, 20));
        $errors = array_merge($errors, $this->isParamValid(self::PARAM_PAYMENTTRANSACTIONID, false, 25));

        return $errors;
    }

    public function isValid()
    {
        return sizeof($this->getInvalidErrors()) > 0;
    }

    protected function isParamValid($param_key, $required = false, $max_length = null, array $allowed_values = null)
    {
        if (!($required and isset($this->params[$param_key]))) {
            return array($param_key." is required");
        } elseif (!($max_length != null and Tools::strlen($this->params[$param_key]) <= $max_length)) {
            return array($param_key." is too long");
        } elseif (!($allowed_values != null and in_array($this->params[$param_key], $allowed_values))) {
            return array("contains no valid value");
        }
        return array();
    }

    protected function paramsHelper(array $params)
    {
        $paramString = '?';
        $first = true;
        foreach ($params as $key => $value) {
            if (!$first) {
                $paramString .= '&';
            }
            $value = urlencode($value);
            $paramString .= "$key=$value";
            $first = false;
        }

        return $paramString;
    }

    public function send()
    {
        if ($this->isValid()) {
            $param_str = $this->paramsHelper($this->params);
            if (_PS_MODE_DEV_) {
                echo $param_str."<br>";
            }

            $url = "https://api.afterbuy.de/afterbuy/ShopInterfaceUTF8.aspx" ;

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($this->params)
                )
            );
            $context  = stream_context_create($options);
            $response = Tools::file_get_contents($url, false, $context);


            if (_PS_MODE_DEV_) {
                echo $response;
            }

            $xml = new SimpleXMLElement($response);
            $success = $xml->success == "1";
            if ($success) {
                Logger::addLog("AfterBuy received successfully the order.");
            } else {
                $error = $xml->errorlist->error;
                Logger::addLog("An error occurred during order transmission. Error-message: " . $error);
            }

            return array($success, $this->createErrorMsgFromXml($xml));
        }
        return array(false, implode(", ", $this->getInvalidErrors()));
    }

    private function createErrorMsgFromXml($xml)
    {
        $msg = "";
        if ($xml->errorlist && $xml->errorlist->error) {
            foreach ($xml->errorlist->error as $error) {
                $msg .= $error . ", ";
            }
            $msg = Tools::substr($msg, 0, (sizeof($msg) - 2));
        }
        return $msg;
    }
}
