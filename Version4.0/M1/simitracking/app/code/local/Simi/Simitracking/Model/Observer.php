<?php

class Simi_Simitracking_Model_Observer {

    public function orderPlacingCompleted($observer) {
        if (!Mage::getStoreConfig('simitracking/general/new_order_notification')) {
            return;
        }
        $orderId = $observer->getEvent()->getOrder()->getId();
        try {
            $orderProcessingReg = Mage::registry('simi_sent_new_notice_once');
            if ($orderProcessingReg) {
                $orderIdArray = explode(",", $orderProcessingReg);
                if (in_array($orderId, $orderIdArray)) {
                    return;
                }
                $orderIdArray[] = $orderId;
                Mage::register('simi_sent_new_notice_once', implode(",", $orderIdArray));
            }
            Mage::register('simi_sent_new_notice_once', $orderId);
            Mage::helper('simitracking/siminotification')->sendNoticeNewOrder($orderId);
        } catch (Exception $exc) {
            
        }
    }

    public function orderSaveAfter($observer) {
        try {
            $order = $observer['order'];
            if ($order->getData('state') == $order->getOrigData('state'))
                return;

            if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
                if (!Mage::getStoreConfig('simitracking/general/order_processing_notification')) {
                    return;
                }
                $orderId = $order->getId();

                $orderProcessingReg = Mage::registry('simi_sent_processing_notice_once');
                if ($orderProcessingReg) {
                    $orderIdArray = explode(",", $orderProcessingReg);
                    if (in_array($orderId, $orderIdArray)) {
                        return;
                    }
                    $orderIdArray[] = $orderId;
                    Mage::register('simi_sent_processing_notice_once', implode(",", $orderIdArray));
                }
                Mage::register('simi_sent_processing_notice_once', $orderId);
                Mage::helper('simitracking/siminotification')->sendNoticeProcessingOrder($orderId);
                return;
            } else if ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {
                if (!Mage::getStoreConfig('simitracking/general/order_completed_notification')) {
                    return;
                }
                $orderId = $order->getId();

                $orderProcessingReg = Mage::registry('simi_sent_complete_notice_once');
                if ($orderProcessingReg) {
                    $orderIdArray = explode(",", $orderProcessingReg);
                    if (in_array($orderId, $orderIdArray)) {
                        return;
                    }
                    $orderIdArray[] = $orderId;
                    Mage::register('simi_sent_complete_notice_once', implode(",", $orderIdArray));
                }
                Mage::register('simi_sent_complete_notice_once', $orderId);
                Mage::helper('simitracking/siminotification')->sendNoticeCompletedOrder($orderId);
                return;
            }
        } catch (Exception $exc) {
            
        }
    }

}
