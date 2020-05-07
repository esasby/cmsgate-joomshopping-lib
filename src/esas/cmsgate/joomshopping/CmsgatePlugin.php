<?php
namespace esas\cmsgate\joomshopping;

use esas\cmsgate\utils\SessionUtils;
use esas\cmsgate\view\admin\ConfigFormJoomshopping;
use JPlugin;
use JFactory;

defined('_JEXEC') or die;

class CmsgatePlugin extends JPlugin
{
    /**
     * @param ConfigFormJoomshopping $configForm
     * @throws Throwable
     * @throws \Throwable
     */
    protected function saveOrRedirect($configForm)
    {
        if (is_array($_REQUEST[$configForm->getFormKey()]) && !$configForm->isValid($_REQUEST[$configForm->getFormKey()])) {
            //если в форме ошибки, то сохраняем ее в сессии и делаем редирект на страницу редактирования
            SessionUtils::storeForm($configForm);
            JFactory::getApplication()->redirect($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&task=edit&payment_id=' . $_REQUEST["payment_id"]);
            exit;
        } else {
            SessionUtils::removeForm($configForm);
            $configForm->save();
        }
    }
}