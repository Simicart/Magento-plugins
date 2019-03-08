<?php

namespace Simi\Hyperpay\Model\ResourceModel;

/**
 * Connector Resource Model
 */
class Hyperpay extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('hyperpay', 'hyperpay_id');
    }
}
