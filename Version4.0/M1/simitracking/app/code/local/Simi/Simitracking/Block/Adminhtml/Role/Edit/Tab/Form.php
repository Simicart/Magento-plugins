<?php

class Simi_Simitracking_Block_Adminhtml_Role_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getConnectorData()) {
            $data = Mage::getSingleton('adminhtml/session')->getConnectorData();
            Mage::getSingleton('adminhtml/session')->setConnectorData(null);
        } elseif (Mage::registry('role_data'))
            $data = Mage::registry('role_data')->getData();


        $fieldset = $form->addFieldset('simitracking_form', array('legend' => Mage::helper('simitracking')->__('Role information')));

        $fieldset->addField('role_title', 'text', array(
            'label' => Mage::helper('simitracking')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'role_title',
        ));

        $fieldset->addField('is_owner_role', 'select', array(
            'label' => Mage::helper('simitracking')->__('Tracking View'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'is_owner_role',
            'values' => array(
                array('value' => 0, 'label' => Mage::helper('simitracking')->__('Sales Staff')),
                array('value' => 1, 'label' => Mage::helper('simitracking')->__('Store Owner')),
            )
        ));
        
        foreach (Mage::getModel('simitracking/permission')->getCollection()->addFieldToFilter('role_id', $data['entity_id']) as $permissionModel) {
            $data['simi_permission_id_' . $permissionModel->getPermissionId()] = '1';
        }
        foreach (Mage::helper('simitracking')->getPermissionSections() as $sectionIndex => $section) {
            $fieldset = $form->addFieldset('section_' . $sectionIndex, array('legend' => $section));
            foreach (Mage::helper('simitracking')->getPermissions() as $permissionId => $permission) {
                $generatedId = 'simi_permission_id_' . $permissionId;
                if ($data[$generatedId] != '1')
                    $data[$generatedId] = '0';
                if ($permission['section'] == $sectionIndex)
                    $fieldset->addField($generatedId, 'select', array(
                        'label' => $permission['title'],
                        'required' => false,
                        'name' => $generatedId,
                        'onchange' => 'this.className= \'permission_allowed_\' + this.options[this.selectedIndex].value',
                        'class' => 'permission_allowed_' . $data[$generatedId],
                        'values' => array(
                            array(
                                'value' => '0',
                                'label' => Mage::helper('simitracking')->__('Not Allowed')
                            ),
                            array(
                                'value' => '1',
                                'label' => Mage::helper('simitracking')->__('Allowed')
                            ))
                    ));
            }
        }

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
