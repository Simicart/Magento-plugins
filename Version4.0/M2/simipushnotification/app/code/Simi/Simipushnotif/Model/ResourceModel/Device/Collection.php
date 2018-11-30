<?php

/**
 * Connector Resource Collection
 */

namespace Simi\Simipushnotif\Model\ResourceModel\Device;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Simi\Simipushnotif\Model\Device', 'Simi\Simipushnotif\Model\ResourceModel\Device');
    }
}
