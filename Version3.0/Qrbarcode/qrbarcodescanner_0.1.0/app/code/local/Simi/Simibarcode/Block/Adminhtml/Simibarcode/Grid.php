<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Simibarcode
 * @copyright   Copyright (c) 2012 
 * @license     
 */

 /**
 * Simibarcode Grid Block
 * 
 * @category    
 * @package     Simibarcode
 * @author      Developer
 */
class Simi_Simibarcode_Block_Adminhtml_Simibarcode_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
    {
		parent::__construct();
		$this->setId('simibarcodeGrid');
		$this->setDefaultSort('barcode_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Simi_Simibarcode_Block_Adminhtml_Simibarcode_Grid
	 */
	protected function _prepareCollection()
    {
		$collection = Mage::getModel('simibarcode/simibarcode')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Simi_Simibarcode_Block_Adminhtml_Simibarcode_Grid
	 */
	protected function _prepareColumns()
    {
        $this->addColumn('barcode_id', array(
            'header'    => Mage::helper('simibarcode')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'barcode_id',
        ));

        $this->addColumn('barcode', array(
            'header'    => Mage::helper('simibarcode')->__('Barcode'),
            'align'     =>'left',
            'index'     => 'barcode',
        ));

        $this->addColumn('qrcode', array(
            'header'    => Mage::helper('simibarcode')->__('QRcode'),
            'align'     =>'left',
            'index'     => 'qrcode',
        ));

        $this->addColumn('product_name', array(
            'header'    => Mage::helper('simibarcode')->__('Product Name'),
            'align'     =>'left',
            'index'     => 'product_name',
        ));

        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('simibarcode')->__('Product Sku'),
            'align'     =>'left',
            'index'     => 'product_sku',
        ));
        
        $this->addColumn('created_date', array(
            'header'    => Mage::helper('simibarcode')->__('Created Date'),
            'align'     =>'left',
            'index'     => 'created_date',
            'type' => 'datetime'
        ));
        
        $this->addColumn('barcode_status', array(
            'header'    => Mage::helper('simibarcode')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'barcode_status',
            'type'        => 'options',
            'options'     => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('simibarcode')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('simibarcode')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simibarcode')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simibarcode')->__('XML'));

        return parent::_prepareColumns();
    }
	
	/**
	 * prepare mass action for this grid
	 *
	 * @return Simi_Simibarcode_Block_Adminhtml_Simibarcode_Grid
	 */
	protected function _prepareMassaction()
    {
		$this->setMassactionIdField('barcode_id');
		$this->getMassactionBlock()->setFormFieldName('simibarcode');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'     => Mage::helper('simibarcode')->__('Delete'),
            'url'       => $this->getUrl('*/*/massDelete'),
            'confirm'   => Mage::helper('simibarcode')->__('Are you sure?')
        ));

		$statuses = Mage::getSingleton('simibarcode/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('simibarcode')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('simibarcode')->__('Status'),
					'values'=> $statuses
				))
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