<?php

class Simi_Simitracking_Helper_Siminotification extends Mage_Core_Helper_Abstract {

    public function sendNoticeNewOrder($orderId) {
        $orderModel = Mage::getModel('sales/order')->load($orderId);
        $data = array();
        $data['order_id'] = $orderId;
        $data['notice_title'] = $this->__('New Order Created: #').$orderModel->getData('increment_id').$this->__(', GrandTotal: ').$orderModel->getData('base_currency_code').$orderModel->getData('base_grand_total');
        $data['notice_content'] = $this->__('New Order Created: #').$orderModel->getData('increment_id').$this->__(', GrandTotal: ').$orderModel->getData('base_currency_code').$orderModel->getData('base_grand_total');
        $trans = $this->send($data);
        return $trans;
    }
    
    public function sendNoticeProcessingOrder($orderId) {
        $orderModel = Mage::getModel('sales/order')->load($orderId);
        $data = array();
        $data['order_id'] = $orderId;
        $data['notice_title'] = $this->__('Order Processing: #').$orderModel->getData('increment_id').$this->__(', GrandTotal: ').$orderModel->getData('base_currency_code').$orderModel->getData('base_grand_total');
        $data['notice_content'] = $this->__('Order Processing: #').$orderModel->getData('increment_id').$this->__(', GrandTotal: ').$orderModel->getData('base_currency_code').$orderModel->getData('base_grand_total');
        $trans = $this->send($data);
        return $trans;
    }
    
    public function sendNoticeCompletedOrder($orderId) {
        $orderModel = Mage::getModel('sales/order')->load($orderId);
        $data = array();
        $data['order_id'] = $orderId;
        $data['notice_title'] = $this->__('Order Completed: #').$orderModel->getData('increment_id').$this->__(', GrandTotal: ').$orderModel->getData('base_currency_code').$orderModel->getData('base_grand_total');
        $data['notice_content'] = $this->__('Order Completed: #').$orderModel->getData('increment_id').$this->__(', GrandTotal: ').$orderModel->getData('base_currency_code').$orderModel->getData('base_grand_total');
        $trans = $this->send($data);
        return $trans;
    }

    public function send($data) {
        $customerModelCollection = Mage::getModel('simitracking/user')->getCollection()->addFieldToFilter('is_receive_notification',1);
        $emailArray = array();
        foreach ($customerModelCollection as $customerModel) {
            $emailArray[] = $customerModel->getData('user_email');
        }
        $collectionDevice = Mage::getModel('simitracking/device')->getCollection()->addFieldToFilter('is_key_token', array('neq' => 1))->addFieldToFilter('user_email', array('in' => $emailArray));
        $collectionDevice2 = Mage::getModel('simitracking/device')->getCollection()->addFieldToFilter('is_key_token', array('neq' => 1))->addFieldToFilter('user_email', array('in' => $emailArray));
        $collectionDevice->addFieldToFilter('plaform_id', array('neq' => 3));
        $collectionDevice2->addFieldToFilter('plaform_id', array('eq' => 3));
        $resultIOS = $this->sendIOS($collectionDevice, $data);
        $resultAndroid = $this->sendAndroid($collectionDevice2, $data);
        if ($resultIOS || $resultAndroid)
            return true;
        else
            return false;
    }

    public function sendIOS($collectionDevice, $data) {
        $ch = $this->getDirPEMfile();
        $dir = $this->getDirPEMPassfile();
        $body['aps'] = array(
            'alert' => $data['notice_title'],
            'sound' => 'default',
            'badge' => 0,
            'title' => $data['notice_title'],
            'message' => $data['notice_content'],
            'order_id' => $data['order_id']
        );
        $payload = json_encode($body);
        $totalDevice = 0;

        $i = 0;
        $tokenArray = array();
        $sentsuccess = true;
        foreach ($collectionDevice as $item) {
            if ($i == 1) {
                $result = $this->repeatSendiOS($tokenArray, $payload, $ch, $dir);
                if (!$result)
                    $sentsuccess = false;
                $i = 0;
                $tokenArray = array();
            }
            $pos = strpos($item->getDeviceToken(), 'nontoken');
            if ((strlen($item->getDeviceToken()) < 70) && ($pos === false))
                $tokenArray[] = $item->getDeviceToken();
            $i++;
            $totalDevice++;
        }
        if ($i <= 1)
            $result = $this->repeatSendiOS($tokenArray, $payload, $ch, $dir);
        if (!$result)
            $sentsuccess = false;
        
        return true;
    }

    public function repeatSendiOS($tokenArray, $payload, $ch, $dir) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ch);
        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp) {
            return;
        }
        foreach ($tokenArray as $deviceToken) {
            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            if (!$result) {
                return false;
            }
        }
        fclose($fp);
        return true;
    }

    public function repeatSendAnddroid($total, $collectionDevice, $message) {
        while (true) {
            $from_user = 0;
            $check = $total - 999;
            if ($check <= 0) {
                //send to  (total+from_user) user from_user
                $is = $this->sendTurnAnroid($collectionDevice, $from_user, $from_user + $total, $message);
                if ($is == false) {
                    return false;
                }
                return true;
            } else {
                //send to 100 user from_user
                $is = $this->sendTurnAnroid($collectionDevice, $from_user, $from_user + 999, $message);
                if ($is == false) {
                    return false;
                }
                $total = $check;
                $from_user += 999;
            }
        }
    }

    public function sendTurnAnroid($collectionDevice, $from, $to, $message) {
        $registrationIDs = array();
        for ($i = $from; $i <= $to; $i++) {
            $item = $collectionDevice[$i];
            $pos = strpos($item['device_token'], 'nontoken');
            if ($pos === false)
                $registrationIDs[] = $item['device_token'];
        }
        
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registrationIDs,
            'data' => array("message" => $message),
        );
        $api_key = 'AIzaSyAy10doIjM2f6ghh-_-PHKURQslm-A3JCk';
        $headers = array(
            'Authorization: key=' . $api_key,
            'Content-Type: application/json');

        $result = '';
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            
        }

        $re = json_decode($result);

        if ($re == NULL || $re->success == 0) {
            return false;
        }
        return true;
    }

    public function sendAndroid($collectionDevice, $data) {
        $total = count($collectionDevice);
        $message = $data;

        $this->repeatSendAnddroid($total, $collectionDevice->getData(), $message);
        return true;
    }

    public function getDirPEMfile() {
        return Mage::getBaseDir('media') . DS . 'simi' . DS . 'simitracking' . DS . 'pem' . DS . 'push.pem';
    }

    public function getDirPEMPassfile() {
        return Mage::getBaseDir('media') . DS . 'simi' . DS . 'simitracking' . DS . 'pem' . DS . 'ios' . DS . 'pass_pem.config';
    }

    public function getConfig($nameConfig, $storeviewId = null) {
        if (!$storeviewId)
            $storeviewId = Mage::app()->getStore()->getId();
        return Mage::getStoreConfig($nameConfig, $storeviewId);
    }

}
