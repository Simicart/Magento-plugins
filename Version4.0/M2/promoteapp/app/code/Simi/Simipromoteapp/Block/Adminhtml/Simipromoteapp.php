<?php

/**
 * Adminhtml connector list block
 *
 */

namespace Simi\Simipromoteapp\Block\Adminhtml;

class Simipromoteapp extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller     = 'adminhtml_simipromoteapp';
        $this->_blockGroup     = 'Simi_Simipromoteapp';
        $this->_headerText     = __('Reports');
        parent::_construct();
        $this->buttonList->remove('add');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function _isAllowedAction($resourceId)
    {
        return true;
    }
}
