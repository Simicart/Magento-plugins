<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Productlabel Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Productlabel
 * @author      Magestore Developer
 */
class Magestore_Productlabel_Adminhtml_Productlabel_ProductlabelController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Productlabel_Adminhtml_ProductlabelController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('productlabel/productlabel')
                ->_addBreadcrumb(Mage::helper('productlabel')->__('Manage Product Labels'), Mage::helper('productlabel')->__('Manage Product Labels'));
        return $this;
    }

    public function indexAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $dirtyRules = Mage::getModel('productlabel/flag')->loadSelf();
        if ($dirtyRules->getState()) {
            Mage::getSingleton('adminhtml/session')->addNotice($this->getDirtyRulesNoticeMessage());
        }
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $id = $this->getRequest()->getParam('id');
        $store = $this->getRequest()->getParam('store');
        $model = Mage::getModel('productlabel/productlabel')->setStoreId($store)
                ->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
            Mage::register('productlabel_data', $model);

            $this->_title($this->__('Product Label'))
                    ->_title($this->__('Manage Label'));
            if ($model->getId()) {
                $this->_title($model->getName());
            } else {
                $this->_title($this->__('New Label'));
            }

            $this->loadLayout();
            $this->_setActiveMenu('productlabel/productlabel');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Product Label Manage'), Mage::helper('adminhtml')->__('Label Manage'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Product Lable'), Mage::helper('adminhtml')->__('Product Lable'));

            $this->getLayout()->getBlock('head')
                    ->setCanLoadExtJs(true)
                    ->setCanLoadRulesJs(true);

            $this->_addContent($this->getLayout()->createBlock('productlabel/adminhtml_productlabel_edit'))
                    ->_addLeft($this->getLayout()->createBlock('productlabel/adminhtml_productlabel_edit_tabs'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productlabel')->__('Product Label does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        if ($data = $this->getRequest()->getPost()) {
//            $model = Mage::getModel('productlabel/productlabel')
//                    ->load($this->getRequest()->getParam('id'));
            $store = $this->getRequest()->getParam('store', 0);

            if (isset($data['image']['delete'])) {
                Mage::helper('productlabel')->deleteImageFile($data['image']['value']);
                if ($data['is_auto_fill']) {
                    Mage::helper('productlabel')->deleteImageFile($data['category_image']['value']);
                }
            }

            $image = Mage::helper('productlabel')->uploadImage('image');



            if ($image || (isset($data['image']['delete']) && $data['image']['delete'])) {
                $data['image'] = $image;
            } else {
                $img_name = str_replace('/label/', '', strstr($data['image']['value'], '/label'));
                unset($data['image']);
            }
            
            //auto fill data from product page seting to category page setting
            if ($data['is_auto_fill']) {
                $data['category_text'] = $data['text'];
                $data['category_position'] = $data['position'];
                $data['category_display'] = $data['display'];
                if ($data['image']) {
                    $data['category_image'] = $data['image'];
                } else {
                    $data['category_image'] = $img_name;
                }
            } else {
                if (isset($data['category_image']['delete'])) {
                    Mage::helper('productlabel')->deleteImageFile($data['category_image']['value']);
                }

                $image = Mage::helper('productlabel')->uploadImage('category_image');



                if ($image || (isset($data['category_image']['delete']) && $data['category_image']['delete'])) {
                    $data['category_image'] = $image;
                } else {
                    unset($data['category_image']);
                }
            }

            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            if (isset($data['from_date']) && $data['from_date'] == '')
                $data['from_date'] = null;
            if (isset($data['to_date']) && $data['to_date'] == '')
                $data['to_date'] = null;

            if (isset($data['rule'])) {
                $rules = $data['rule'];
                if (isset($rules['conditions']))
                    $data['conditions'] = $rules['conditions'];
                if (isset($rules['actions']))
                    $data['actions'] = $rules['actions'];
                unset($data['rule']);
            }
            $autoApply = false;
            if (!empty($data['auto_apply'])) {
                $autoApply = true;
                unset($data['auto_apply']);
            }
// add data to model
            $model = Mage::getModel('productlabel/productlabel');
            $model->addData($data)
                    ->setId($this->getRequest()->getParam('id'));
            $model->setStoreId($store);
            try {
                $model->loadPost($data);
                $model->setData('from_date', $data['from_date'] != 0 ? $data['from_date'] : date('Y-m-d H:m:s'));
                $model->setData('to_date', $data['to_date']);
                $model->setIsApply(2);
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productlabel')->__('Product Label has been successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($autoApply) {
//                    if ($model->getStatus() == 1)
//                        $model->setIsApply(1);
                    $this->getRequest()->setParam('rule_id', $model->getId());
                    $this->_forward('applyRules');
                } else {
                    Mage::getModel('productlabel/flag')->loadSelf()
                            ->setState(1)
                            ->save();
                }
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId(), 'store' => $store,
                    ));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id'),
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productlabel')->__('Unable to find product label to save'));
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('productlabel/productlabel');
                if ($model->load($this->getRequest()->getParam('id'))->getAcceptDelete() == 2) {
                    Mage::getSingleton('adminhtml/session')->addError('You have not permission delete the label templates');
                } else {
                    $model->setId($this->getRequest()->getParam('id'))
                            ->delete();

                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('adminhtml')->__('Item was successfully deleted')
                    );
                }
                Mage::getModel('productlabel/flag')->loadSelf()
                        ->setState(0)
                        ->save();
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function applyRulesAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $errorMessage = Mage::helper('productlabel')->__('Unable to apply product label.');
        try {
            Mage::getModel('productlabel/productlabel')->applyAll();
            Mage::getModel('productlabel/flag')->loadSelf()
                    ->setState(0)
                    ->save();
            $labels = Mage::getModel('productlabel/productlabel')->getCollection();
            foreach ($labels as $label) {
                $label->afterLoad();
                $label->setIsApply(1);
                try {
                    $label->setId($label->getId())->save();
                } catch (Exception $exc) {
                    echo $exc->getTraceAsString();
                }
            }
            $this->_getSession()->addSuccess(Mage::helper('productlabel')->__('Product label has been successfully applied.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($errorMessage . ' ' . $e->getMessage());
        } catch (Exception $e) {
            echo $e->getMessage();
            $this->_getSession()->addError($errorMessage);
        }
        $this->_redirect('*/*');
    }

    public function setDirtyRulesNoticeMessage($dirtyRulesNoticeMessage) {
        $this->_dirtyRulesNoticeMessage = $dirtyRulesNoticeMessage;
    }

    /**
     * Get dirty rules notice message
     *
     * @return string
     */
    public function getDirtyRulesNoticeMessage() {
        $defaultMessage = Mage::helper('productlabel')->__('There are label that have been changed but were not applied. Please, click Apply Labels in order to see immediate effect in the product labels.');
        return isset($this->_dirtyRulesNoticeMessage) ? $this->_dirtyRulesNoticeMessage : $defaultMessage;
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $productlabelIds = $this->getRequest()->getParam('productlabel');
        if (!is_array($productlabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($productlabelIds as $productlabelId) {
                    $productlabel = Mage::getModel('productlabel/productlabel')->load($productlabelId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($productlabelIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $productlabelIds = $this->getRequest()->getParam('productlabel');
        if (!is_array($productlabelIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productlabelIds as $productlabelId) {
                    $model = Mage::getSingleton('productlabel/productlabel')
                            ->load($productlabelId);

                    if ($model->getStatus() == 1 && $this->getRequest()->getParam('status') == 2)
                        $model->setIsApply(2);
                    if ($model->getStatus() == 2 && $this->getRequest()->getParam('status') == 1)
                        $model->setIsApply(2);
                    $model->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true);
                    $model->save();
                }
                Mage::getModel('productlabel/flag')->loadSelf()
                        ->setState(1)
                        ->save();
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($productlabelIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $fileName = 'productlabel.csv';
        $content = $this->getLayout()
                ->createBlock('productlabel/adminhtml_productlabel_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
		// if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        $fileName = 'productlabel.xml';
        $content = $this->getLayout()
                ->createBlock('productlabel/adminhtml_productlabel_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('productlabel');
    }

}