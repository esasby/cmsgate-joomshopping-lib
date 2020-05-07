<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 15.07.2019
 * Time: 13:14
 */

namespace esas\cmsgate;

use esas\cmsgate\view\admin\fields\ConfigFieldRichtext;
use esas\cmsgate\view\admin\fields\ConfigFieldTextarea;
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
        $configField = Registry::getRegistry()->getManagedFieldsFactory()->getFieldByKey($key);
        if ($configField instanceof ConfigFieldTextarea || $configField instanceof ConfigFieldRichtext) {
            $statictext = JSFactory::getTable("statictext", "jshop");
            $rowstatictext = $statictext->loadData(self::staticAliasName($key));
            return $rowstatictext->text;
        } else {
            if (array_key_exists($key, $this->settings))
                return $this->settings[$key];
            else
                return "";
        }
    }

    private static function staticAliasName($key)
    {
        return Registry::getRegistry()->getPaySystemName() . '_' . $key;
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

    public function saveConfig($key, $value)
    {
        $configFieldType = Registry::getRegistry()->getManagedFieldsFactory()->getFieldByKey($key);
        if ($configFieldType instanceof ConfigFieldTextarea || $configFieldType instanceof ConfigFieldRichtext) {
            $languagesModel = JSFactory::getModel("languages");
            foreach ($languagesModel->getAllLanguages(1) as $lang) {
                //todo HGCMS-13
                $bind["text_" . $lang->language] = $value;
            }
            $statictext = JSFactory::getTable("statictext", "jshop");
            $statictext->load(["alias" => self::staticAliasName($key)]);
            if ($statictext->id == null) // на случай, если в БД еще нет такой записи
                $bind["alias"] = self::staticAliasName($key);
            $statictext->bind($bind);
            $statictext->store();
        } else {
            $this->settings[$key] = $value;
            $parseString = new parseString($this->settings);
            $this->pm_method->payment_params = $parseString->splitParamsToString();
            $this->pm_method->store();
        }
    }
    }