<?php

namespace Simi\Paypalmobile\Model\ResourceModel;

/**
 * Connector Resource Model
 */
class Paypalmobile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('paypalmobile', 'paypalmobile_id');
    }
}
