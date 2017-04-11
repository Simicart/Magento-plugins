<?php

/**
 * Connector Resource Collection
 */
namespace Simi\Simicheckoutcom\Model\ResourceModel\Simicheckoutcom;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Simi\Simicheckoutcom\Model\Simicheckoutcom', 'Simi\Simicheckoutcom\Model\ResourceModel\Simicheckoutcom');
    }
}
