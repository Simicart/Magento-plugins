<?php

class Simi_Simitracking_Model_Api_Permissions extends Simi_Simiconnector_Model_Api_Abstract {

    public function setBuilderQuery() {
        $data = $this->getData();
        if ($data['resourceid']) {
            
        } else {
            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                $user = Mage::getModel('simitracking/user')->getCollection()->addFieldToFilter('user_email', Mage::getSingleton('customer/session')->getCustomer()->getEmail())->getFirstItem();
                $this->builderQuery = Mage::getModel('simitracking/permission')->getCollection()->addFieldToFilter('role_id', $user->getRoleId());
            } else
                throw new Exception(Mage::helper('simitracking')->__('Please Login'), 4);
        }
    }
    
    public function index()
    {
        $result = parent::index();
        $permissionList = Mage::helper('simitracking')->getPermissions();

        foreach ($result['permissions'] as $index=>$permission) {
            $permission['permission_title'] = $permissionList[$permission['permission_id']]['title'];
            $result['permissions'][$index] = $permission;
        }
        return $result;
    }

}
