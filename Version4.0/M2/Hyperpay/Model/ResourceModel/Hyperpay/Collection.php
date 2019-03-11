<?php

/**
 * Connector Resource Collection
 */
namespace Simi\Hyperpay\Model\ResourceModel\Hyperpay;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Hyperpay\Model\Hyperpay', 'Simi\Hyperpay\Model\ResourceModel\Hyperpay');
    }
}
