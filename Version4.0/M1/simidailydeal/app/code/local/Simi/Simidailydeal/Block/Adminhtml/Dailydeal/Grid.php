<?php

class Simi_Simidailydeal_Block_Adminhtml_Dailydeal_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('dailydealGrid');
		$this->setDefaultSort('dailydeal_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('simidailydeal/dailydeal')->getCollection()
			->addFieldToFilter('is_random', '0');;
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id', array(
			'header' => $this->__('ID'),
			'align'  => 'right',
			'width'  => '50px',
			'index'  => 'id',
		));

		$this->addColumn('title', array(
			'header' => $this->__('Deal Name'),
			'align'  => 'left',
			'index'  => 'title',
            'width'    => '200px'
		));

        $this->addColumn('product_id', array(
            'header'   => $this->__('Product ID'),
            'width'    => '50px',
            //'renderer' => 'simidailydeal/adminhtml_dailydeal_renderer_product',
            'index'    => 'product_id',
        ));

		$this->addColumn('product_name', array(
			'header'   => $this->__('Product Name'),
			'width'    => '250px',
			'renderer' => 'simidailydeal/adminhtml_dailydeal_renderer_product',
			'index'    => 'product_name',
		));

        $this->addColumn('product_price', array(
            'header'   => $this->__('Product Price'),
            'width'    => '100px',
            'renderer' => 'simidailydeal/adminhtml_dailydeal_renderer_productprice',
            //'index'    => 'product_name',
            'type'          => 'currency',
            'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

		$this->addColumn('save', array(
			'header'   => $this->__('Discount'),
			'width'    => '100px',
			'index'    => 'save',
			'renderer' => 'simidailydeal/adminhtml_dailydeal_renderer_save',
			'type'     => 'number',
		));
		$this->addColumn('deal_price', array(
			'header'        => $this->__('Deal Price'),
			'width'         => '100px',
			'index'         => 'deal_price',
			'type'          => 'currency',
			'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
		));
		$this->addColumn('quantity', array(
			'header' => $this->__('Deal Qty'),
			'width'  => '100px',
			'index'  => 'quantity',
			'type'   => 'number',
		));
		$this->addColumn('sold', array(
			'header' => $this->__('Sold'),
			'width'  => '150px',
			'index'  => 'sold',
			'type'   => 'number',
		));

		$this->addColumn('start_time', array(
			'header' => $this->__('Start Time'),
			'width'  => '150px',
			'index'  => 'start_time',
			'type'   => 'datetime',
		));
		$this->addColumn('close_time', array(
			'header' => $this->__('End Time'),
			'width'  => '150px',
			'index'  => 'close_time',
			'type'   => 'datetime',
		));


		$this->addColumn('status', array(
			'header'  => $this->__('Status'),
			'align'   => 'left',
			'width'   => '80px',
			'index'   => 'status',
			'type'    => 'options',
			'options' => array(
				1 => 'Schedule',
				3 => 'Active',
				4 => 'Expired',
				2 => 'Disable',
			),
		));

		$this->addColumn('action',
			array(
				'header'    => $this->__('Action'),
				'width'     => '50',
				'type'      => 'action',
				'getter'    => 'getId',
				'actions'   => array(
					array(
						'caption' => $this->__('Edit'),
						'url'     => array('base' => '*/*/edit'),
						'field'   => 'id'
					)),
				'filter'    => false,
				'sortable'  => false,
				'index'     => 'stores',
				'is_system' => true,
			));

		$this->addExportType('*/*/exportCsv', $this->__('CSV'));
		$this->addExportType('*/*/exportXml', $this->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('simidailydeal');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'   => $this->__('Delete'),
			'url'     => $this->getUrl('*/*/massDelete'),
			'confirm' => $this->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('simidailydeal/status')->getOptionArray();

		array_unshift($statuses, array('label' => '', 'value' => ''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'      => $this->__('Change status'),
			'url'        => $this->getUrl('*/*/massStatus', array('_current' => true)),
			'additional' => array(
				'visibility' => array(
					'name'   => 'status',
					'type'   => 'select',
					'class'  => 'required-entry',
					'label'  => $this->__('Status'),
					'values' => $statuses
				))
		));

		return $this;
	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}