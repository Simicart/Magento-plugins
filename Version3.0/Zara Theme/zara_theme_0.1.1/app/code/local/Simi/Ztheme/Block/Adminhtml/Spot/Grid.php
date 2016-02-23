<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Ztheme
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Ztheme Grid Block
 * 
 * @category    
 * @package     Ztheme
 * @author      Developer
 */
class Simi_Ztheme_Block_Adminhtml_Spot_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('SpotGrid');
        $this->setDefaultSort('spotproduct_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ztheme/spotproduct')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    
    
    protected function _prepareColumns()
    {
        $this->addColumn('spotproduct_id', array(
            'header'    => Mage::helper('ztheme')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'spotproduct_id',
        ));
        $this->addColumn('position', array(
            'header'    => Mage::helper('ztheme')->__('Position'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'position',
        ));
        $this->addColumn('spotproduct_name', array(
            'header'    => Mage::helper('ztheme')->__('Name'),
            'align'     =>'left',
            'index'     => 'spotproduct_name',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('ztheme')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('ztheme')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('ztheme')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        return parent::_prepareColumns();
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