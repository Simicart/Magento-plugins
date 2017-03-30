<?php

/**
 * Action Abstract Model to Change Points on Reward Points system
 *
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simicart Developer
 */

namespace Simi\Simirewardpoints\Model\Action;

abstract class AbstractAction extends \Magento\Framework\DataObject
{

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_transaction;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Simi\Simirewardpoints\Helper\Data
     */
    protected $_helper;

    /**
     * Invoice constructor.
     * @param \Simi\Simirewardpoints\Model\TransactionFactory $transaction
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Simi\Simirewardpoints\Helper\Data $helper,
        \Simi\Simirewardpoints\Model\TransactionFactory $transaction,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_helper = $helper;
        $this->_transaction = $transaction;
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Action Code
     *
     * @var string
     */
    protected $_code = null;

    /**
     * get action code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * set action code
     *
     * @param string $value
     * @return \Simi\Simirewardpoints\Model\Action\Abstract
     */
    public function setCode($value)
    {
        $this->_code = $value;
        return $this;
    }

    /**
     * get HTML Title for action depend on current transaction
     *
     * @param \Simi\Simirewardpoints\Model\Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null)
    {
        return $this->getTitle();
    }

    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     *
     * @return \Simi\Simirewardpoints\Model\Action\Abstract
     */
    public function prepareTransaction()
    {
        return $this;
    }

    /**
     * Calculate Expiration Date for transaction
     *
     * @param int $days Days to be expired
     * @return null|string
     */
    public function getExpirationDate($days = 0)
    {
        if ($days <= 0) {
            return null;
        }
        $timestamp = time() + $days * 86400;
        return date('Y-m-d H:i:s', $timestamp);
    }
}
