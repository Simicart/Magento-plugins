<?php

/**
 * Simiconnector Resource Collection
 */

namespace Simi\Simibraintree\Model\ResourceModel\Simibraintree;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Simi\Simibraintree\Model\Simibraintree', 'Simi\Simibraintree\Model\ResourceModel\Simibraintree');
    }
}
