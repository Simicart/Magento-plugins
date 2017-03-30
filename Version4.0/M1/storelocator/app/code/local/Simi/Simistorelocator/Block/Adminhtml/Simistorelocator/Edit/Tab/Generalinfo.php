<?php

class Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit_Tab_Generalinfo extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Simi_Simistorelocator_Block_Adminhtml_Simistorelocator_Edit_Tab_Generalinfo
     */
    protected function _prepareForm() {
        //prepare info form that this want to view
        
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $dataObj = new Varien_Object(array(
                    'store_id' => '',
                    'name_in_store' => '',
                    'status_in_store' => '',
                    'description_in_store' => '',
                    'address_in_store' => '',
                    'city_in_store' => '',
                    'sort_in_store' => ''
                ));
        
        if (Mage::getSingleton('adminhtml/session')->getSimistorelocatorData()) {
            $data = Mage::getSingleton('adminhtml/session')->getSimistorelocatorData();
            Mage::getSingleton('adminhtml/session')->setSimistorelocatorData(null);
        } elseif (Mage::registry('simistorelocator_data'))
            $data = Mage::registry('simistorelocator_data')->getData();
        if (isset($data))
            $dataObj->addData($data);        
        $data = $dataObj->getData();
        $fieldset = $form->addFieldset('simistorelocator_form', array('legend' => Mage::helper('simistorelocator')->__('Store Information')));

        $inStore = $this->getRequest()->getParam('store');
        $defaultLabel = Mage::helper('simistorelocator')->__('Use Default');
        $defaultTitle = Mage::helper('simistorelocator')->__('-- Please Select --');
        $scopeLabel = Mage::helper('simistorelocator')->__('STORE VIEW');
        
        $fullAddress = '';
        if($this->getRequest()->getParam('id') && (!$this->getRequest()->getParam('store') || $this->getRequest()->getParam('store') == '0')){
             $fullAddress = $data['address'].$data['city'].$data['state'].$data['zipcode'].$data['country'];
            $fullAddress = str_replace(" ", "", $fullAddress);            
        }
        
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $wysiwygConfig->addData(array(
            'add_variables'				=> false,
            'plugins'					=> array(),
            'widget_window_url'			=> Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/widget/index'),
            'directives_url'			=> Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
            'directives_url_quoted'		=> preg_quote(Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive')),
            'files_browser_window_url'	=> Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
        ));
        
        
        
        if ($data['simistorelocator_id']) {
            $typeID = Mage::helper('simiconnector')->getVisibilityTypeId('storelocator');
            $visibleStoreViews = Mage::getModel('simiconnector/visibility')->getCollection()
                    ->addFieldToFilter('content_type', $typeID)
                    ->addFieldToFilter('item_id', $data['simistorelocator_id']);
            $storeIdArray = array();
            foreach ($visibleStoreViews as $visibilityItem) {
                $storeIdArray[] = $visibilityItem->getData('store_view_id');
            }
            $data['storeview_id'] = implode(',', $storeIdArray);
        }
        
        $field = $fieldset->addField('storeview_id', 'multiselect', array(
            'name' => 'storeview_id[]',
            'label' => Mage::helper('simiconnector')->__('Store View'),
            'title' => Mage::helper('simiconnector')->__('Store View'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));
        $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
        $field->setRenderer($renderer);
        
        
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Store Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
            'disabled' => ($inStore && !$data['name_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="name_default" name="name_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['name_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="name_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('simistorelocator')->__('Status'),
            'required' => false,
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('simistorelocator')->__('Enabled'),
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('simistorelocator')->__('Disabled'),
                ),
            ),
            'disabled' => ($inStore && !$data['status_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="status_default" name="status_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['status_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="status_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('address', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Address'),
            'name' => 'address',
            'required' => true,
            'class' => 'required-entry',
            'disabled' => ($inStore && !$data['address_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="address_default" name="address_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['address_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="address_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('City'),
            'name' => 'city',
            'required' => true,
            'class' => 'required-entry',
            'after_element_html' => $inStore ? '</td><td class="use-default">
			<input id="city_default" name="city_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['city_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
			<label for="city_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('zipcode', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Zip Code'),
            'name' => 'zipcode',                        
        ));
        $fieldset->addField('country', 'select', array(
            'label' => Mage::helper('simistorelocator')->__('Country'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'country',
            'values' => Mage::helper('simistorelocator')->getOptionCountry(),
        ));

        $fieldset->addField('stateEl', 'note', array(
            'label' => Mage::helper('simistorelocator')->__('State/Province'),
            'name' => 'stateEl',
            'text' => $this->getLayout()->createBlock('simistorelocator/adminhtml_region')->setTemplate('simistorelocator/region.phtml')->toHtml(),
        ));
        
        $fieldset->addField('link', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Link to store'),
            'name' => 'link',
            'note' => 'E.g. The official link or Facebook link of store',
            'required' => false,
            'after_element_html' => '<input type="hidden" id="full-address" name="full_address" value="' . $fullAddress . '">',
        ));
        if($this->getRequest()->getParam('id')){
            $data['tags_store'] = Mage::helper('simistorelocator')->getTags($this->getRequest()->getParam('id'));
        }        
        $fieldset->addField('tags_store', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Store’s Tag(s)'),
            'name' => 'tags_store',     
            'note' => 'Used to filter stores by tag in frontend.<br />
                       E.g. simi, magento',
        ));
        
        $fieldset->addField('sort', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Sort order'),
            'name' => 'sort',            
            'required' => false,            
            'disabled' => ($inStore && !$data['sort_in_store']),
            'after_element_html' => $inStore ? '<p class="note" id="note_sort"><span>Order of store in list.</span></p></td><td class="use-default">
                <input id="sort_default" name="sort_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['sort_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                    <label for="sort_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                        [' . $scopeLabel . ']' : '<p class="note" id="note_sort"><span>Sort the display order of store in the store list.</span></p></td><td class="scope-label">[' . $scopeLabel . ']',
        ));

        $fieldset->addField('description', 'editor', array(
            'name' => 'description',
            'label' => Mage::helper('simistorelocator')->__('Description'),
            'title' => Mage::helper('simistorelocator')->__('Description'),
            'style' => 'width:500px; height:150px;',
            'wysiwyg'	=> true,
            'required'	=> false,
            'config'	=> $wysiwygConfig,
            'disabled' => ($inStore && !$data['description_in_store']),
            'after_element_html' => $inStore ? '</td><td class="use-default">
                                      <input id="description_default" name="description_default" type="checkbox" value="1" class="checkbox config-inherit" ' . ($data['description_in_store'] ? '' : 'checked="checked"') . ' onclick="toggleValueElements(this, Element.previous(this.parentNode))" />
                                      <label for="description_default" class="inherit" title="' . $defaultTitle . '">' . $defaultLabel . '</label>
                        </td><td class="scope-label">
                                      [' . $scopeLabel . ']
                        ' : '</td><td class="scope-label">
                                      [' . $scopeLabel . ']',
        ));

        $fieldset->addField('phone', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Phone Number'),
            'name' => 'phone',
            'required' => false,
        ));
        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Email Address'),
            'name' => 'email',
            'required' => false,
        ));

        $fieldset->addField('fax', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Fax Number'),
            'name' => 'fax',
            'required' => false,
        ));

        $fieldset->addField('image_id', 'text', array(
            'label' => Mage::helper('simistorelocator')->__('Store Image(s)'),
            'name' => 'images_id',
            'value' => Mage::helper('simistorelocator')->getDataImage($this->getRequest()->getParam('id')),
        ))->setRenderer($this->getLayout()->createBlock('simistorelocator/adminhtml_grid_renderer_storeimage'));

//        if (Mage::getSingleton('adminhtml/session')->getSimistorelocatorData()) {
//            $form->setValues(Mage::getSingleton('adminhtml/session')->getSimistorelocatorData());
//            Mage::getSingleton('adminhtml/session')->setSimistorelocatorData(null);
//        } elseif (Mage::registry('simistorelocator_data')) {
            $form->setValues($data);
//        }
        return parent::_prepareForm();
    }

}
