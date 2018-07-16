<?php

/**
 *
 * @author egutsche
 *
 */
class SDZeCOM_Aurednik_Block_Adminhtml_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid
{
	/**
	 * (non-PHPdoc)
	 * @see Mage_Adminhtml_Block_Newsletter_Subscriber_Grid::_prepareColumns()
	 */
	protected function _prepareColumns()
	{
		parent::_prepareColumns();

		$this->removeColumn('firstname');
		$this->removeColumn('lastname');


		$this->addColumnAfter('subscriber_firstname', array(
			'header' => Mage::helper('newsletter')->__('First Name'),
			'index' => 'subscriber_firstname',
			'filter_index' => 'subscriber_firstname',
			'type' => 'varchar',
			'align' => 'left',
			'default' => '----'
		), 'Email');

		$this->addColumnAfter('subscriber_lastname', array(
			'header' => Mage::helper('newsletter')->__('Last Name'),
			'index' => 'subscriber_lastname',
			'filter_index' => 'subscriber_lastname',
			'type' => 'varchar',
			'align' => 'left',
			'default' => '----'
		), 'subscriber_firstname');

		$this->addColumnAfter('subscriber_company', array(
			'header' => Mage:: helper('admin')->__('Company'),
			'index' => 'subscriber_company',
			'type' => 'varchar',
			'filter_index' => 'subscriber_company',
			'align' => 'left',
			'default' => '----'
		), 'subscriber_lastname');

	}
}
