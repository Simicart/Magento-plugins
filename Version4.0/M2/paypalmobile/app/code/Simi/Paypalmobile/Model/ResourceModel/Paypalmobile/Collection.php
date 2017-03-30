<?php

/**
 * Connector Resource Collection
 */
namespace Simi\Paypalmobile\Model\ResourceModel\Paypalmobile;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Paypalmobile\Model\Paypalmobile', 'Simi\Paypalmobile\Model\ResourceModel\Paypalmobile');
    }
}
