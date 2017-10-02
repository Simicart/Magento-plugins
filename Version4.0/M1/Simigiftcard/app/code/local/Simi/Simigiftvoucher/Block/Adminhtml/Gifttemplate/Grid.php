<?php
/**
 * Simi
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Simicart.com license that is
 * available through the world-wide-web at this URL:
 * http://www.Simicart.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Simigiftvoucher
 * @module     Giftvoucher
 * @author      Simi Developer
 *
 * @copyright   Copyright (c) 2016 Simi (http://www.Simicart.com/)
 * @license     http://www.Simicart.com/license-agreement.html
 *
 */

/**
 * Class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Grid
 */
class Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /**
     * Simi_Simigiftvoucher_Block_Adminhtml_Gifttemplate_Grid constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setId('gifttemplateGrid');
        $this->setDefaultSort('giftcard_template_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('simigiftvoucher/gifttemplate')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns() {
        $this->addColumn('giftcard_template_id', array(
            'header' => Mage::helper('simigiftvoucher')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'giftcard_template_id',
        ));
        $this->addColumn('template_name', array(
            'header' => Mage::helper('simigiftvoucher')->__('Template Name'),
            'align' => 'left',
            'index' => 'template_name',
        ));
        if (!Mage::helper('simigiftvoucher')->isShowOnlySimpleTemplate()) {
            $this->addColumn('design_pattern', array(
                'header' => Mage::helper('simigiftvoucher')->__('Template Design'),
                'align' => 'left',
                'index' => 'design_pattern',
                'type' => 'options',
                'options' => Mage::getSingleton('simigiftvoucher/designpattern')->getOptionArray(),
                'width' => '80px',
            ));
            $this->addColumn('caption', array(
                'header' => Mage::helper('simigiftvoucher')->__('Title'),
                'align' => 'left',
                'index' => 'caption',
            ));
        }

        $this->addColumn('style_color', array(
            'header' => Mage::helper('simigiftvoucher')->__('Style Color'),
            'align' => 'left',
            'index' => 'style_color'
        ));
        $this->addColumn('text_color', array(
            'header' => Mage::helper('simigiftvoucher')->__('Text Color'),
            'align' => 'left',
            'index' => 'text_color'
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('simigiftvoucher')->__('Status'),
            'align' => 'left',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('simigiftvoucher/statusgifttemplate')->getOptionArray(),
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('simigiftvoucher')->__('Action'),
            'width' => '70px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('simigiftvoucher')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simigiftvoucher')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simigiftvoucher')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('giftcard_template_id');
        $this->getMassactionBlock()->setFormFieldName('gifttemplate');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('simigiftvoucher')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('simigiftvoucher')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('simigiftvoucher/statusgifttemplate')->getOptionHash();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('simigiftvoucher')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('simigiftvoucher')->__('Status'),
                    'values' => $statuses
                )
            )
        ));



        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
