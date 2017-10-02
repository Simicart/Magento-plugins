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
 * Adminhtml Giftvoucher History controller
 *
 * @category Simi
 * @package  Simi_Simigiftvoucher
 * @module   Giftvoucher
 * @author   Simi Developer
 */
class Simi_Simigiftvoucher_Adminhtml_Simigiftvoucher_GifthistoryController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initialize action
     *
     * @return Simi_Simigiftvoucher_Adminhtml_GifthistoryController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simigiftvoucher/gifthistory')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift History'), 
                Mage::helper('adminhtml')->__('Gift  History'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Gift History'))
            ->_title($this->__('Gift History'));
        $this->_initAction()
            ->renderLayout();
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
        $fileName = 'gifthistory.csv';
        $content = $this->getLayout()
            ->createBlock('simigiftvoucher/adminhtml_gifthistory_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'gifthistory.xml';
        $content = $this->getLayout()
            ->createBlock('simigiftvoucher/adminhtml_gifthistory_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simigiftvoucher/gifthistory');
    }

}
