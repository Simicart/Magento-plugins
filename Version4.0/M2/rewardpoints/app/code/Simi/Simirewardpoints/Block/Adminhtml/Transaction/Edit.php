<?php

namespace Simi\Simirewardpoints\Block\Adminhtml\Transaction;

/**
 * Form containerEdit
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'reward_id';
        $this->_blockGroup = 'Simi_Simirewardpoints';
        $this->_controller = 'adminhtml_transaction';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Transaction'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->add(
            'saveandcontinue',
            [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
            ],
                ],
            -100
        );
    }
}
