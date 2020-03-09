<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 06.03.2020
 * Time: 10:51
 */

namespace esas\cmsgate\view\admin;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use \JHTML;

class ConfigPageJoomshopping extends ConfigPage
{
       public function generate()
    {
        return
            element::div(
                attribute::clazz("col100"),
                element::fieldset(
                    attribute::clazz('adminform'),
                    $this->generateTabs()
                )
            ) .
            element::div(
                attribute::clazz('clr')
            );
    }

    public function generateTabs()
    {
        if (count($this->configFormsArray) == 1)
            return $this->configFormsArray[0]->generate();
        $ret = JHTML::_('bootstrap.startTabSet', self::tabsName(), array('active' => $this->configFormsArray[0]->getFormKey()));
        foreach ($this->configFormsArray as $configForm) {
            $ret .= $configForm->generate();
        }
        $ret .= JHTML::_('bootstrap.endTabSet');
        return $ret;
    }

    public static function tabsName() {
        return Registry::getRegistry()->getPaySystemName() . 'Tab';
    }
}