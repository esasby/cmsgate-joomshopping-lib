<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 24.04.2020
 * Time: 10:54
 */

namespace esas\cmsgate\joomshopping;

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/init.php');

use esas\cmsgate\ConfigFields;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\SystemSettingsWrapperJoomshopping;
use Exception;

class InstallUtilsJoomshopping
{
    /**
     * @param $paySystemName
     * @throws Exception
     */
    public static function preInstall($paySystemName) {
        //вручную копируем файлы из временной папки, в папку components, иначе не сработают require_once
        $pmPath = JPATH_SITE . '/plugins/jshopping/' . $paySystemName . '/components';
        $newPath = JPATH_SITE . '/components';
        if (!JFolder::copy($pmPath, $newPath, "", true)) {
            throw new Exception('Can not copy folder from[' . $pmPath . '] to [' . $newPath . ']');
        }
        self::req($paySystemName);
    }

    public static function req($paySystemName)
    {
        require_once(PATH_JSHOPPING . 'lib/factory.php');
        require_once(PATH_JSHOPPING . 'payments/pm_' . $paySystemName . '/init.php');
    }

    /**
     * @throws Exception
     */
    public static function dbAddPaymentMethod()
    {
        $payment_code = SystemSettingsWrapperJoomshopping::getPaymentCode();
        $db = JFactory::getDBO();
        $query = "SELECT * FROM `#__jshopping_payment_method` WHERE payment_code = '" . $payment_code . "'";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 0)
            return;
        $paymentMethod = new stdClass();
        $paymentMethod->payment_code = $payment_code;
        $paymentMethod->scriptname = $payment_code;
        $paymentMethod->payment_class = $payment_code;
        $paymentMethod->payment_publish = 1;
        $paymentMethod->payment_ordering = 0;
        $paymentMethod->payment_params = '';
        $paymentMethod->payment_type = 2;
        $paymentMethod->price = 0.00;
        $paymentMethod->price_type = 0;
        $paymentMethod->tax_id = 1;
        $paymentMethod->image = ''; //todo
        $paymentMethod->show_descr_in_email = 0;
        $paymentMethod->show_bank_in_order = 1;
        $paymentMethod->order_description = '';
        $paymentMethod->order_description = '';
        // без этих полей не добавляется запись в БД
        $jshoppingLanguages = JSFactory::getTable('language', 'jshop');
        foreach ($jshoppingLanguages::getAllLanguages() as $lang) {
            $i18nField = 'name_' . $lang->language;
            $paymentMethod->$i18nField = Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigFields::paymentMethodName(), $lang->language);
            $i18nField = 'description_' . $lang->language;
            $paymentMethod->$i18nField = Registry::getRegistry()->getTranslator()->getConfigFieldDefault(ConfigFields::paymentMethodDetails(), $lang->language);
        }
        if (!JFactory::getDbo()->insertObject('#__jshopping_payment_method', $paymentMethod))
            throw new Exception('Can not add new payment method');
    }

    public static function dbActivatePlugin()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update('#__extensions');
        $query->set($db->quoteName('enabled') . ' = 1');
        $query->where($db->quoteName('element') . ' = ' . $db->quote(Registry::getRegistry()->getPaySystemName()));
        $query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
        $db->setQuery($query);
        if ($db->execute())
            throw new Exception('Can not activate plugin');

    }

    public static function dbDeletePaymentMethod()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('payment_code') . ' = ' . $db->quote(SystemSettingsWrapperJoomshopping::getPaymentCode())
        );
        $query->delete($db->quoteName('#__jshopping_payment_method'));
        $query->where($conditions);

        $db->setQuery($query);
        return $db->execute();
    }

    public static function deleteFiles() {
        $ret = true;
        $ret = $ret && self::deleteWithLogging(PATH_JSHOPPING . 'models/' . Registry::getRegistry()->getPaySystemName());
        $ret = $ret && self::deleteWithLogging(PATH_JSHOPPING . 'controllers/' . Registry::getRegistry()->getPaySystemName());
        $ret = $ret && self::deleteWithLogging(PATH_JSHOPPING . 'payments/' . SystemSettingsWrapperJoomshopping::getPaymentCode());
        return $ret;
    }


    public static function deleteWithLogging($file)
    {
        $result = true;
        if (is_dir($file)) {
            JFolder::delete($file);
            $deleted = !JFolder::exists($file);
        } else
            $deleted = JFile::delete($file);
        if (!$deleted) {
            $result = false;
            echo JText::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $file) . '<br />';;
        }
        return $result;
    }

}