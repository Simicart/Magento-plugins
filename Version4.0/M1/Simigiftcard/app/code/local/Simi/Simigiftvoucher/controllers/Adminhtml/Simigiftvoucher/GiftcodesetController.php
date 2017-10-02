<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

/**
 * Class Simi_Simigiftvoucher_Adminhtml_Simigiftvoucher_GiftcodesetController
 */
class Simi_Simigiftvoucher_Adminhtml_Simigiftvoucher_GiftcodesetController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initialize action
     *
     * @return Simi_Simigiftvoucher_Adminhtml_GiftCodeSetController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simigiftvoucher/giftcodeset')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Code Sets'),
                Mage::helper('adminhtml')->__('Gift Code Sets'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Gift Code Sets'))
            ->_title($this->__('Gift Code Sets'));
        $this->_initAction()
            ->renderLayout();
    }

    public function giftcodelistAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    /**
     * View and edit item action
     */
    public function editAction()
    {
        $giftCodeSetId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simigiftvoucher/giftcodeset')->load($giftCodeSetId);

        if ($model->getId() || $giftCodeSetId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            $this->_title($this->__('Gift Code Set'));
            if ($model->getId()) {
                $this->_title($model->getSetName());
            } else {
                $this->_title($this->__('New Gift Code'));
            }

            Mage::register('giftcodeset_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('simigiftvoucher/giftcodeset');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Gift Code Set Manager'),
                Mage::helper('adminhtml')->__('Gift Code Set Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Gift Code Set'), Mage::helper('adminhtml')->__('Gift Code Set')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftcodeset_edit'))
                ->_addLeft($this->getLayout()->createBlock('simigiftvoucher/adminhtml_giftcodeset_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('simigiftvoucher')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save
     */
    public function saveAction()
    {
        $model = Mage::getModel('simigiftvoucher/giftcodeset');

        $data = $this->getRequest()->getPost();
        if ($data) {
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            try {

                if(!(substr($_FILES['import_code']["name"], -4)=='.csv')){
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simigiftvoucher')->__('Please import the csv file!.'));
                    $this->_redirect('*/*/edit',array('id' => $model->getId()));
                }

                $model->save();
                if( isset($_FILES['import_code']) && substr($_FILES['import_code']["name"], -4)=='.csv') {
                    try {
                        $fileName = $_FILES['import_code']['tmp_name'];
                        $csvObject = new Varien_File_Csv();
                        $data= $csvObject->getData($fileName);
                        $count = array();
                        $fields = array();
                        $giftVoucherImport = array();
                        foreach ($data as $row => $cols) {
                            if ($row == 0) {
                                $fields = $cols;
                            } else {
                                $giftVoucherImport[] = array_combine($fields, $cols);
                            }
                        }

                        foreach ($giftVoucherImport as $giftVoucherData) {
                            $giftVoucher = Mage::getModel('simigiftvoucher/giftvoucher');
                            if (isset($giftVoucherData['gift_code']) && $giftVoucherData['gift_code']) {
                                $giftVoucher = $giftVoucher->load($giftVoucherData['gift_code'], 'gift_code');
                                if ($giftVoucher->getId()) {
                                    Mage::getSingleton('adminhtml/session')->addError(
                                        Mage::helper('simigiftvoucher')->__('Gift code %s already existed', $giftVoucher->getGiftCode())
                                    );

                                } else {
                                    if ($model->getId()) {
                                        try {
                                            $giftVoucher->setGiftCode($giftVoucherData['gift_code'])
                                                ->setIncludeHistory(true)
                                                ->setUsed($giftVoucherData['used'])
                                                ->setSetId($model->getId())
                                                ->setStoreId(0)
                                                ->save();
                                            $count[] = $giftVoucher->getId();
                                        } catch (Exception $e) {
                                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                                        }
                                    }
                                }
                            }
                        }

                        $qtys=$model->load($this->getRequest()->getParam('set_id'))->getSetQty();
                        $model->setSetQty($qtys+count($count));
                        $model->save();

                        if (count($count)) {
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simigiftvoucher')->__('Imported total %d Gift Code(s)', count($count)));
                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simigiftvoucher')->__('The Gift Code Set have been saved.'));

                            if ($this->getRequest()->getParam('back')) {
                                return $this->_redirect('*/*/edit', array('id' => $model->getId()));
                            }
                            return $this->_redirect('*/*/');
                        } else {
                            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simigiftvoucher')->__('No gift code imported'));
                            return $this->_redirect('*/*/edit',array('id' => $model->getId()));
                        }
                    } catch (Exception $e) {
                        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simigiftvoucher')->__('Please check your import file content again.'));
                        return $this->_redirect('*/*/edit',array('id' => $model->getId()));
                    }

                }

                Mage::getSingleton('adminhtml/session')->setFormData(false);


                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }


        }
        return $this->_redirect('*/*/edit',array('id' => $model->getId()));
    }


    /**
     * Delete Gift template in mass number
     */
    public function massDeleteAction()
    {
        $giftCodeSets = $this->getRequest()->getParam('giftcodeset');
        if (!is_array($giftCodeSets)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simigiftvoucher')->__('Please select gift code set(s).'));
        } else {
            if (!empty($giftCodeSets)) {
                try {
                    foreach ($giftCodeSets as $giftCodeSetId) {
                        $giftCodeSet = Mage::getSingleton('simigiftvoucher/giftcodeset')->load($giftCodeSetId);
                        $giftCodeSet->delete();
                    }
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($giftCodeSets))
                    );
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete item action
     */
    public function deleteAction()
    {

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('simigiftvoucher/giftcodeset');

                $model->setId($this->getRequest()->getParam('id'))->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );

                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' =>
                    $this->getRequest()->getParam('id')))

                ;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Create new Gift history action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'giftcodeset.csv';
        $content = $this->getLayout()
            ->createBlock('simigiftvoucher/adminhtml_giftcodeset_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'giftcodeset.xml';
        $content = $this->getLayout()
            ->createBlock('simigiftvoucher/adminhtml_giftcodeset_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simigiftvoucher/giftcodeset');
    }

    public function downloadSampleSetsAction()
    {
        $filename = Mage::getBaseDir('media') . DS . 'simigiftvoucher' . DS . 'import_giftcodesets_sample.csv';
        $this->_prepareDownloadResponse('import_giftcodesets_sample.csv', file_get_contents($filename));
    }

}
