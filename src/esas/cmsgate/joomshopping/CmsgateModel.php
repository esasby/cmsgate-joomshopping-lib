<?php
namespace esas\cmsgate\joomshopping;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\wrappers\OrderWrapper;
use JSFactory;

class CmsgateModel extends JModelLegacy
{

    /**
     * Получаем из БД заказ не по order_id, а по индентификатору транзакции внешней системы
     * @param $transaction
     * @return OrderWrapper
     */
    static function getOrderByTrxId($transaction)
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE transaction = '" . $db->escape($transaction) . "' ORDER BY order_id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            Logger::getLogger("model")->error('Can not load order by transaction[' . $transaction . "]");
            return null;
        }
        return Registry::getRegistry()->getOrderWrapper($rows[0]->order_id);

    }


    /**
     * Получаем из БД заказ по order_number
     * @param $order_number
     * @return OrderWrapper
     */
    static function getOrderByOrderNumber($order_number)
    {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE order_number = '" . $db->escape($order_number) . "' ORDER BY order_id DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1) {
            Logger::getLogger("model")->error('Can not load order by order_number[' . $order_number . "]");
            return null;
        }
        return Registry::getRegistry()->getOrderWrapper($rows[0]->order_id);
    }
}