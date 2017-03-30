<?php

class Simi_Simitracking_Block_Adminhtml_User_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getConnectorData()) {
            $data = Mage::getSingleton('adminhtml/session')->getConnectorData();
            Mage::getSingleton('adminhtml/session')->setConnectorData(null);
        } elseif (Mage::registry('user_data'))
            $data = Mage::registry('user_data')->getData();

        $fieldset = $form->addFieldset('simitracking_form', array('legend' => Mage::helper('simitracking')->__('User information')));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('simitracking')->__('Enable'),
            'name' => 'status',
            'values' => array(
                array('value' => 1, 'label' => Mage::helper('simitracking')->__('Yes')),
                array('value' => 0, 'label' => Mage::helper('simitracking')->__('No')),
            )
        ));

        $fieldset->addField('user_title', 'text', array(
            'label' => Mage::helper('simitracking')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'user_title',
        ));

        $roleArray = array();
        foreach (Mage::getModel('simitracking/role')->getCollection() as $role) {
            $roleArray[] = array(
                'value' => $role->getId(),
                'label' => $role->getRoleTitle()
            );
        }
        if (count($roleArray) == 0)
            $fieldset->addField('role_id', 'text', array(
                'label' => Mage::helper('simitracking')->__('Role'),
                'class' => 'required-entry',
                'required' => true,
                'disabled' => true,
                'name' => 'role_id',
            ));
        else
            $fieldset->addField('role_id', 'select', array(
                'label' => Mage::helper('simitracking')->__('Role'),
                'class' => 'required-entry',
                'required' => true,
                'values' => $roleArray,
                'name' => 'role_id',
            ));

        $fieldset->addField('user_profile_image', 'image', array(
            'label' => Mage::helper('simitracking')->__('Profile Image'),
            'required' => false,
            'note' => Mage::helper('simitracking')->__('Square image. Eg. 200px x 200px'),
            'name' => 'user_profile_image_co',
        ));

        $fieldset->addField('user_email', 'text', array(
            'label' => Mage::helper('simitracking')->__('Customer Email'),
            'class' => 'required-entry validate-email',
            'required' => true,
            'note' => Mage::helper('simitracking')->__('Customer account with this Email has permissions as the Role assigned'),
            'name' => 'user_email',
        ));
        $existed_device = Mage::getModel('simitracking/device')->getCollection()
                        ->addFieldToFilter('is_key_token', 1)
                        ->addFieldToFilter('user_email', $data['user_email'])->getFirstItem();
        if ($existed_device->getId()) {
            //$url = Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_SECURE_URL);
            $url = $this->builderQuery = Mage::getModel('core/store')->getCollection()->getFirstItem()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
            $jsonString = '{"user_email":"' . $data['user_email'] . '","url":"' . $url . '","session_id":"' . $existed_device->getData('session_id') . '"}';
            $encodedString = base64_encode($jsonString);
            $loginQR = '<img src="http://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . $encodedString . '" />';
            $fieldset->addField('qrcode', 'label', array(
                'label' => Mage::helper('simitracking')->__('Login QR Code'),
                'required' => false,
                'bold' => true,
                'name' => 'qrcode',
                'after_element_html' => $loginQR
            ));
        }
        
                
        if (!isset($data['is_receive_notification']))
            $data['is_receive_notification'] = 1;
        $fieldset->addField('is_receive_notification', 'select', array(
            'label' => Mage::helper('simitracking')->__('Receive Notification'),
            'name' => 'is_receive_notification',
            'values' => array(
                array('value' => 1, 'label' => Mage::helper('simitracking')->__('Yes')),
                array('value' => 0, 'label' => Mage::helper('simitracking')->__('No')),
            )
        ));

        
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
