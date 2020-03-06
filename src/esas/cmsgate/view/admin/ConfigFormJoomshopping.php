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

defined('_JEXEC') or die();

class ConfigFormJoomshopping extends ConfigFormHtml
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
            JHtml::_('bootstrap.addTab', ConfigFormsJoomshopping::tabsName(), $this->formKey, $this->headingTitle) .
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
            ) .
            JHtml::_('bootstrap.endTab');
    }



    function generateTextField(ConfigField $configField)
    {
        return
            self::elementRow(
                $configField,
                self::elementInput($configField, "text")
            );
    }

    public function generatePasswordField(ConfigFieldPassword $configField)
    {
        return
            self::elementRow(
                $configField,
                self::elementInput($configField, "password")
            );
    }

    function generateCheckboxField(ConfigFieldCheckbox $configField)
    {
        return
            self::elementRow(
                $configField,
                element::input(
                    attribute::clazz("form-control"),
                    self::attributeInputName($configField),
                    attribute::type("checkbox"),
                    attribute::checked($configField->isChecked()),
                    attribute::value($configField->getValue())
                )
            );
    }

    private static function elementInput(ConfigField $configField, $type)
    {
        return
            element::input(
                attribute::clazz("form-control"),
                self::attributeInputName($configField),
                self::attributeInputId($configField),
                attribute::type($type),
                attribute::value($configField->getValue())
            );
    }

    private static function elementRow(ConfigField $configField, $thContent)
    {
        return
            element::div(
                attribute::clazz("row"),
                element::div(
                    attribute::clazz("span11 offset1"),
                    element::div(
                        attribute::clazz("form-group"),
                        self::elementLabel($configField),
                        element::div(
                            attribute::clazz('span8'),
                            $thContent,
                            element::p(
                                attribute::clazz("help-block"),
                                element::content($configField->getDescription())
                            ),
                            self::elementValidationError($configField)
                        )
                    )
                )
            );
    }

    private static function attributeInputName(ConfigField $configField)
    {
        return attribute::name("pm_params[" . $configField->getKey() . "]");
    }

    private static function attributeInputId(ConfigField $configField)
    {
        return attribute::id(self::createId($configField));
    }

    private static function createId(ConfigField $configField) {
        return "pm_params-" . $configField->getKey();
    }

    private static function elementValidationError(ConfigField $configField)
    {
        $validationResult = $configField->getValidationResult();
        if ($validationResult != null && !$validationResult->isValid())
            return
                element::font(
                    attribute::color("red"),
                    element::content($validationResult->getErrorTextSimple())
                );
        else
            return "";
    }

    private static function elementLabel(ConfigField $configField)
    {
        return
            element::div(
                attribute::clazz('span2'),
                element::label(
                    attribute::forr(self::createId($configField)),
                    element::content($configField->getName())
                )
            );
    }

    function generateTextAreaField(ConfigFieldTextarea $configField)
    {
        $editor = \JFactory::getEditor();
        return self::elementRow(
            $configField,
            $editor->display(self::attributeInputName($configField), $configField->getValue(), '100%', '350', '75', '20')
        );
    }

    public function generateFileField(ConfigFieldFile $configField)
    {
        return
            self::elementRow(
                $configField,
                element::input(
                    element::input(
                        attribute::type("file"),
                        attribute::clazz("form-control"),
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
            self::elementRow(
                $configField,
                element::select(
                    attribute::clazz("inputbox"),
                    attribute::name(self::attributeInputName($configField)),
                    self::attributeInputId($configField),
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