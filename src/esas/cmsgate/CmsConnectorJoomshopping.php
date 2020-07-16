<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 13.04.2020
 * Time: 12:23
 */

namespace esas\cmsgate;


use esas\cmsgate\descriptors\CmsConnectorDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\lang\LocaleLoaderJoomshopping;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormJoomshopping;
use esas\cmsgate\wrappers\OrderWrapper;
use esas\cmsgate\wrappers\OrderWrapperJoomshopping;
use esas\cmsgate\wrappers\SystemSettingsWrapperJoomshopping;
use \JSFactory;

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
        $orderId = JSFactory::getModel(Registry::getRegistry()->getPaySystemName())->getOrderIdByOrderNumber($orderNumber);
        return $this->createOrderWrapperByOrderId($orderId);
    }
    
    public function createOrderWrapperByExtId($extId)
    {
        $orderId = JSFactory::getModel(Registry::getRegistry()->getPaySystemName())->getOrderIdByTrxId($extId);
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

    public function createCmsConnectorDescriptor()
    {
        return new CmsConnectorDescriptor(
            "cmsgate-joomshopping-lib",
            new VersionDescriptor(
                "v1.11.0",
                "2020-06-05"
            ),
            "Cmsgate Joomshopping connector",
            "https://bitbucket.esas.by/projects/CG/repos/cmsgate-joomshopping-lib/browse",
            VendorDescriptor::esas(),
            "joomshopping"
        );
    }
}