<?php

namespace Simi\Simipushnotif\Controller\Adminhtml\Notification;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * Customer grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Simi_Simipushnotif::notification_manager');
    }
}
