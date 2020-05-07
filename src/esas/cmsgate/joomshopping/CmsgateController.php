<?php
namespace esas\cmsgate\joomshopping;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\Logger as HgLogger;
use esas\cmsgate\utils\RequestParams;
use esas\cmsgate\wrappers\SystemSettingsWrapperJoomshopping;
use \JshoppingControllerBase;

class CmsgateController extends JshoppingControllerBase
{
    

    /**
     * В Joomla после оформления заказа и перехода на стадию "finish". Происходит очистка
     * сессии. И если необходимо повторно отобразить итоговую страницу с инструкцией по оплате счета
     * приходится или подпихивать в сессию переменную jshop_end_order_id или делать через этот метод контроллера
     */
    function complete()
    {
        try {
            $orderNumber = $_REQUEST[RequestParams::ORDER_NUMBER];
            $orderWrapper = Registry::getRegistry()->getOrderWrapperByOrderNumber($orderNumber);
            $jorder = $orderWrapper->getJOrder();
            $pm_method = $jorder->getPayment();
            $paymentsysdata = $pm_method->getPaymentSystemData();
            $payment_system = $paymentsysdata->paymentSystem;
            // проверяем что для указанного заказа оплата производилась через ХуткиГрош
            if ($payment_system
                && $pm_method->payment_class == SystemSettingsWrapperJoomshopping::getPaymentCode()) {
                $pmconfigs = $pm_method->getConfigs();
                $payment_system->complete($pmconfigs, $jorder, $pm_method);
            }
        } catch (Throwable $e) {
            HgLogger::getLogger("complete")->error("Exception: ", $e);
        }
    }
}