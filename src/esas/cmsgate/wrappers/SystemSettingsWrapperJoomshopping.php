<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 21.02.2020
 * Time: 15:01
 */

namespace esas\cmsgate\wrappers;


use esas\cmsgate\Registry;
use esas\cmsgate\utils\OpencartVersion;

class SystemSettingsWrapperJoomshopping extends SystemSettingsWrapper
{
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
}