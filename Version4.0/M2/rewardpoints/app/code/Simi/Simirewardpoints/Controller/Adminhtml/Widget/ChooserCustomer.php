<?php

namespace Simi\Simirewardpoints\Controller\Adminhtml\Widget;

class ChooserCustomer extends \Magento\Backend\App\Action
{

    /**
     * Prepare block for chooser
     *
     * @return void
     */
    public function execute()
    {

        $block = $this->_view->getLayout()->createBlock(
            'Simi\Simirewardpoints\Block\Adminhtml\Transaction\Widget\ChooserCustomer',
            'simnrewardpoints_widget_chooser_customer',
            ['data' => ['js_form_object' => $this->getRequest()->getParam('form')]]
        );
        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }
}
