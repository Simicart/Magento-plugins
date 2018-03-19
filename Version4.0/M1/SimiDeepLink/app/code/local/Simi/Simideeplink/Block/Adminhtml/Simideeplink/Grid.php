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
 * @package     Magestore_Simideeplink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simideeplink Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Simideeplink
 * @author      Magestore Developer
 */
class Simi_Simideeplink_Block_Adminhtml_Simideeplink_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simideeplinkGrid');
        $this->setDefaultSort('simideeplink_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Simi_Simideeplink_Block_Adminhtml_Simideeplink_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('simideeplink/simideeplink')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Simi_Simideeplink_Block_Adminhtml_Simideeplink_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('simideeplink_id', array(
            'header'    => Mage::helper('simideeplink')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'simideeplink_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('simideeplink')->__('Title'),
            'align'     =>'left',
            'width'     => '150px',
            'index'     => 'title',
        ));

        $this->addColumn('link', array(
            'header'    => Mage::helper('simideeplink')->__('Link'),

            'index'     => 'link',
        ));

        $this->addColumn('', array(
            'header'    => Mage::helper('simideeplink')->__('Type'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'type',
            'type'        => 'options',
            'options'     => array(
                1 => 'Product',
                2 => 'Category',
                3 => 'CMS',
            ),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simideeplink')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simideeplink')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Simi_Simideeplink_Block_Adminhtml_Simideeplink_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('simideeplink_id');
        $this->getMassactionBlock()->setFormFieldName('simideeplink');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('simideeplink')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('simideeplink')->__('Are you sure?')
        ));

        return $this;
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}