{*
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
*}
<div class="alert alert-info">
    <img src="../modules/esafterbuyshop/logo.png" style="float:left; margin-right:15px;" height="60">
    <p><strong>{l s='Note' mod='esafterbuyshop'}</strong>
    </p>
    <p>{l s='Please verify that the reference on your products is set and only contains numbers.' mod='esafterbuyshop'}</p>
</div>
<form id="configuration_form" class="defaultForm form-horizontal esafterbuyshop"
      action="{$esafterbuyshop_url|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
    <input type="hidden" name="submitesafterbuyshop" value="1">

    <div class="panel" id="fieldset_0">

        <div class="panel-heading">
            {l s='Settings' mod='esafterbuyshop'}
        </div>


        <div class="form-wrapper">

            <div class="form-group">

                <label class="control-label col-lg-3 required">
                    {l s='AfterBuy Username (User-ID)' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">

                    <input type="text" name="esafterbuyshop-user-id" id="esafterbuyshop-user-id"
                           value="{$esafterbuyshop_user_id|escape:'htmlall':'UTF-8'}" class="" required="required">

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3 required">
                    {l s='Interface Number (Partner-ID)' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">

                    <input type="text" name="esafterbuyshop-partner-id" id="esafterbuyshop-partner-id"
                           value="{$esafterbuyshop_partner_id|escape:'htmlall':'UTF-8'}" class="" required="required">

                </div>

            </div>


            <div class="form-group">

                <label class="control-label col-lg-3 required">
                    {l s='Interface Password (Partner-Pass)' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">

                    <input type="text" name="esafterbuyshop-partner-pass" id="esafterbuyshop-partner-pass"
                           value="{$esafterbuyshop_partner_pass|escape:'htmlall':'UTF-8'}" class="" required="required">

                </div>

            </div>
            <div class="form-group">

                <label class="control-label col-lg-3 required">
                    {l s='Article identification' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">

                    <div class="row">

                        <label class="control-label col-md-2">{l s='PrestaShop' mod='esafterbuyshop'}</label>

                        <div class="col-md-4">

                            <select name="psident" class="custom-select">
                                <option value="reference" {if $psident == "reference"}selected="selected"{/if}>
                                    {l s='Reference' mod='esafterbuyshop'}
                                </option>
                                <option value="id" {if $psident == "id"}selected="selected"{/if}>
                                    {l s='ID' mod='esafterbuyshop'}
                                    (Modules "Afterbuy zu PrestaShop Produktübertragung wird benötigt, damit Varianten
                                    zugeordnet werden können")
                                </option>
                                <option value="ean" {if $psident == "ean"}selected="selected"{/if}>
                                    {l s='EAN' mod='esafterbuyshop'}
                                </option>
                            </select>
                        </div>

                        <label class="control-label col-md-2">{l s='Afterbuy' mod='esafterbuyshop'}</label>

                        <div class="col-md-4">

                            <select name="abident" class="custom-select">
                                <option value="1" {if $abident == "1"}selected="selected"{/if}>{l s='Reference' mod='esafterbuyshop'}</option>
                                <option value="0"
                                        {if $abident == "0"}selected="selected"{/if}>{l s='ID' mod='esafterbuyshop'}</option>
                                <option value="2"
                                        {if $abident == "2"}selected="selected"{/if}>{l s='External reference' mod='esafterbuyshop'}</option>
                                <option value="13"
                                        {if $abident == "13"}selected="selected"{/if}>{l s='EAN' mod='esafterbuyshop'}</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>

            <div class="form-group">

                <label class="control-label col-lg-3">
                    {*{l s='' mod='esafterbuyshop'}*}
                </label>

                <div class="col-lg-9">

                    <div class="row">

                        <label class="control-label col-md-2">{l s='Alternative article reference 1' mod='esafterbuyshop'}</label>

                        <div class="col-md-4">

                            <select name="alternativeArticleReference1" class="custom-select">
                                <option value="nothing">{l s='Nothing' mod='esafterbuyshop'}</option>
                                <option value="reference" {if $alternativeArticleReference1 == 'reference'}selected="selected"{/if}>
                                    {l s='Reference' mod='esafterbuyshop'}
                                </option>
                                <option value="id" {if $alternativeArticleReference1 == 'id'}selected="selected"{/if}>
                                    {l s='ID' mod='esafterbuyshop'}
                                    (Modules "Afterbuy zu PrestaShop Produktübertragung wird benötigt, damit Varianten
                                    zugeordnet werden können")
                                </option>
                                <option value="ean" {if $alternativeArticleReference1 == 'ean'}selected="selected"{/if}>
                                    {l s='EAN' mod='esafterbuyshop'}
                                </option>
                            </select>
                        </div>

                        <label class="control-label col-md-2">{l s='Alternative article reference 2' mod='esafterbuyshop'}</label>

                        <div class="col-md-4">

                            <select name="alternativeArticleReference2" class="custom-select">
                                <option value="nothing">{l s='Nothing' mod='esafterbuyshop'}</option>
                                <option value="reference" {if $alternativeArticleReference2 == 'reference'}selected="selected"{/if}>
                                    {l s='Reference' mod='esafterbuyshop'}
                                </option>
                                <option value="id" {if $alternativeArticleReference2 == 'id'}selected="selected"{/if}>
                                    {l s='ID' mod='esafterbuyshop'}
                                    (Modules "Afterbuy zu PrestaShop Produktübertragung wird benötigt, damit Varianten
                                    zugeordnet werden können")
                                </option>
                                <option value="ean" {if $alternativeArticleReference2 == 'ean'}selected="selected"{/if}>
                                    {l s='EAN' mod='esafterbuyshop'}
                                </option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Artikel-Nr. Übertragung' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">
                    <select name="artnrtransfer" class="custom-select">
                        <option value="artnr">{l s='Artikel-Nr.' mod='esafterbuyshop'}</option>
                        <option value="id" {if $artnrtransfer == 'id'}selected="selected"{/if}>
                            {l s='Id' mod='esafterbuyshop'}
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='CronJob URL' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">
                    <input type="text" name="esafterbuyshop-cronjob-url" id="esafterbuyshop-cronjob-url"
                           value="{$esafterbuyshop_cronjob_url|escape:'htmlall':'UTF-8'}" class="" required="required">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-lg-3">
                    {l s='Transfer method' mod='esafterbuyshop'}
                </label>

                <div class="col-lg-9">
                    <select name="esafterbuyshop-transfer-method" id="esafterbuyshop-transfer-method">
                        <option value="hook" {if $esafterbuyshop_transfer_method eq 'hook'}selected="selected"{/if}>{l s='Direct order transfer on order (Hook: displayOrderConfirmation)' mod='esafterbuyshop'}</option>
                        <option value="cronjob" {if $esafterbuyshop_transfer_method eq 'cronjob'}selected="selected"{/if}>{l s='Order transfer with over cronjob url' mod='esafterbuyshop'}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="submitesafterbuyshop"
                    class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save' mod='esafterbuyshop'}
            </button>
        </div>
    </div><!-- /.form-wrapper -->

    {if $unsuccessful_orders}
        <div class="panel" id="fieldset_1">
            <div class="panel-heading">
                {l s='Unsuccessful submitted orders log' mod='esafterbuyshop'}
            </div>
            <div class="form-wrapper">
                <table class="table">
                    <tr>
                        <th>{l s='Order id' mod='esafterbuyshop'}</th>
                        <th>{l s='Status' mod='esafterbuyshop'}</th>
                        <th>{l s='Error' mod='esafterbuyshop'}</th>
                    </tr>
                    {foreach item=unsuccessful_order from=$unsuccessful_orders}
                        <tr>
                            <td>{$unsuccessful_order.id_order|escape:'htmlall':'UTF-8'}</td>
                            <td>{$unsuccessful_order.status|escape:'htmlall':'UTF-8'}</td>
                            <td>{$unsuccessful_order.afterbuy_error_response|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
    {/if}

</form>
