<?php

namespace Simi\Simipushnotif\Controller\Adminhtml\Notification;

class Devicegrid extends \Simi\Simipushnotif\Controller\Adminhtml\Device\Grid
{

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
