<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Simi_Simistorelocator_Adminhtml_Simistorelocator_Importcontroller extends Mage_Adminhtml_Controller_Action {

    public function initAction() {
        $this->loadLayout()
                ->_setActiveMenu('simistorelocator/stores')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));
        return $this;
    }

    public function importstoreAction() {
        $this->loadLayout();
        $this->_setActiveMenu('simistorelocator/simistorelocator');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $editBlock = $this->getLayout()->createBlock('simistorelocator/adminhtml_simistorelocator_import');
        $editBlock->removeButton('delete');
        $editBlock->removeButton('saveandcontinue');
        $editBlock->removeButton('reset');
        $editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('simistorelocatoradmin/simistorelocator_simistorelocator/index') . '\')');
        $editBlock->setData('form_action_url', $this->getUrl('*/*/save', array()));

        $this->_addContent($editBlock)
                ->_addLeft($this->getLayout()->createBlock('simistorelocator/adminhtml_simistorelocator_import_tabs'));

        $this->renderLayout();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('connector');
    }

    public function saveAction() {
        $overwrite_option = $this->getRequest()->getPost('overwrite_store');
        if (!isset($_FILES['csv_store'])) {
            Mage::getSingleton('core/session')->addError('Not selected file!');
            $this->_redirect('*/*/importstore');
            return;
        }
        $importCsvFile = new Varien_File_Csv();
        $data = $importCsvFile->getData($_FILES['csv_store']['tmp_name']);
        $store = Mage::getModel('simistorelocator/simistorelocator');
        $storeData = array();
        try {
            $total = 0;
            if (count($data)) {
                foreach ($data as $col => $row) {
                    if ($col == 0) {
                        $index_row = $row;
                    } else {

                        for ($i = 0; $i < count($row); $i++) {
                            $storeData[$index_row[$i]] = $row[$i];
                        }
                        if (($storeData['status']) && ($storeData['status'] == 'Enabled')) {
                            $storeData['status'] = 1;
                        } else {
                            $storeData['status'] = 0;
                        }
                        $store->setData($storeData);
                        $store->setId(null);
                        if ($store->import($overwrite_option))
                            $total++;
                        $typeID = Mage::helper('simiconnector')->getVisibilityTypeId('storelocator');
                        $visibleStoreViews = Mage::getModel('simiconnector/visibility')->getCollection()
                                ->addFieldToFilter('content_type', $typeID)
                                ->addFieldToFilter('item_id', $store->getData('simistorelocator_id'));
                        foreach ($visibleStoreViews as $visibilityItem)
                            $visibilityItem->delete();
                        foreach (Mage::getModel('core/store')->getCollection() as $storeViewModel){
                            $storeViewId = $storeViewModel->getId();
                            $visibilityItem = Mage::getModel('simiconnector/visibility');
                            $visibilityItem->setData('content_type',$typeID);                        
                            $visibilityItem->setData('item_id',$store->getData('simistorelocator_id'));
                            $visibilityItem->setData('store_view_id',$storeViewId);
                            $visibilityItem->save();
                        } 
						
                        if (($storeData['image_name']) && ($storeData['image_name'] != null)) {
                            $i = 0;
                            $storeId = $store->getId();
                            Mage::helper('simistorelocator')->deleteImageFormStore($storeId);
                            $image_names = explode(',', $storeData['image_name']);
                            foreach ($image_names as $image_name) {
                                $i++;
                                $image = Mage::getModel('simistorelocator/image');
                                $last = $image->getCollection()->getLastItem()->getData('options') + 1;

                                $image_name = trim($image_name);

                                if (isset($image_name) && ($image_name != null)) {
                                    if ($i == 1) {
                                        $image->setData('statuses', 1);
                                    } else {
                                        $image->setData('statuses', 0);
                                    }
                                    $image->setData('options', $last);
                                    $image->setData('image_delete', 2);
                                    $image->setData('name', $image_name);
                                    $image->setData('simistorelocator_id', $storeId);
                                    $image->save();
                                }
                            }
                        }

                        if (($storeData['tag_store']) && ($storeData['tag_store'] != null)) {
                            $storeId = $store->getId();
                            Mage::helper('simistorelocator')->deleteTagFormStore($storeId);
                            $tag_arr = explode(",", $storeData['tag_store']);
                            foreach ($tag_arr as $item) {
                                $tag = Mage::getModel('simistorelocator/tag');
                                $tagItem = trim($item);
                                if (isset($tagItem)) {
                                    $tag->setData('value', $tagItem);
                                    $tag->setData('simistorelocator_id', $storeId);
                                    $tag->save();
                                }
                            }
                        }
                    }
                }   
            } else {
                $this->_redirect('*/simistorelocator_simistorelocator/index');
                Mage::getSingleton('core/session')->addError('Import Empty File !');
            }
            $this->_redirect('*/simistorelocator_simistorelocator/index');

            if ($total != 0)
                Mage::getSingleton('core/session')->addSuccess('Imported successful total ' . $total . ' stores');
            else
                Mage::getSingleton('core/session')->addSuccess('Store file is Imported!');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/importstore');
        }
    }

}

?>
