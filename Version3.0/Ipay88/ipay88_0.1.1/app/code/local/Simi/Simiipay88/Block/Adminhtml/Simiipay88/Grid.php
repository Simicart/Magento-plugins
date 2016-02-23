<?php
/**
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	
 * @package 	Simiipay88
 * @copyright 	Copyright (c) 2012 
 * @license 	
 */

 /**
 * Simiipay88 Grid Block
 * 
 * @category 	
 * @package 	Simiipay88
 * @author  	Developer
 */
class Simi_Simiipay88_Block_Adminhtml_Simiipay88_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('simiipay88Grid');
		$this->setDefaultSort('simiipay88_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Simi_Simiipay88_Block_Adminhtml_Simiipay88_Grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('simiipay88/simiipay88')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Simi_Simiipay88_Block_Adminhtml_Simiipay88_Grid
	 */
	protected function _prepareColumns(){
		$this->addColumn('simiipay88_id', array(
			'header'	=> Mage::helper('simiipay88')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'simiipay88_id',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('simiipay88')->__('Title'),
			'align'	 =>'left',
			'index'	 => 'title',
		));

		$this->addColumn('content', array(
			'header'	=> Mage::helper('simiipay88')->__('Item Content'),
			'width'	 => '150px',
			'index'	 => 'content',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('simiipay88')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Enabled',
				2 => 'Disabled',
			),
		));

		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('simiipay88')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('simiipay88')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('simiipay88')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('simiipay88')->__('XML'));

		return parent::_prepareColumns();
	}
	
	/**
	 * prepare mass action for this grid
	 *
	 * @return Simi_Simiipay88_Block_Adminhtml_Simiipay88_Grid
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('simiipay88_id');
		$this->getMassactionBlock()->setFormFieldName('simiipay88');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('simiipay88')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('simiipay88')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('simiipay88/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('simiipay88')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('simiipay88')->__('Status'),
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
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}