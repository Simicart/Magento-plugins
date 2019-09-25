<?php
namespace Simi\Simibraintree\Model;

/**
* 
*/
class Simibraintree extends \Magento\Framework\Model\AbstractModel
{
	
	public $simiObjectManager;
	public $orderRepository;
	public $invoiceService;
	public $transaction;
	function __construct(
		\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $simiObjectManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
	)
	{
		
		$this->simiObjectManager = $simiObjectManager;
		$this->orderRepository = $orderRepository;
		$this->invoiceService = $invoiceService;
		$this->transaction = $transaction;
		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	public function _construct()
    {

        $this->_init('Simi\Simibraintree\Model\ResourceModel\Simibraintree');
    }

	public function updateBraintreePayment($data){
		$helper = $this->simiObjectManager->get('Simi\Simibraintree\Helper\Data');
		$result = $helper->createTransaction($data);
        if (isset($result->success) && $result->success == 1) {
            $transaction = $result->transaction;
            $transaction = [
                "transaction_id" => $transaction->id,
                "status" => $transaction->status,
                "order_id" => $data->order_id,
                "type" => $transaction->type,
                "is_closed" => "0",
                "currency_code" => $transaction->currencyIsoCode,
                "amount" => $transaction->amount,
                "merchant_id" => $transaction->merchantAccountId,
                'additional_data'=>$this->_prepareAdditionalData($transaction)
            ];
            if ($this->_initInvoice($data->order_id, $transaction)) {
                return '';
            }
        }
        else {
            return $result->message;
        }
        return __('Update Transaction Failed');
	}

	public function _initInvoice($orderId,$transactionData){
		$order = $this->simiObjectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($orderId);
		$items = [];
        if (!$order)
            return false;
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
        	->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
        	->save();

        $transactionModel = $this->simiObjectManager->create('Simi\Simibraintree\Model\Simibraintree');

        $transactionModel->setData('transaction_id', $transactionData['transaction_id'])
		                ->setData('transaction_name', 'Braintree')
		                ->setData('transaction_email', $order->getData('customer_email'))
		                ->setData('amount', $transactionData['amount'])
		                ->setData('currency_code', $transactionData['currency_code'])
		                ->setData('status', $transactionData['status'])
		                ->setData('order_id', $order->getId())
		                ->setData('additional_data',$transactionData['additional_data'])
		                ->save()
        			;
        return true;

	}

	public function _prepareAdditionalData($transaction){
		if($transaction->creditCardDetails){
			$info = $transaction->creditCardDetails;
			$cardDetail = [
				'cardType' =>$info->cardType,
				'ccLast4' =>$info->last4
			];

			return json_encode($cardDetail);
		}

		return '';
	}
}