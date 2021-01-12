<?php
/*
* @info     Платёжный модуль BGPB для JoomShopping
* @package  bgpb
* @author   esas.by
* @license  GNU/GPL
*/
namespace esas\cmsgate\joomshopping;

use esas\cmsgate\CmsConnectorJoomshopping;
use esas\cmsgate\hutkigrosh\RegistryHutkigroshJoomshopping;
use esas\cmsgate\messenger\Messages;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\RequestParams;
use esas\cmsgate\view\admin\ConfigForm;
use esas\cmsgate\view\admin\ConfigPageJoomshopping;
use esas\cmsgate\view\ViewUtils;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use PaymentRoot;
use Throwable;

defined('_JEXEC') or die('Restricted access');

class CmsgatePaymentRootJoomshopping extends PaymentRoot
{
    /**
     * Отображение формы с настройками платежного шлюза (админка)
     * Для отображения ошибок в форме, сами формы должны быть сохранены в сессии
     * @param $params
     */
    function showAdminFormParams($params)
    {
        try {
            $configForms = new ConfigPageJoomshopping();
            $configFormCommon = RegistryHutkigroshJoomshopping::getRegistry()->getConfigForm();
            $this->validateFields($configFormCommon);
            $configForms->addForm($configFormCommon);
            echo $configForms->generate();
        } catch (Exception $e) {
            Factory::getApplication()->enqueueMessage(ViewUtils::logAndGetMsg("admin", $e), 'error');
        } catch (Throwable $e) {
            Factory::getApplication()->enqueueMessage(ViewUtils::logAndGetMsg("admin", $e), 'error');
        }
    }

    /**
     * @param ConfigForm $configForm
     */
    private function validateFields($configForm)
    {
        if (!$configForm->isValid()) {
            Factory::getApplication()->enqueueMessage(RegistryHutkigroshJoomshopping::getRegistry()->getTranslator()->translate(Messages::INCORRECT_INPUT), 'error');
        }
    }

    const RESP_CODE_OK = '0';
    const RESP_CODE_CANCELED = '2018';


    function checkTransaction($pmconfigs, $order, $act)
    {
        /**
         * Тут никаких проверок ответа от PS не делаем, т.к. сделали их ранее в Controller-х
         * Просто сохраняем враппер в сессии для возможности обращения к нему в getStatusFromResCode
         */
        $orderWrapper = Registry::getRegistry()->getOrderWrapper($order->order_id);
        Factory::getSession()->set('orderWrapper', $orderWrapper);
        return array(0, "", $orderWrapper->getExtId());
    }

    /**
     * На основе кода ответа от платежного шлюза задаем статус заказу
     * @param int $rescode
     * @param array $pmconfigs
     * @return mixed
     */
    function getStatusFromResCode($rescode, $pmconfigs)
    {
        $orderWrapper=  Factory::getSession()->get('orderWrapper');
        return $orderWrapper->getStatus();
    }

    /**
     * При каких кодах ответов от платежного шлюза считать оплату неуспешной.
     * @return array
     */
    function getNoBuyResCode()
    {
        return array(self::RESP_CODE_CANCELED);
    }

    /**
     * Метод-обертка для addInvoice
     * Форма отображаемая клиенту на step7.
     * @param $pmconfigs
     * @param $order
     * @throws Throwable
     */
    function showEndForm($pmconfigs, $order)
    {
        try {
            $orderWrapper = Registry::getRegistry()->getOrderWrapper($order->order_id);
            $this->addInvoice($orderWrapper);
            $redirectParams = array(
                "js_paymentclass" => CmsConnectorJoomshopping::getPaymentCode(),
                RequestParams::ORDER_ID => $order->order_id);
            Factory::getApplication()->redirect(CmsConnectorJoomshopping::generateControllerPath("checkout", "step7") . '&' . http_build_query($redirectParams));
        } catch (Throwable $e) {
            $this->redirectError($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->redirectError($e->getMessage());
        }

    }

    public function addInvoice($orderWrapper) {

    }

    function redirectError($message)
    {
        Factory::getApplication()->redirect(Route::_('index.php?option=com_jshopping&controller=cart&task=view', FALSE), stripslashes($message), 'error');
    }

    // возможно, уже не надо
    function getUrlParams($pmconfigs)
    {
        $reqest_params = Factory::getApplication()->input->request->getArray();
        $params = array();
        $params['order_id'] = $reqest_params[RequestParams::ORDER_ID];
        $params['hash'] = '';
        $params['checkHash'] = false;
        $params['checkReturnParams'] = false;
        return $params;
    }
}

?>