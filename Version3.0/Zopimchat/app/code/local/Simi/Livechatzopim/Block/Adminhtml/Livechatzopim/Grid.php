<?php

class Simi_Livechatzopim_Block_Adminhtml_Livechatzopim_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('livechatzopimGrid');
		$this->setDefaultSort('livechatzopim_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('livechatzopim/livechatzopim')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('livechatzopim_id', array(
			'header'	=> Mage::helper('livechatzopim')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'livechatzopim_id',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('livechatzopim')->__('Title'),
			'align'	 =>'left',
			'index'	 => 'title',
		));

		$this->addColumn('content', array(
			'header'	=> Mage::helper('livechatzopim')->__('Item Content'),
			'width'	 => '150px',
			'index'	 => 'content',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('livechatzopim')->__('Status'),
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
				'header'	=>	Mage::helper('livechatzopim')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('livechatzopim')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('livechatzopim')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('livechatzopim')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('livechatzopim_id');
		$this->getMassactionBlock()->setFormFieldName('livechatzopim');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('livechatzopim')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('livechatzopim')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('livechatzopim/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('livechatzopim')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('livechatzopim')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}