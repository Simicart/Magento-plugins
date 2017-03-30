<?php

namespace Simi\Simirewardpoints\Controller\Adminhtml\Managepointbalances;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Import extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Simi_Simirewardpoints::Manage_Point_Balances');
        $resultPage->addBreadcrumb(__('Import Points'), __('Import Points'));
        $resultPage->addBreadcrumb(__('Point Balance'), __('Point Balance'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import Points'));

        return $resultPage;
    }

    /**
     * Is the user allowed to view the blog post grid.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Simi_Simirewardpoints::Manage_Point_Balances');
    }
}
