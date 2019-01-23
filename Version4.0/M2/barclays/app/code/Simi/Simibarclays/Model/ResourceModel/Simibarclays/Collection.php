<?php

/**
 * Simiconnector Resource Collection
 */

namespace Simi\Simibarclays\Model\ResourceModel\Simibarclays;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Simi\Simibarclays\Model\Simibarclays', 'Simi\Simibarclays\Model\ResourceModel\Simibarclays');
    }
}
