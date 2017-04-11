<?php

namespace Simi\Simicheckoutcom\Model;

class Simicheckoutcom extends \Magento\Framework\Model\AbstractModel
{

    public $quote;
    public $simiObjectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $tableresource,
        \Simi\Simicheckoutcom\Model\ResourceModel\Simicheckoutcom $resource,
        \Simi\Simicheckoutcom\Model\ResourceModel\Simicheckoutcom\Collection $resourceCollection,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager
    )
    {
        $this->simiObjectManager = $simiObjectManager;
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
        $this->_init('Simi\Simicheckoutcom\Model\ResourceModel\Simicheckoutcom');
    }
    
    public function updatePayment($dataComfirm) {
        $data = array();
        $data['invoice_number'] = $dataComfirm->invoice_number;
        if ($this->_initInvoice($data['invoice_number'], $data)) {
            $information = $this->statusSuccess();
            $information['message'] = __('Thank you for your purchase!');
            return $information;
        } else {
            $information['message'] = __('Cannot Create Invoice');
            return $information;
        }
    }
    
    public function statusSuccess() {
        return array('status' => 'SUCCESS',
            'message' => array('SUCCESS'),           
        );
    }
    
    protected function _initInvoice($orderId, $data) {
        $items = array();
        $order = $this->_getOrder($orderId);
        if (!$order){
            throw new \Simi\Simiconnector\Helper\SimiException(__('The order is not existed'), 4);
        }
        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }
        try {
            $paymentModel = $this->simiObjectManager->get('Simi\Paypalmobile\Model\Paypalmobile')
                ->setData('transaction_name', '')
                ->setData('transaction_email', '')
                ->setData('currency_code', '')
                ->setData('order_id', $order->getId());
            if (isset($data['transaction_id'])){
                $paymentModel->setData('transaction_id', $data['transaction_id']);
            }
            if (isset($data['payment_status'])){
                $paymentModel->setData('status', $data['payment_status']);
            }
            $paymentModel->save();
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
}
