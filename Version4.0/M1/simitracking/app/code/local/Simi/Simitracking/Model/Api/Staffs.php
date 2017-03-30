<?php

/**
 * 
 */
class Simi_Simitracking_Model_Api_Staffs extends Simi_Simiconnector_Model_Api_Customers {

    protected $_DEFAULT_ORDER = 'entity_id';
    protected $_RETURN_MESSAGE;

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            switch ($data['resourceid']) {
                case 'login':
                    if (Mage::getModel('simiconnector/customer')->login($data))
                        $this->builderQuery = Mage::getSingleton('customer/session')->getCustomer();
                    else
                        throw new Exception($this->_helper->__('Login Failed'), 4);
                    break;
                case 'loginWithKeySession':
                    if (Mage::getModel('simitracking/customer')->loginWithKeySession($data))
                        $this->builderQuery = Mage::getSingleton('customer/session')->getCustomer();
                    else
                        throw new Exception($this->_helper->__('Login Failed'), 4);
                    break;
                case 'logout':
                    if (Mage::getModel('simiconnector/customer')->logout($data)) {
                        if (isset($data['params']) && isset($data['params']['device_token'])) {
                            Mage::getModel('simitracking/device')->deleteDevice($data['params']['device_token']);
                        }
                        $this->builderQuery = Mage::getSingleton('customer/session')->getCustomer();
                    }
                    else
                        throw new Exception($this->_helper->__('Logout Failed'), 4);
                    break;
                default:
                    break;
            }
        } else {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $currentCustomerId = Mage::getSingleton('customer/session')->getId();
            }
            $this->builderQuery = Mage::getModel('customer/customer')->getCollection()
                    ->addFieldToFilter('entity_id', $currentCustomerId);
        }
    }
    
    public function show() {
        $data = $this->getData();
        if ($data['resourceid'] == 'logout')
            return parent::show();
        $entity = $this->builderQuery;
        // Staff information       
        $user = Mage::getModel('simitracking/user')->getCollection()->addFieldToFilter('user_email', $entity->getEmail())->getFirstItem();
        if (!$user->getId())
                throw new Exception(Mage::helper('simitracking')->__('Your Account has No Tracking Permission'), 4);
        
        $userInfo = $user->toArray();
        $roleModel = Mage::getModel('simitracking/role')->load($user->getRoleId());
        $userInfo['role_title'] = $roleModel->getData('role_title');
        $userInfo['is_owner_role'] = $roleModel->getData('is_owner_role');
        
        /*
         *  Register Device and Create Session Id
         */
        // from email and password
        if (isset($data['params']) && isset($data['params']['device_token'])) {
            $deviceModel = Mage::getModel('simitracking/device')->saveDevice($data['params'], $user->getData('user_email'));
            $userInfo['device_data'] = $deviceModel->toArray();
        }
        // from qr code
        if (isset($data['params']['new_token_id']) && isset($data['params']['qr_session_id'])) {
            $platform = 1;
            if (isset($data['params']['plaform_id'])) {
                $platform = $data['params']['plaform_id'];
            }
            $deviceModel = Mage::getModel('simitracking/device')->saveDeviceFromQR($data['params']['new_token_id'],$user->getData('user_email'),$platform);
            $userInfo['device_data'] = $deviceModel->toArray();
        }        
        
        // Additional Information
        $userInfo['base_currency'] = Mage::app()->getStore()->getBaseCurrencyCode();
        $userInfo['device_ip'] = $_SERVER['REMOTE_ADDR'];
        
        // Permission
        $permissionCollection = Mage::getModel('simitracking/permission')->getCollection()->addFieldToFilter('role_id', $user->getRoleId());
        $permissionArray = array();
        $permissionList = Mage::helper('simitracking')->getPermissions();
        foreach ($permissionCollection as $permission) {
            $permissionInfo = $permission->toArray();
            $permissionInfo['permission_title'] = $permissionList[$permission['permission_id']]['title'];
            $permissionArray[] = $permissionInfo;
        }
        $userInfo['permissions'] = $permissionArray;
        
        $userInfo['customer_account_information'] = $entity->toArray();
        return $this->getDetail($userInfo);
    }
}
