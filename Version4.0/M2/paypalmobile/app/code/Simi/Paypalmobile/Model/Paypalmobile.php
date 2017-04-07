<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Simi\Paypalmobile\Model;



/**
 * Pay In Store payment method model
 */
class Paypalmobile extends \Magento\Framework\Model\AbstractModel
{

     /**
     * @var \Simi\Simiconnector\Helper\Website
     **/
    protected $_websiteHelper;
    protected $_tableresource;
    protected $_coreRegistry = null;
    
    public $simiObjectManager;
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Key $resource
     * @param ResourceModel\Key\Collection $resourceCollection
     * @param \Simi\Simiconnector\Helper\Website $websiteHelper
     * @param AppFactory $app
     * @param PluginFactory $plugin
     * @param DesignFactory $design
     * @param ResourceModel\App\CollectionFactory $appCollection
     * @param ResourceModel\Key\CollectionFactory $keyCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $tableresource,
        \Simi\Paypalmobile\Model\ResourceModel\Paypalmobile $resource,
        \Simi\Paypalmobile\Model\ResourceModel\Paypalmobile\Collection $resourceCollection
    )
    {
        
        $this->simiObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_tableresource = $tableresource;
        $this->_coreRegistry = $this->simiObjectManager->get('\Magento\Framework\Registry');

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Paypalmobile\Model\ResourceModel\Paypalmobile');
    }


    public function statusPending() {
        return array(
            'status' => 'PENDING',
        );
    }
    
    public function updatePaypalPaymentv2($dataComfrim){
        if ($dataComfrim->payment_status == '2') {
            //update status order-> cancel          
            $order = $this->setOrderCancel($dataComfrim->invoice_number);            
            return array('order' => $order, 'message' => __('The order has been cancelled'));            
        }
        $confirm_db = $dataComfrim->proof;
        $data = $this->simiObjectManager->get('Simi\Paypalmobile\Helper\Data')->getResponseBody($confirm_db, 0);
        $data['invoice_number'] = $dataComfrim->invoice_number;

        if ((isset($data['payment_status'] )&& $data['payment_status']== 'PENDING') 
            || 
            (!isset($data['transaction_id']) || !$data['transaction_id'])
            || (!isset($data['invoice_number']) || !$data['invoice_number'])) {
            return array('message' => $dataComfrim->payment_status);                
        }
        
        if ($order=$this->_initInvoicev2($data['invoice_number'], $data)){
            return array('order' => $order, 'message' => __('Thank you for your purchase!'));                            
        }else{
            return array('message' => 'The order has been pending');                
        }             
    }
    
    protected function _initInvoice($orderId, $data) {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order)
            return false;
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }

        $this->simiObjectManager->get('Simi\Simiconnector\Model\Paypalmobile')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('transaction_name', $data['fund_source_type'])
                ->setData('transaction_dis', $data['last_four_digits'])
                ->setData('transaction_email', $data['transaction_email'])
                ->setData('amount', $data['amount'])
                ->setData('currency_code', $data['currency_code'])
                ->setData('status', $data['payment_status'])
                ->setData('order_id', $order->getId())
                ->save();
        $this->simiObjectManager->get('\Magento\Checkout\Model\Session')->setOrderIdForEmail($order->getId());
        $orderService = $this->simiObjectManager->create('Magento\Sales\Api\InvoiceManagementInterface');
        $invoice = $orderService->prepareInvoice($order, $items);
        $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
        $invoice->setEmailSent(true)->register();
        
        
        $this->_coreRegistry->register('current_invoice', $invoice);
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = $this->simiObjectManager->get('Magento\Framework\DB\Transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
        $transactionSave->save();
        $this->simiObjectManager->get('\Magento\Checkout\Model\Session')->setOrderIdForEmail(null);
        return true;
    }

    protected function _initInvoicev2($orderId, $data) {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order){
            throw new \Simi\Simiconnector\Helper\SimiException(__('The order is not existed'), 4);
        }
            
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        try {
            $ppMobileModel = $this->simiObjectManager->get('Simi\Paypalmobile\Model\Paypalmobile')
                ->setData('transaction_id', $data['transaction_id'])
                ->setData('transaction_name', $data['fund_source_type'])
                ->setData('transaction_email', $data['transaction_email'])
                ->setData('amount', $data['amount'])
                ->setData('currency_code', $data['currency_code'])
                ->setData('status', $data['payment_status'])
                ->setData('order_id', $order->getId());
            if (isset($data['last_four_digits'])){
                $ppMobileModel->setData('transaction_dis', $data['last_four_digits']);
            }
            $ppMobileModel->save();
            $this->simiObjectManager->get('\Magento\Checkout\Model\Session')->setOrderIdForEmail($order->getId());
            
            $orderService = $this->simiObjectManager->create('Magento\Sales\Api\InvoiceManagementInterface');
            $invoice = $orderService->prepareInvoice($order, $items);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->setEmailSent(true)->register();
            //$invoice->setTransactionId();
            $this->_coreRegistry->register('current_invoice', $invoice);
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = $this->simiObjectManager->get('Magento\Framework\DB\Transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
            $transactionSave->save();
            $this->simiObjectManager->get('\Magento\Checkout\Model\Session')->setOrderIdForEmail(null);
        }catch(Exception $e){
            throw new \Simi\Simiconnector\Helper\SimiException(__('Unable to save the order'), 4);
        }        
        
        return $order;
    }

    protected $_order;

    protected function _getOrder($orderId) {
        if (is_null($this->_order)) {
            $this->_order = $this->simiObjectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
            if (!$this->_order->getId()) {
                throw new \Simi\Simiconnector\Helper\SimiException(__("Can not create invoice. Order was not found."));
            }
        }
        if (!$this->_order->canInvoice())
            return FALSE;
        return $this->_order;
    }

    protected function setOrderCancel($orderIncrementId) {
        $order = $this->simiObjectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($orderIncrementId);
        if ($order->getId()) {
            $order->cancel()->save();
        }
        return $order;
    }
}
