<?php

class Simi_Simimigrate_Adminhtml_Simimigrate_StoreviewController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Simi_Appreport_Adminhtml_AppreportController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('simimigrate/storeview')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Transactions'), Mage::helper('adminhtml')->__('App Transactions')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'storeview.csv';
        $content = $this->getLayout()
                ->createBlock('simimigrate/adminhtml_storeview_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'storeview.xml';
        $content = $this->getLayout()
                ->createBlock('simimigrate/adminhtml_storeview_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('simimigrate');
    }

    /**
     * gird action
     */
    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

}
