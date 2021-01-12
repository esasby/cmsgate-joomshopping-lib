<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 13.04.2020
 * Time: 12:23
 */

namespace esas\cmsgate;
if (!class_exists( 'JSFactory' )) require_once(PATH_JSHOPPING . 'lib/factory.php'); // для использование JSFactory

use esas\cmsgate\descriptors\CmsConnectorDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\joomshopping\CmsgateModelJoomshopping;
use esas\cmsgate\lang\LocaleLoaderJoomshopping;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigFormJoomshopping;
use esas\cmsgate\wrappers\OrderWrapper;
use esas\cmsgate\wrappers\OrderWrapperJoomshopping;
use Joomla\CMS\Uri\Uri;
use \JSFactory;

class CmsConnectorJoomshopping extends CmsConnectorJoomla
{

    /**
     * @var CmsgateModelJoomshopping
     */
    private $moduleModel;

    /**
     * @return CmsgateModelJoomshopping
     */
    public function getModuleModel()
    {
        if ($this->moduleModel == null) //вынесено из конструктора, т.к. в нем нельзя обращаться к Registry
            $this->moduleModel = JSFactory::getModel(Registry::getRegistry()->getPaySystemName());
        return $this->moduleModel;
    }

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
        return null;
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
        $orderId = $this->getModuleModel()->getCurrentOrderId();
        return $this->createOrderWrapperByOrderId($orderId);
    }

    public function createOrderWrapperByOrderNumber($orderNumber)
    {
        $orderId = $this->getModuleModel()->getOrderIdByOrderNumber($orderNumber);
        return $this->createOrderWrapperByOrderId($orderId);
    }
    
    public function createOrderWrapperByExtId($extId)
    {
        $orderId = $this->getModuleModel()->getOrderIdByTrxId($extId);
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

    public function getConstantConfigValue($key)
    {
        switch ($key) {
            case ConfigFields::useOrderNumber():
                return true;
            default:
                return parent::getConstantConfigValue($key);
        }
    }

    public static function getPaymentCode() {
        return 'pm_' . Registry::getRegistry()->getPaySystemName();
    }

    public static function generateControllerPath($controller, $task)
    {
        return "index.php?option=com_jshopping&controller=" . $controller . "&task=" . $task;
    }

    public static function generatePaySystemControllerPath($task)
    {
        return self::generateControllerPath(Registry::getRegistry()->getPaySystemName(), $task);
    }

    public static function generateControllerUrl($controller, $task)
    {
        return Uri::root() . self::generateControllerPath($controller, $task);
    }

    public static function generatePaySystemControllerUrl($task)
    {
        return Uri::root() . self::generatePaySystemControllerPath($task);
    }

    public function createCmsConnectorDescriptor()
    {
        return new CmsConnectorDescriptor(
            "cmsgate-joomshopping-lib",
            new VersionDescriptor(
                "v1.15.0",
                "2021-01-12"
            ),
            "Cmsgate Joomshopping connector",
            "https://bitbucket.esas.by/projects/CG/repos/cmsgate-joomshopping-lib/browse",
            VendorDescriptor::esas(),
            "joomshopping",
            "plugin"
        );
    }
}