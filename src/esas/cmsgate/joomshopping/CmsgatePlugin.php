<?php
namespace esas\cmsgate\joomshopping;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\FileUtils;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\utils\SessionUtils;
use esas\cmsgate\view\admin\AdminViewFields;
use esas\cmsgate\view\admin\ConfigForm;
use Joomla\CMS\Factory;
use JPlugin;
use Throwable;

defined('_JEXEC') or die;

class CmsgatePlugin extends JPlugin
{
    /**
     * @param ConfigForm $configForm
     * @throws Throwable
     * @throws \Throwable
     */
    protected function saveOrRedirect($configForm)
    {
        if (is_array($_REQUEST[$configForm->getFormKey()]) && !$configForm->isValid($_REQUEST[$configForm->getFormKey()])) {
            //если в форме ошибки, то сохраняем ее в сессии и делаем редирект на страницу редактирования
            SessionUtils::storeForm($configForm);
            Factory::getApplication()->redirect($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] . '&task=edit&payment_id=' . $_REQUEST["payment_id"]);
            exit;
        } else {
            SessionUtils::removeForm($configForm);
            $configForm->save();
        }
    }

    /**
     * Метод обеспечивает валидацию и сохранение параметров платежного метода
     * Срабатывает только в случае, если плагин активен в joomla
     * @param $post
     * @throws \Throwable
     */
    public function onBeforeSavePayment(&$post)
    {
        try {
            if (isset($_REQUEST[AdminViewFields::CONFIG_FORM_BUTTON_DOWNLOAD_LOG])) {
                FileUtils::downloadByPath(Logger::getLogFilePath());
            } else {
                $configForm = Registry::getRegistry()->getConfigForm();
                $this->saveOrRedirect($configForm);
                //если ни одна из внутренних кнопок не была нажата, очищаем сессию,
                //чтобы корректно отработала основная кнопка "save"
//                SessionUtils::removeAllForms();
            }
        } catch (Throwable $e) {
            Logger::getLogger("admin")->error("Exception: ", $e);
        }
    }
}