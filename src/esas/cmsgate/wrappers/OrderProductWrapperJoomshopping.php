<?php
/*
* @info     Платёжный модуль Hutkigrosh для JoomShopping
* @package  hutkigrosh
* @author   esas.by
* @license  GNU/GPL
*/

namespace esas\cmsgate\wrappers;

class OrderProductWrapperJoomshopping extends OrderProductWrapper
{
    private $product;

    /**
     * OrderProductWrapperJoomshopping constructor.
     * @param $product
     */
    public function __construct($product)
    {
        $this->product = $product;
    }

    /**
     * Артикул товара
     * @return string
     */
    public function getInvId()
    {
        return $this->product->product_ean; //  TODO
    }

    /**
     * Название или краткое описание товара
     * @return string
     */
    public function getName()
    {
        return $this->product->product_name;
    }

    /**
     * Количество товароа в корзине
     * @return mixed
     */
    public function getCount()
    {
        return round($this->product->product_quantity);
    }

    /**
     * Цена за единицу товара
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->product->product_item_price;
    }
}