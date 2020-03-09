<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/

namespace esas\cmsgate\view\admin;

use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use esas\cmsgate\view\admin\fields\ConfigField;
use esas\cmsgate\view\admin\fields\ConfigFieldCheckbox;
use esas\cmsgate\view\admin\fields\ConfigFieldFile;
use esas\cmsgate\view\admin\fields\ConfigFieldList;
use esas\cmsgate\view\admin\fields\ConfigFieldPassword;
use esas\cmsgate\view\admin\fields\ConfigFieldTextarea;
use esas\cmsgate\view\admin\fields\ListOption;
use \JHTML;

defined('_JEXEC') or die();

class ConfigFormJoomshopping2x extends ConfigFormHtml
{
    private $orderStatuses;

    /**
     * ConfigFormJoomshopping constructor.
     */
    public function __construct($managedFields, $formKey, $submitUrl, $submitButtons)
    {
        parent::__construct($managedFields, $formKey, $submitUrl, $submitButtons);
        $orders = \JModelLegacy::getInstance('orders', 'JshoppingModel');
        foreach ($orders->getAllOrderStatus() as $orderStatus) {
            $this->orderStatuses[] = new ListOption($orderStatus->status_id, $orderStatus->name);
        }
    }

    public function generate()
    {
        return
            element::div(
                attribute::clazz('col100'),
                element::fieldset(
                    attribute::clazz('adminform'),
                    element::table(
                        attribute::clazz("admintable"),
                        attribute::width('100%'),
                        element::content(parent::generate())
                    )
                )
            ) .
            element::div(
                attribute::clazz('clr')
            );
    }

    function generateTextField(ConfigField $configField)
    {
        return
            self::elementTr(
                $configField,
                self::elementInput($configField, "text")
            );
    }

    public function generatePasswordField(ConfigFieldPassword $configField)
    {
        return
            self::elementTr(
                $configField,
                self::elementInput($configField, "password")
            );
    }

    function generateCheckboxField(ConfigFieldCheckbox $configField)
    {
        return
            self::elementTr(
                $configField,
                element::input(
                    attribute::clazz("inputbox"),
                    self::attributeInputName($configField),
                    attribute::type("checkbox"),
                    attribute::placeholder($configField->getName()),
                    attribute::checked($configField->isChecked()),
                    attribute::value("1")
                )
            );
    }

    private static function elementInput(ConfigField $configField, $type)
    {
        return
            element::input(
                attribute::clazz("inputbox"),
                self::attributeInputName($configField),
//                attribute::id($configField->getKey()),
                attribute::type($type),
                attribute::placeholder($configField->getName()),
                attribute::value($configField->getValue())
            );
    }

    private static function elementTr(ConfigField $configField, $thContent)
    {
        return
            element::tr(
                self::elementLabel($configField),
                element::td(
                    $thContent
                )
            ) .
            self::elementValidationError($configField);
    }

    private static function attributeInputName(ConfigField $configField)
    {
        return attribute::name("pm_params[" . $configField->getKey() . "]");
    }

    private static function elementValidationError(ConfigField $configField)
    {
        $validationResult = $configField->getValidationResult();
        if ($validationResult != null && !$validationResult->isValid())
            $td =
                element::td(
                    attribute::clazz("alert alert-danger"),
                    element::content($validationResult->getErrorTextSimple())
                );
        else
            $td = "";
        return
            element::tr(
                element::td(),
                $td
            );
    }

    private static function elementLabel(ConfigField $configField)
    {
        return
            element::td(
                attribute::clazz('key'),
                attribute::width('300'),
                attribute::title($configField->getDescription()),
                element::content($configField->getName())
            );
    }

    function generateTextAreaField(ConfigFieldTextarea $configField)
    {
        $editor = \JFactory::getEditor();
        return self::elementTr(
            $configField,
            $editor->display(self::attributeInputName($configField), $configField->getValue(), '100%', '350', '75', '20')
        );
    }

    public function generateFileField(ConfigFieldFile $configField)
    {
        return
            self::elementTr(
                $configField,
                element::input(
                    element::input(
                        attribute::type("file"),
                        attribute::name($configField->getKey())
                    )
                ) .
                element::p(
                    element::font(
                        attribute::color("green"),
                        element::content($configField->getValue())
                    )
                )
            );
    }


    function generateListField(ConfigFieldList $configField)
    {
        return
            self::elementTr(
                $configField,
                element::select(
                    attribute::clazz("inputbox"),
                    attribute::name(self::attributeInputName($configField)),
                    attribute::id("input-" . $configField->getKey()),
                    parent::elementOptions($configField)
//                   . JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[' . $configField->getKey() . ']', 'class="inputbox" size="1"', 'status_id', 'name', $configField->getValue())
                )
            );
    }

    /**
     * @return ListOption[]
     */
    public function createStatusListOptions()
    {
        return $this->orderStatuses;
    }
}