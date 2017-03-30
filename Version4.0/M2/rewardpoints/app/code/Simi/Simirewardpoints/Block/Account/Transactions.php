<?php

/**
 * Simirewardpoints All Transactions
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Block\Account;

class Transactions extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Simi\Simirewardpoints\Model\ResourceModel\Transaction\Collection
     */
    protected $_transactionCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_modelSession;

    /**
     * Transactions constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Simi\Simirewardpoints\Model\ResourceModel\Transaction\Collection $transactionCollection
     * @param \Magento\Customer\Model\Session $modelSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Simi\Simirewardpoints\Model\ResourceModel\Transaction\Collection $transactionCollection,
        \Magento\Customer\Model\Session $modelSession,
        array $data
    ) {
        $this->_transactionCollection = $transactionCollection;
        $this->_modelSession = $modelSession;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $customerId = $this->_modelSession->getCustomerId();
        $collection = $this->_transactionCollection
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('created_time', 'DESC')
                ->setOrder('transaction_id', 'DESC');
        $this->setCollection($collection);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'transactions_pager')
                ->setCollection($this->getCollection());
        $this->setChild('transactions_pager', $pager);
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('transactions_pager');
    }
}
