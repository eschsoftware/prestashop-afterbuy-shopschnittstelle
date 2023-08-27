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

require_once(_PS_ROOT_DIR_ . "/modules/esafterbuyshop/classes/TransmissionService.php");

class AdminABShopCronController extends ModuleAdminController
{

    public function __construct()
    {
        if (Tools::getValue('token') != Configuration::getGlobalValue('FSABSHOP_EXECUTION_TOKEN')) {
            die('Invalid token');
        }

        parent::__construct();

        $this->postProcess();

        die;
    }

    public function postProcess()
    {
        $transmissionService = new TransmissionService();
        $transmissionService->transferUntransferedOrders();
    }
}
