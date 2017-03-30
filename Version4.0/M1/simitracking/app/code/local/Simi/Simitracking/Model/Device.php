<?php

class Simi_Simitracking_Model_Device extends Mage_Core_Model_Abstract {

    protected $_eventPrefix = 'simitracking_device';
    protected $_eventObject = 'simitracking_device';

    public function _construct() {
        parent::_construct();
        $this->_init('simitracking/device');
    }

    public function detectMobile() {
        $user_agent = '';
        if ($_SERVER["HTTP_USER_AGENT"]) {
            $user_agent = $_SERVER["HTTP_USER_AGENT"];
        }
        if (strstr($user_agent, 'iPhone') || strstr($user_agent, 'iPod')) {
            return 1;
        } elseif (strstr($user_agent, 'iPad')) {
            return 2;
        } elseif (strstr($user_agent, 'Android')) {
            return 3;
        } else {
            return 1;
        }
    }

    public function createKeyDevice($userEmail) {
        $existed_device = Mage::getModel('simitracking/device')->getCollection()
                        ->addFieldToFilter('is_key_token', 1)
                        ->addFieldToFilter('user_email', $userEmail)->getFirstItem();
        if (!$existed_device->getId()) {
            $newDevice = Mage::getModel('simitracking/device');
            $newDevice->setData('is_key_token', 1);
            $newDevice->setData('user_email', $userEmail);
            $newDevice->setData('device_token', 'nontoken_' . md5(time() . rand(1000000000, 9999999999)));
            $newDevice->setData('session_id', md5(time() . rand(1000000000, 9999999999)));
            $newDevice->setData('plaform_id', 99);
            $newDevice->setData('session_deadline', time() + 86400 * 3);
            $newDevice->setData('session_id', md5(time() . rand(1000000000, 9999999999)));
            $newDevice->save();
        }
    }

    public function saveDeviceFromQR($qrNewToken, $userEmail, $platformId) {
        $deviceData = array();
        $deviceData['device_token'] = $qrNewToken;
        $deviceData['plaform_id'] = $platformId;
        return $this->saveDevice($deviceData, $userEmail);
    }

    public function saveDevice($deviceData, $userEmail = '') {
        if (!$deviceData['device_token'])
            return;

        if (isset($deviceData['plaform_id']))
            $device_id = $deviceData['plaform_id'];
        else
            $device_id = $this->detectMobile();

        $existed_device = $this->getCollection()->addFieldToFilter('device_token', $deviceData['device_token'])->getFirstItem();
        if ($existed_device->getId()) {
            $this->setId($existed_device->getId());
        }
        $this->setData('session_id', md5(time() . rand(1000000000, 9999999999)));
        $this->setData('session_deadline', time() + 86400 * 3);
        $this->setData('user_email', $userEmail);
        $this->setData('device_token', $deviceData['device_token']);
        $this->setData('plaform_id', $device_id);
        $this->setData('latitude', $deviceData['latitude']);
        $this->setData('longitude', $deviceData['longitude']);
        $this->setData('created_time', now());
        $this->setData('app_id', $deviceData['app_id']);
        $this->setData('device_ip', $_SERVER['REMOTE_ADDR']);
        $this->setData('device_user_agent', $_SERVER['HTTP_USER_AGENT']);
        $this->save();

        return $this;
    }
    
    public function deleteDevice($deviceToken) {
        $existed_device = $this->getCollection()->addFieldToFilter('device_token', $deviceToken)->getFirstItem();
        if ($existed_device->getId()) {
            $existed_device->delete();
        }
    }
    
    

}
