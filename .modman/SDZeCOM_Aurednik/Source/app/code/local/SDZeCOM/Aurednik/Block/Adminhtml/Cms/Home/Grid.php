<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

	/**
	 *
	 * @var GRID_ID
	 */
	const GRID_ID = 'cmsHomeGrid';

	/**
	 *
	 * @var MAIN_TABLE
	 */
	const MAIN_TABLE = 'main_table';

	//---------------------------------- public area ------------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 */
	public function __construct()
	{

		$this->setId(self :: GRID_ID);

		$this->setDefaultSort('id');

		$this->setSaveParametersInSession(true);

		$this->setDefaultDir('asc');

		$this->setUseAjax(true);

		parent:: __construct();

	}


	/**
	 * (non-PHPdoc)
	 * @see Mage_Adminhtml_Block_Widget_Grid::getRowUrl()
	 */
	public function getRowUrl($objRow)
	{
		return $this->getUrl('*/cms_edit/index', array(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID => $objRow->getId()));
	}


	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Mage_Adminhtml_Block_Widget_Grid::_prepareCollection()
	 */
	protected function _prepareCollection()
	{

		$objEntityModel = null;

		$objEntityModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

		$objEntityStoreModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store ();

		$objCollection = $objEntityModel->getCollection();

		$objEntityTypeModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type ();

		$strEntityTypeMainTable = $objEntityTypeModel->getResource()->getMainTable();

		$strEntityStoreMainTable = $objEntityStoreModel->getResource()->getMainTable();

		$objCollection
			->getSelect()
			->joinLeft(
				array($strEntityTypeMainTable => $strEntityTypeMainTable),
				self :: MAIN_TABLE . '.' . $objEntityModel:: TABLE_COLUMN_TYPE . '=' . $strEntityTypeMainTable . "." . SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type :: TABLE_COLUMN_ID,
				array("type_name" => $strEntityTypeMainTable . ".name"));

		$this->setCollection($objCollection);

		return parent:: _prepareCollection();

	}


	/**
	 * (non-PHPdoc)
	 * @see Mage_Adminhtml_Block_Widget_Grid::_prepareColumns()
	 */
	protected function _prepareColumns()
	{

		$objEntityTypeModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Type ();

		$strEntityTypeMainTable = $objEntityTypeModel->getResource()->getMainTable();

		$objEntityStoreModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store ();

		$strEntityStoreMainTable = $objEntityStoreModel->getResource()->getMainTable();

		$this->addColumn(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID, array(
			'header' => Mage:: helper('admin')->__('id'),
			'index' => SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID,
			'type' => 'number',
			'filter_index' => self :: MAIN_TABLE . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID,
			'align' => 'center',
		));

		$this->addColumn(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_NAME, array(
			'header' => Mage:: helper('admin')->__('name'),
			'align' => 'center',
			'width' => '50px',
			'index' => 'entity_name',
			'filter_index' => self :: MAIN_TABLE . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_NAME
		));

		$this->addColumn('entity_type', array(
			'header' => Mage:: helper('admin')->__('type'),
			'align' => 'center',
			'width' => '20px',
			'index' => 'type_name',
			'type' => 'options',
			'options' => $objEntityTypeModel->toBackendFilterOptionArray(),
			'filter_index' => $strEntityTypeMainTable . '.' . $objEntityTypeModel:: TABLE_COLUMN_NAME
		));


		$this->addColumn(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE, array(
			'header' => Mage:: helper('admin')->__('active'),
			'align' => 'center',
			'width' => '50px',
			'index' => SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE,
			'filter_index' => self :: MAIN_TABLE . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE
		));

		$this->addColumn(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_SORT, array(
			'header' => Mage:: helper('admin')->__('sort'),
			'align' => 'center',
			'width' => '20px',
			'index' => SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_SORT,
			'type' => 'text',
			'filter_index' => self :: MAIN_TABLE . '.' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_SORT,
		));


		$this->addColumn(SDZeCOM_Aurednik_Model_Cms_Home_Entity_STORE::TABLE_COLUMN_STORE_ID,
			array(
				'header' => Mage::helper('cms')->__('Store View'),
				'index' => SDZeCOM_Aurednik_Model_Cms_Home_Entity_STORE::TABLE_COLUMN_STORE_ID,
				'type' => 'store',
				'width' => '100px',
				'store_all' => true,
				'store_view' => true,
				'sortable' => false,
				'filter_condition_callback' => array($this, '_filterStoreCondition'),

			));


		return parent:: _prepareColumns();

	}


	/**
	 * (non-PHPdoc)
	 * @see Mage_Adminhtml_Block_Widget_Grid::_afterLoadCollection()
	 */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');
		parent:: _afterLoadCollection();
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * Filtert die Collection anhand der übergebenen Spalte im Grid
	 *
	 * @param SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity_Collection $collection
	 * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
	 *
	 * @return SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_Grid
	 */
	protected function _filterStoreCondition($collection, $column)
	{

		$value = $column->getFilter()->getValue();

		if (!$value)
		{
			return $this;
		}

		$this->getCollection()->addStoreFilter($value);
	}


	protected function _prepareMassaction()
	{

		$this->setMassactionIdField(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID);

		$this->getMassactionBlock()->setFormFieldName(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID);

		$this->getMassactionBlock()->addItem(
			'delete',
			array(
				'label' => Mage:: helper('admin')->__('Delete'),
				'url' => $this->getUrl('*/*/massDeleteEntity', array('' => '')),
				'confirm' => Mage:: helper('admin')->__('Are you sure?')
			));

		$this->getMassactionBlock()->addItem(
			'activate',
			array(
				'label' => Mage:: helper('admin')->__('Activate'),
				'url' => $this->getUrl('*/*/massSetEntityActiveStatus', array(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ACTIVE => 1)),
			));

		$this->getMassactionBlock()->addItem(
			'deactivate',
			array(
				'label' => Mage:: helper('admin')->__('Disable'),
				'url' => $this->getUrl('*/*/massSetEntityActiveStatus', array(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ACTIVE => 0)),
			));

		return $this;
	}


	//---------------------------------- private area -----------------------------------------------------
}
