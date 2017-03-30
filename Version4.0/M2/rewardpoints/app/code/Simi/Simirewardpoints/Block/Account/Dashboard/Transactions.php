<?php

/**
 * Simirewardpoints Account Dashboard Recent Transactions
 *
 * @category    Simicart
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */

namespace Simi\Simirewardpoints\Block\Account\Dashboard;

class Transactions extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    public $_customerSession;

    /**
     * @var \Simi\Simirewardpoints\Model\TransactionFactory
     */
    public $_transactionFactory;

    /**
     * Transactions constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Simi\Simirewardpoints\Model\TransactionFactory $transactionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Simi\Simirewardpoints\Model\TransactionFactory $transactionFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_transactionFactory = $transactionFactory;
        parent::__construct($context, []);
    }

    protected function _construct()
    {
        parent::_construct();
        $customerId = $this->_customerSession->getCustomerId();
        $collection = $this->_transactionFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()->limit(5)
                ->order('created_time DESC');
        $collection->setOrder('transaction_id', 'DESC');
        $this->setCollection($collection);
    }
}
