<?php
require_once 'HomeController.php';

/**
 *
 * @author sdz Aleksej Kniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Adminhtml_Cms_NewController extends SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
{

	/**
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_NewController
	 */
	protected function _initAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('cms');
		return $this;
	}


	/**
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_NewController
	 */
	public function indexAction()
	{
		$this->_initAction();

		$this->renderLayout();

		return $this;
	}
}