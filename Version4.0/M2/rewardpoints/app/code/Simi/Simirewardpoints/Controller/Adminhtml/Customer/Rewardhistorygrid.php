<?php

namespace Simi\Simirewardpoints\Controller\Adminhtml\Customer;

class Rewardhistorygrid extends \Magento\Customer\Controller\Adminhtml\Index
{

    /**
     * Customer orders grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        $resultLayout = $this->resultLayoutFactory->create();
        return $resultLayout;
    }
}
