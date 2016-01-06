<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Simi_Simivideo
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Simi_Simivideo_Block_Adminhtml_Simivideo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('videoGrid');
        $this->setDefaultSort('video_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    
    protected function _prepareCollection() {
        $webId = 0;
        $collection = Mage::getModel('simivideo/simivideo')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('video_id', array(
            'header' => Mage::helper('simivideo')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'video_id',
        ));

        $this->addColumn('video_title', array(
            'header' => Mage::helper('simivideo')->__('Title'),
            'align' => 'left',
            'index' => 'video_title',
        ));

        $this->addColumn('video_url', array(
            'header' => Mage::helper('simivideo')->__('Video Key'),
            'width' => '550px',
            'index' => 'video_url',
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('simivideo')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('simivideo')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simivideo')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'video_id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Madapter_Block_Adminhtml_Madapter_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('video_id');
        $this->getMassactionBlock()->setFormFieldName('video_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simivideo')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simivideo')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('simivideo/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('simivideo')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('simivideo')->__('Status'),
                    'values' => $statuses
            ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        $webId = Mage::getBlockSingleton('connector/adminhtml_web_switcher')->getWebsiteId();
        return $this->getUrl('*/*/edit', array('video_id' => $row->getId()));
    }
}
