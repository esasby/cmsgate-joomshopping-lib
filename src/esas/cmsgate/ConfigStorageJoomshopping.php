<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 15.07.2019
 * Time: 13:14
 */

namespace esas\cmsgate;

use esas\cmsgate\wrappers\SystemSettingsWrapperJoomshopping;
use Exception;
use JSFactory;
use parseString;

class ConfigStorageJoomshopping extends ConfigStorageCms
{
    private $settings;
    private $pm_method;

    /**
     * ConfigurationWrapperOpencart constructor.
     * @param $config
     */
    public function __construct()
    {
        parent::__construct();
        $this->pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $this->pm_method->loadFromClass(SystemSettingsWrapperJoomshopping::getPaymentCode());
        $dbSettings = $this->pm_method->getConfigs();
        if (is_array($dbSettings))
            $this->settings = $dbSettings;
        else // если это превая конфигурация модуля, то в БД будет пусто
            $this->settings = array();
    }


    /**
     * @param $key
     * @return string
     * @throws Exception
     */
    public function getConfig($key)
    {
        if (array_key_exists($key, $this->settings))
            return $this->settings[$key];
        else
            return "";
    }

    /**
     * @param $cmsConfigValue
     * @return bool
     * @throws Exception
     */
    public function convertToBoolean($cmsConfigValue)
    {
        return $cmsConfigValue == '1' || $cmsConfigValue == "true";
    }

    /**
     * Сохранение значения свойства в харнилища настроек конкретной CMS.
     *
     * @param string $key
     * @throws Exception
     */
    public function saveConfig($key, $value)
    {
        $this->settings[$key] = $value;
        $parseString = new parseString($this->settings);
        $this->pm_method->payment_params = $parseString->splitParamsToString();
        $this->pm_method->store();
    }
}