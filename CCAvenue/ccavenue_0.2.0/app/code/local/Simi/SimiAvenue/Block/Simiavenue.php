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
 * @package     Magestore_SimiAvenue
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simiavenue Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiAvenue
 * @author      Magestore Developer
 */
class Simi_SimiAvenue_Block_Simiavenue extends Mage_Payment_Block_Info_Cc
{
	protected $_tranS;
    

    protected function _prepareSpecificInformation($transport = null) {
		// die("xxxxxxxx");
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
		
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $orderId = $invoice->getOrderId();
        } elseif (Mage::getSingleton('core/session')->getOrderIdForEmail()) {
            $orderId = Mage::getSingleton('core/session')->getOrderIdForEmail();
        }
        $train = Mage::getModel('simiavenue/simiavenue')->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->getLastItem();
        // $this->_tranS = $train;
        $info = null;
        $transport = parent::_prepareSpecificInformation($transport);
        if (count($train->getData())) {
            $info = array('EncResponse' => $train->getChecksum(),
					"Amount" => $train->getAmount(),
                    );
        } else {
            $info = array('Notice' => 'Pending');
        }

        return $transport->addData($info);
    }

    public function getCcTypeName() {
        return '';
    }
	
}