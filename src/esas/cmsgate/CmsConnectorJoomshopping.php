<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 13.04.2020
 * Time: 12:23
 */

namespace esas\cmsgate;


use esas\cmsgate\lang\LocaleLoaderJoomshopping;
use esas\cmsgate\opencart\ModelExtensionPayment;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\AdminViewFieldsOpencart;
use esas\cmsgate\view\admin\ConfigFormJoomshopping;
use esas\cmsgate\view\admin\ConfigFormOpencart;
use esas\cmsgate\wrappers\OrderWrapper;
use esas\cmsgate\wrappers\OrderWrapperJoomshopping;
use esas\cmsgate\wrappers\SystemSettingsWrapperJoomshopping;
use esas\cmsgate\wrappers\SystemSettingsWrapperOpencart;

class CmsConnectorJoomshopping extends CmsConnector
{
    public function createCommonConfigForm($managedFields)
    {
        $configForm = new ConfigFormJoomshopping(
            $managedFields,
            AdminViewFields::CONFIG_FORM_COMMON,
            null,
            null);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_SAVE);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_DOWNLOAD_LOG);
        $configForm->addSubmitButton(AdminViewFields::CONFIG_FORM_BUTTON_CANCEL);
        $configForm->addCmsManagedFields();
        return $configForm;
    }

    public function createSystemSettingsWrapper()
    {
        return new SystemSettingsWrapperJoomshopping();
    }
    
    /**
     * По локальному id заказа возвращает wrapper
     * @param $orderId
     * @return OrderWrapper
     */
    public function createOrderWrapperByOrderId($orderId)
    {
        return new OrderWrapperJoomshopping($orderId);
    }
    
    public function createOrderWrapperForCurrentUser()
    {
        $orderId = JSFactory::getModel(Registry::getRegistry()->getPaySystemName())->getCurrentOrderId();
        return $this->createOrderWrapperByOrderId($orderId);
    }

    public function createOrderWrapperByOrderNumber($orderNumber)
    {
        $orderId = JSFactory::getModel(Registry::getRegistry()->getPaySystemName())->getOrderByOrderNumber();
        return $this->createOrderWrapperByOrderId($orderId);
    }
    
    public function createOrderWrapperByExtId($extId)
    {
        $orderId = JSFactory::getModel(Registry::getRegistry()->getPaySystemName())->getOrderByTrxId();
        return $this->createOrderWrapperByOrderId($orderId);
    }

    public function createConfigStorage()
    {
        return new ConfigStorageJoomshopping();
    }

    public function createLocaleLoader()
    {
        return new LocaleLoaderJoomshopping();
    }
}