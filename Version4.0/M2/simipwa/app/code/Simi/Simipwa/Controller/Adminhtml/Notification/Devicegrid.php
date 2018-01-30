<?php

namespace Simi\Simipwa\Controller\Adminhtml\Notification;

class Devicegrid extends \Simi\Simipwa\Controller\Adminhtml\Device\Grid
{

    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
