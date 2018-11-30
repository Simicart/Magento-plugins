<?php
/**
 * Created by PhpStorm.
 * User: macos
 * Date: 11/16/18
 * Time: 1:54 PM
 */

namespace Simi\Simipushnotif\Block\Adminhtml;


class Notification extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_notification';
        $this->_blockGroup = 'Simi_Simipushnotif';
        $this->_headerText = __('Notification');
        $this->_addButtonLabel = __('Add New Notification');
        parent::_construct();
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