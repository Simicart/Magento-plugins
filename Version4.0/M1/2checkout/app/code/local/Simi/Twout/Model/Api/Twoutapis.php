<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 5/3/16
 * Time: 9:37 PM
 */
class Simi_Twout_Model_Api_Twoutapis extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            
        }
    }

    /*
     * Update Checkout Order (onepage) Information
     */

    public function update() {
        $data = $this->getData();
        if ($data['resourceid'] == 'update_order') {
            $data = $data['contents'];
            if ($data->payment_status == '2') {
                $this->setOrderCancel($data->invoice_number);
                return $this->statusError(array(Mage::helper('core')->__('The order has been cancelled')));
            }
            $orderdata = array();
            $orderdata['invoice_number'] = $data->invoice_number;
            $orderdata['transaction_id'] = $data->transaction_id;
            $orderdata['payment_status'] = $data->payment_status;
            Mage::getModel('twout/twout')->updateOrder($orderdata['invoice_number'], $orderdata);
            return array('twoutapi' => array('message' => Mage::helper('core')->__('Thank you for your purchase!')));
        }
    }

}
