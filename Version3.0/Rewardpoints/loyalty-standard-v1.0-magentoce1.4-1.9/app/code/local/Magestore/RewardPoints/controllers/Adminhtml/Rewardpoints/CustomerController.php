<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Rewardpoints
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Rewardpoints Admin Controller
 * 
 * @category    
 * @package     Rewardpoints
 * @author      Developer
 */
class Magestore_RewardPoints_Adminhtml_Rewardpoints_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'rewardpoints_history.csv';
        $content    = $this->getLayout()
                           ->createBlock('rewardpoints/adminhtml_customer_edit_tab_history')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'rewardpoints_history.xml';
        $content    = $this->getLayout()
                           ->createBlock('rewardpoints/adminhtml_customer_edit_tab_history')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints');
    }
}
