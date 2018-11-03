<?php

namespace Simi\Simibraintree\Model\ResourceModel;

/**
 * Simiconnector Resource Model
 */
class Simibraintree extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('simibraintree', 'braintree_id');
    }
}
