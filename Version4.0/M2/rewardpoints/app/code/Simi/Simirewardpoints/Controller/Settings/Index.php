<?php

namespace Simi\Simirewardpoints\Controller\Settings;

class Index extends \Simi\Simirewardpoints\Controller\AbstractAction
{

    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}
