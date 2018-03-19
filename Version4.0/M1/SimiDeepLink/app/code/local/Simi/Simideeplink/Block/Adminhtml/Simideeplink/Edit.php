<?php


class Simi_Simideeplink_Block_Adminhtml_Simideeplink_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'simideeplink';
        $this->_controller = 'adminhtml_simideeplink';
        if (Mage::registry('simideeplink_data')
            && Mage::registry('simideeplink_data')->getId()
        ) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        } else {
            $this->_updateButton('save', 'label', Mage::helper('simideeplink')->__('Generate '));
        }
        $this->_updateButton('delete', 'label', Mage::helper('simideeplink')->__('Delete Link'));


        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('simideeplink_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'simideeplink_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'simideeplink_content');
            }

            function saveAndContinueEdit(){
            console.log('saveAndContinueEdit');
               var x = editForm.submit($('edit_form').action+'back/edit/');
               console.log('xxxx ' + x);
            }
            
            
            function onChangeType(type){
                switch (type) {
                    case '0':
                    // none
                    $('title').up('tr').hide();
                    $('title').className = 'input-text';
                    $('product_id').up('tr').hide();    
                    $('product_id').className = 'input-text'; 
                    $('category_id').up('tr').hide(); 
                    $('category_id').className = 'input-text';
                    $('cms_id').up('tr').hide(); 
                    $('cms_id').className = 'input-text';
                    break;
                    case '1':
                    // product
                    $('title').up('tr').show();
                    $('product_id').up('tr').show();    
                    $('product_id').className = 'required-entry input-text'; 
                    $('category_id').up('tr').hide(); 
                    $('category_id').className = 'input-text';
                    $('cms_id').up('tr').hide(); 
                    $('cms_id').className = 'input-text';
                    break;
                    case '2':
                    // category
                    $('title').up('tr').show();
                    $('product_id').up('tr').hide();    
                    $('product_id').className = 'input-text'; 
                    $('category_id').up('tr').show(); 
                    $('category_id').className = 'required-entry input-text';
                    $('cms_id').up('tr').hide(); 
                    $('cms_id').className = 'input-text';
                    break;
                    case '3':
                    // cms
                    $('title').up('tr').show();
                    $('product_id').up('tr').hide();    
                    $('product_id').className = 'input-text'; 
                    $('category_id').up('tr').hide(); 
                    $('category_id').className = 'input-text';
                    $('cms_id').up('tr').show(); 
                    $('cms_id').className = 'required-entry';
                    break;
                    default:
                    $('title').up('tr').hide();
                    $('title').className = 'input-text';
                    $('product_id').up('tr').hide();    
                    $('product_id').className = 'input-text'; 
                    $('category_id').up('tr').hide(); 
                    $('category_id').className = 'input-text';
                    $('cms_id').up('tr').hide(); 
                    $('cms_id').className = 'input-text';
                    break;  
                }
            }
            
        ";
    }


    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('simideeplink_data')
            && Mage::registry('simideeplink_data')->getId()
        ) {
            return Mage::helper('simideeplink')->__("Edit Item '%s'",
                $this->htmlEscape(Mage::registry('simideeplink_data')->getTitle())
            );
        }
        return Mage::helper('simideeplink')->__('Add Item');
    }
}