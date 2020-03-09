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
use esas\cmsgate\view\admin\fields\ConfigFieldRichtext;
use esas\cmsgate\view\admin\fields\ConfigFieldTextarea;
use esas\cmsgate\view\admin\fields\ListOption;
use JHTML;

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
            JHTML::_('bootstrap.addTab', ConfigPageJoomshopping::tabsName(), $this->formKey, $this->headingTitle) .
            element::div(
                attribute::clazz('col100'),
                element::fieldset(
                    attribute::clazz('adminform'),
                    element::table(
                        attribute::clazz("admintable"),
                        attribute::width('100%'),
                        element::content(parent::generate())
                    )
                ) .
                element::br(),
                $this->elementSubmitButtons()
            ) .
            element::div(
                attribute::clazz('clr')
            ) .
            JHTML::_('bootstrap.endTab');
    }


    function generateTextField(ConfigField $configField)
    {
        return
            $this->elementRow(
                $configField,
                $this->elementInput($configField, "text")
            );
    }

    public function generatePasswordField(ConfigFieldPassword $configField)
    {
        return
            $this->elementRow(
                $configField,
                $this->elementInput($configField, "password")
            );
    }

    function generateCheckboxField(ConfigFieldCheckbox $configField)
    {
        return
            $this->elementRow(
                $configField,
                element::input(
                    attribute::clazz("form-control"),
                    $this->attributeInputName($configField),
                    attribute::type("checkbox"),
                    attribute::checked($configField->isChecked()),
                    attribute::value("1")
                )
            );
    }

    private function elementInput(ConfigField $configField, $type)
    {
        return
            element::input(
                attribute::clazz("form-control"),
                $this->attributeInputName($configField),
                $this->attributeInputId($configField),
                attribute::type($type),
                attribute::value($configField->getValue())
            );
    }

    private function elementRow(ConfigField $configField, $thContent)
    {
        return
            element::div(
                attribute::clazz("row"),
                element::div(
                    attribute::clazz("span11 offset1"),
                    element::div(
                        attribute::clazz("form-group"),
                        $this->elementLabel($configField),
                        element::div(
                            attribute::clazz('span8'),
                            $thContent,
                            element::p(
                                attribute::clazz("help-block"),
                                element::content($configField->getDescription())
                            ),
                            $this->elementValidationError($configField)
                        )
                    )
                )
            );
    }


    private function attributeInputName(ConfigField $configField)
    {
        // в joomshoppingв в аттрибуте name принято использовать pm_params[имя переменной], тогда срабатывает встроенный
        // механизм сохранения настроекн, но в случае cmsgate сохранение настроек выполняется средствами ConfigWrapper-а
        // (в том числе для валидации и отображения ошибок), поэтому pm_params не используется
        return attribute::name($this->formKey . "[" . $configField->getKey() . "]");
    }

    private function attributeInputId(ConfigField $configField)
    {
        return attribute::id($this->createId($configField));
    }

    private function createId(ConfigField $configField)
    {
        return $this->formKey . "-" . $configField->getKey();
    }

    private function elementValidationError(ConfigField $configField)
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


    private function elementLabel(ConfigField $configField)
    {
        return
            element::div(
                attribute::clazz('span2'),
                element::label(
                    attribute::forr($this->createId($configField)),
                    element::span( //fixme почему-то не работает
                        attribute::clazz("hasTooltip"),
                        attribute::data("original-title", $configField->getDescription()),
                        element::content($configField->getName())
                    )
                )
            );
    }

    function generateTextAreaField(ConfigFieldTextarea $configField)
    {
        return $this->elementRow(
            $configField,
            element::textarea(
                attribute::rows("3"),
                attribute::cols("20"),
                attribute::clazz("form-control"),
                attribute::type("textarea"),
                $this->attributeInputName($configField),
                $this->attributeInputId($configField),
//                attribute::style("max-width:80%;"),
                element::content($configField->getValue())
            )
        );
    }

    function generateRichtextField(ConfigFieldRichtext $configField)
    {
//        $editor = \JFactory::getEditor();
//        return $this->elementRow(
//            $configField,
//            $editor->display($this->attributeInputName($configField), $configField->getValue(), '100%', '350', '75', '20')
//        );
        return $this->generateTextAreaField($configField);
    }

    public function generateFileField(ConfigFieldFile $configField)
    {
        return
            $this->elementRow(
                $configField,
                element::input(
                    attribute::type("file"),
                    attribute::clazz("form-control"),
                    attribute::name($configField->getKey())
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
            $this->elementRow(
                $configField,
                element::select(
                    attribute::clazz("inputbox"),
                    $this->attributeInputName($configField),
                    $this->attributeInputId($configField),
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

    protected function elementSubmitButtons()
    {
        return
            element::div(
                attribute::clazz("row"),
                element::div(
                    attribute::clazz("span11 offset1"),
                    element::div(
                        attribute::clazz("form-group"),
                        element::div(
                            attribute::clazz('span2')
                        ),
                        element::div(
                            attribute::clazz('span8'),
                            parent::elementSubmitButtons()
                        )
                    )
                )
            );
    }

    protected function elementInputSubmit($name, $value)
    {
        return
            element::input(
                attribute::type("submit"),
                attribute::clazz("btn btn-small"),
                attribute::onclick("Joomla.submitbutton('apply');"),
                attribute::name($name),
                attribute::value($value)
            );
    }

}