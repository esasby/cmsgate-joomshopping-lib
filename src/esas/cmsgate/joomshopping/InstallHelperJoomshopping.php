<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 24.04.2020
 * Time: 10:54
 */

namespace esas\cmsgate\joomshopping;

use esas\cmsgate\ConfigFields;
use esas\cmsgate\joomla\InstallHelperJoomla;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\SystemSettingsWrapperJoomshopping;
use Exception;
use JFactory;
use JFolder;
use JFile;
use Joomla\CMS\Factory;
use stdClass;
use JSFactory;

class InstallHelperJoomshopping extends InstallHelperJoomla
{
    /**
     * @throws Exception
     */
    public static function dbPaymentMethodAdd()
    {
        $payment_code = SystemSettingsWrapperJoomshopping::getPaymentCode();
        $db = Factory::getDBO();
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

    public static function dbPaymentMethodDelete()
    {
        $db = Factory::getDbo();
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
        $ret1 = self::deleteWithLogging(PATH_JSHOPPING . 'models/' . Registry::getRegistry()->getPaySystemName());
        $ret2 = self::deleteWithLogging(PATH_JSHOPPING . 'controllers/' . Registry::getRegistry()->getPaySystemName());
        $ret3 = self::deleteWithLogging(PATH_JSHOPPING . 'payments/' . SystemSettingsWrapperJoomshopping::getPaymentCode());
        $ret4 = self::deleteWithLogging(PATH_JSHOPPING_ADMINISTRATOR . 'models/' . Registry::getRegistry()->getPaySystemName());
        $ret5 = self::deleteWithLogging(PATH_JSHOPPING_ADMINISTRATOR . 'controllers/' . Registry::getRegistry()->getPaySystemName());
        return $ret1 && $ret2 && $ret3 && $ret4 && $ret5;
    }

    public static function dbCompletionTextAdd($configField)
    {
        $staticText = new stdClass();
        $staticText->alias = $configField;
        $staticText->use_for_return_policy = 0;
        $jshoppingLanguages = JSFactory::getTable('language', 'jshop');
        foreach ($jshoppingLanguages::getAllLanguages() as $lang) {
            $i18nField = 'text_' . $lang->language;
            $staticText->$i18nField = Registry::getRegistry()->getTranslator()->getConfigFieldDefault($configField, $lang->language);
        }
        return Factory::getDbo()->insertObject('#__jshopping_config_statictext', $staticText);
    }

    public static function dbCompletionTextDelete($configField)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('alias') . ' = ' . $db->quote($configField)
        );
        $query->delete($db->quoteName('#__jshopping_config_statictext'));
        $query->where($conditions);

        $db->setQuery($query);
        return $db->execute();
    }
}