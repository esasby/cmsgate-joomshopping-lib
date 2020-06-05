<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.09.2018
 * Time: 13:08
 */

namespace esas\cmsgate\wrappers;

use JSFactory;
use Throwable;
use \JFactory;

class OrderWrapperJoomshopping extends OrderSafeWrapper
{
    private $order;

    /**
     * OrderWrapperWoo constructor.
     */
    public function __construct($order_id)
    {
        parent::__construct();
        $this->order = JSFactory::getTable('order', 'jshop');
        $this->order->load($order_id);
    }

    /**
     * @return mixed
     */
    public function getJOrder()
    {
        return $this->order;
    }
  

    /**
     * Уникальный номер заказ в рамках CMS
     * @return string
     * @throws Throwable
     */
    public function getOrderIdUnsafe()
    {
        return $this->order->order_id;
    }


    public function getOrderNumberUnsafe()
    {
        return $this->order->order_number;
    }

    /**
     * Полное имя покупателя
     * @throws Throwable
     * @return string
     */
    public function getFullNameUnsafe()
    {
        return $this->order->f_name . ' ' . $this->order->l_name;
    }

    /**
     * Мобильный номер покупателя для sms-оповещения
     * (если включено администратором)
     * @throws Throwable
     * @return string
     */
    public function getMobilePhoneUnsafe()
    {
        return $this->order->phone;
    }

    /**
     * Email покупателя для email-оповещения
     * (если включено администратором)
     * @throws Throwable
     * @return string
     */
    public function getEmailUnsafe()
    {
        return $this->order->email;
    }

    /**
     * Физический адрес покупателя
     * @throws Throwable
     * @return string
     */
    public function getAddressUnsafe()
    {
        return $this->order->city . ' ' .
            $this->order->state . ' ' .
            $this->order->street;
    }

    /**
     * Общая сумма товаров в заказе
     * @throws Throwable
     * @return string
     */
    public function getAmountUnsafe()
    {
        return $this->order->order_total;
    }

    /**
     * Валюта заказа (буквенный код)
     * @throws Throwable
     * @return string
     */
    public function getCurrencyUnsafe()
    {
        return $this->order->currency_code;
    }

    /**
     * Массив товаров в заказе
     * @throws Throwable
     * @return OrderProductWrapper[]
     */
    public function getProductsUnsafe()
    {
        $products = $this->order->getAllItems();
        foreach ($products as $item) {
            $ret[] = new OrderProductWrapperJoomshopping($item);
        }
        return $ret;
    }

    const EXTID_METADATA_KEY = 'ext_order_id';

    /**
     * Идентификатор платежа внешней системы
     * @throws Throwable
     * @return mixed
     */
    public function getExtIdUnsafe()
    {
        return $this->order->transaction;
    }

    /**
     * Текущий статус заказа в CMS
     * @return mixed
     * @throws Throwable
     */
    public function getStatusUnsafe()
    {
        return $this->order->order_status;
    }

    /**
     * Обновляет статус заказа в БД
     * @param $newStatus
     * @return mixed
     * @throws Throwable
     */
    public function updateStatus($newStatus)
    {
        $this->order->order_status = $newStatus;
        $model = JSFactory::getModel('orderChangeStatus', 'jshop');
        $model->setData($this->getOrderId(), $newStatus, 0); //тут можно включить sendmail
        $model->store();
    }

    /**
     * Сохраняет привязку billid к заказу
     * @param $extId
     * @return mixed
     * @throws Throwable
     */
    public function saveExtId($extId)
    {
        $this->order->transaction = $extId;
        $this->order->store();
    }


    /**
     * Идентификатор клиента
     * @throws Throwable
     * @return string
     */
    public function getClientIdUnsafe()
    {
        return JFactory::getUser()->id;
    }
}