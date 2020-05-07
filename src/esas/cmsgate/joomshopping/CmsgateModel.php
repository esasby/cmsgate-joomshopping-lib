<?php

namespace esas\cmsgate\joomshopping;

use \JModelLegacy;
use \JFactory;

class CmsgateModel extends JModelLegacy
{

    /**
     * Получаем из БД заказ не по order_id, а по индентификатору транзакции внешней системы
     * @param $transaction
     * @return order
     */
    public static function getOrderIdByTrxId($transaction)
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE transaction = '" . $db->escape($transaction) . "' ORDER BY order_id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            saveToLog("payment.log", 'Can not load order by transaction[' . $transaction . "]");
            return null;
        }
        return $rows[0]->order_id;
    }

    /**
     * Получения идентификатора текущего заказа. Возможно, есть какой-то более удачный способ
     * @return mixed
     */
    public static function getCurrentOrderId()
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE user_id = '" . $db->escape(JFactory::getUser()->id) . "' ORDER BY order_id DESC LIMIT 1";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows[0]->order_id;
    }

    /**
     * Получаем из БД заказ по order_number
     * @param $order_number
     * @return order
     */
    public static function getOrderIdByOrderNumber($order_number)
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE order_number = '" . $db->escape($order_number) . "' ORDER BY order_id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            saveToLog("payment.log", 'Can not load order by order_number[' . $order_number . "]");
            return null;
        }
        return $rows[0]->order_id;
    }
}