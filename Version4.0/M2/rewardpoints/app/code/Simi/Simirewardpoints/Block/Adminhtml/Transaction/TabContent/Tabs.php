<?php

namespace Simi\Simirewardpoints\Block\Adminhtml\Transaction\TabContent;

/**
 * Tabs containerTabs
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('general_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Transaction Information'));
    }

    /**
     * Preparing global layout.
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }
}
