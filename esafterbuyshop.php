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

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_ROOT_DIR_."/modules/esafterbuyshop/classes/TransmissionService.php");

class EsAfterBuyShop extends Module
{

//    protected $entity_manager;

    const FSABSHOP_PARTNER_ID = 'esafterbuyshop-partner-id';
    const FSABSHOP_PARTNER_PASS = 'esafterbuyshop-partner-pass';
    const FSABSHOP_USER_ID = 'esafterbuyshop-user-id';
    const FSABSHOP_PRODUCT_IDENT = "esafterbuyshop-product-identification";
    const FSABSHOP_CRONJOB_URL = "cronjob-url";

    public function __construct()
    {
        $this->name = 'esafterbuyshop';
        $this->tab = 'billing_invoicing';
        $this->version = '1.0.15';
        $this->author = 'FancySoftware|EschSoftware';
        $this->need_instance = 0;
        $this->is_eu_compatible = 1;
        $this->bootstrap = true;
        $this->module_key = "742af9a532f595dd53f423f9c30f27f2";

        $this->currencies = false;

        parent::__construct();

        $this->displayName = $this->l('PrestaShop to Afterbuy order transmission');
        $this->description = $this->l('Submits the orders from Prestashop to AfterBuy over their SHOP-Interface API.');
        $this->ps_version_compilancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install()
            || !$this->loadTables()
            || !$this->createConfig()
            || !$this->installTab()
            || !$this->registerHook('displayOrderConfirmation')) {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->deleteOrderTranferedTable()
            || !$this->deleteConfig()
            || !$this->uninstallTab()
            || !$this->unregisterHook('displayOrderConfirmation')) {
            return false;
        }
        return true;
    }

    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminABShopCron';

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Cron Jobs';
        }

        $tab->id_parent = -1;
        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminABShopCron');

        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }

        return false;
    }

    protected function loadTables()
    {
        return $this->createOrderTransferedTable();
    }

    protected function createOrderTransferedTable()
    {
        return Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `'
                                          ._DB_PREFIX_.'fs_abshop_order` '
                                          .'(id_order int(10) primary key, status varchar(10), afterbuy_error_response text )');
    }

    protected function deleteOrderTranferedTable()
    {
        return Db::getInstance()->Execute('DROP TABLE `'._DB_PREFIX_.'fs_abshop_order`');
    }

    protected function createConfig()
    {
        Configuration::updateValue("installation-time", date("Y-m-d H:i:s"));
        $token = Tools::encrypt(Tools::getShopDomainSsl().time());
        Configuration::updateGlobalValue('FSABSHOP_EXECUTION_TOKEN', $token);

        return true;
    }

    protected function deleteConfig()
    {
        Configuration::deleteByName("installation-time");
        Configuration::deleteByName("FSABSHOP_EXECUTION_TOKEN");
        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $error = false;
            // validate user-id
            $key = self::FSABSHOP_USER_ID;
            $user_id = Tools::getValue($key);
            if (!$user_id || empty($user_id)) {
                $error = true;
                $output .= $this->displayError($this->l('Field \'AfterBuy Username (User-ID)\' is required.'));
            }

            // validate partner-id
            $key = self::FSABSHOP_PARTNER_ID;
            $partner_id = Tools::getValue($key);
            if (!$partner_id || empty($partner_id)) {
                $error = true;
                $output .= $this->displayError($this->l('Field \'Interface Number (Partner-ID)\' is required.'));
            } elseif (!preg_match('/^[0-9]*$/', $partner_id)) {
                $error = true;
                $err_msg = $this->l('Only digits are allowed for the field  \'Interface Number (Partner-ID)\'.');
                $output .= $this->displayError($err_msg);
            }

            // validate partner-pass
            $key = self::FSABSHOP_PARTNER_PASS;
            $partner_pass = Tools::getValue($key);
            if (!$partner_pass || empty($partner_pass)) {
                $error = true;
                $output .= $this->displayError($this->l('Field \'Interface Password (Partner-Pass)\' is required.'));
            }
            // validate partner-pass
            // Duplikat

            $psident = Tools::getValue("psident");
            $abident = Tools::getValue("abident");
            $alternativeArticleReference1 = Tools::getValue("alternativeArticleReference1");
            $alternativeArticleReference2 = Tools::getValue("alternativeArticleReference2");
            $transferMethod = Tools::getValue("esafterbuyshop-transfer-method");
            $artnrtransfer = Tools::getValue("artnrtransfer");

            if (!$error) {
                Configuration::updateValue(self::FSABSHOP_USER_ID, $user_id);
                Configuration::updateValue(self::FSABSHOP_PARTNER_ID, $partner_id);
                Configuration::updateValue(self::FSABSHOP_PARTNER_PASS, $partner_pass);
                Configuration::updateValue('esafterbuyshop-psident', $psident);
                Configuration::updateValue('esafterbuyshop-abident', $abident);
                Configuration::updateValue('esafterbuyshop-alternativeArticleReference1', $alternativeArticleReference1);
                Configuration::updateValue('esafterbuyshop-alternativeArticleReference2', $alternativeArticleReference2);
                Configuration::updateValue('esafterbuyshop-transfer-method', $transferMethod);
                Configuration::updateValue('esafterbuyshop-artnrtransfer', $artnrtransfer);
                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }


        return $output.$this->displayForm();
    }


    protected function displayForm()
    {
        $q = "SELECT id_order, status, afterbuy_error_response FROM `"._DB_PREFIX_."fs_abshop_order` WHERE status != 'transfered'";
        $unsuccessful_orders = Db::getInstance()->executeS($q);


        $link = new Link();
        if (strpos(_PS_VERSION_, "1.6", 0) === 0) {
            $admin_folder = $this->getAdminDir();
            $path = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.$admin_folder;
            $cron_url = $path.'/'.$link->getAdminLink('AdminABShopCron', false);
        } else {
            $cron_url = $link->getAdminLink('AdminABShopCron', false);
        }
        $cronjobUrl = $cron_url.'&token='.Configuration::getGlobalValue('FSABSHOP_EXECUTION_TOKEN');

        // Load current value
        $token = Tools::getAdminTokenLite('AdminModules');
        $link = 'index.php?controller=AdminModules&configure=esafterbuyshop&token='.$token;
        $this->context->smarty->assign(array(
            'esafterbuyshop_url' => $link,
            'esafterbuyshop_partner_id' => Configuration::get(self::FSABSHOP_PARTNER_ID),
            'esafterbuyshop_partner_pass' => Configuration::get(self::FSABSHOP_PARTNER_PASS),
            'psident' => Configuration::get('esafterbuyshop-psident'),
            'abident' => Configuration::get('esafterbuyshop-abident'),
            'alternativeArticleReference1' => Configuration::get('esafterbuyshop-alternativeArticleReference1'),
            'alternativeArticleReference2' => Configuration::get('esafterbuyshop-alternativeArticleReference2'),
            'esafterbuyshop_user_id' => Configuration::get(self::FSABSHOP_USER_ID),
            'esafterbuyshop_transfer_method' => Configuration::get('esafterbuyshop-transfer-method'),
            'esafterbuyshop_cronjob_url' => $cronjobUrl,
            'unsuccessful_orders' => $unsuccessful_orders,
            'artnrtransfer' => Configuration::get('esafterbuyshop-artnrtransfer')
        ));

        return $this->context->smarty->fetch($this->getLocalPath()."views/templates/admin/configuration.tpl");
    }

    protected function getAdminDir()
    {
        return basename(_PS_ADMIN_DIR_);
    }

    public function hookDisplayOrderConfirmation($params)
    {
        $transferMethod = Configuration::get('esafterbuyshop-transfer-method');
        if (!isset($transferMethod) || $transferMethod === 'hook') {
            $transmissionService = new TransmissionService();
            $transmissionService->transferUntransferedOrders();
        }
    }
}
