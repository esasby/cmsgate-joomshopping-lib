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

class ConfigFormsJoomshopping
{
    /**
     * @var ConfigFormJoomshopping[]
     */
    private $configFormsArray;

    /**
     * ConfigFormsJoomshopping constructor.
     * @param ConfigFormJoomshopping[] $configFormsArray
     */
    public function __construct(array $configFormsArray = null)
    {
        $this->configFormsArray = $configFormsArray;
    }

    /**
     * @param $configForm
     * @return $this
     */
    public function addForm($configForm) {
        $this->configFormsArray[] = $configForm;
        return $this;
    }

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
        $ret = JHtml::_('bootstrap.startTabSet', self::tabsName(), array('active' => 'kassa-tab')); //fixme
        foreach ($this->configFormsArray as $configForm) {
            $ret .= $configForm->generate();
        }
        $ret .= JHtml::_('bootstrap.endTabSet');
        return $ret;
    }

    public static function tabsName() {
        return Registry::getRegistry()->getPaySystemName() . 'Tab';
    }
}