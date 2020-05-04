<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.09.2018
 * Time: 13:09
 */

namespace esas\cmsgate\lang;

use Joomla\CMS\Factory;

class LocaleLoaderJoomshopping extends LocaleLoaderCms
{
    public function getLocale()
    {
        $cmsLocale = Factory::getLanguage()->getTag();
        return str_replace("-", "_", $cmsLocale);
    }


    public function getCmsVocabularyDir()
    {
        return dirname(__FILE__);
    }
}