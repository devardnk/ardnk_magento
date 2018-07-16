<?php
require_once 'HomeController.php';

/**
 *
 * @author sdz Aleksej Kniss
 *
 * @version 1.0
 *
 */
class SDZeCOM_Aurednik_Adminhtml_Cms_EditController extends SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
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


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Speichert einen neuen Banner
	 *
	 * @return Mage_Adminhtml_Controller_Action
	 */
	public function saveAction()
	{

		$arrPost = $this->getRequest()->getPost();

		if (!isset ($arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_DATA]) ||
			!isset ($arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_DATA] [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID]) ||
			!isset ($arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_ATTRIBUTE_DATA])
		)
		{

			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while saving') . $objEx->getMessage());

			$this->redirect('*/cms_home/index', true);
		}

		$intEntityModel = $this->saveEntity($arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_DATA], $arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_DATA] [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID]);

		if (!$intEntityModel)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while saving'));

			$this->redirect('*/cms_home/index', true);
		}

		$arrFiles = $this->getUploadFiles();

		$this->saveFiles($intEntityModel->getId(), $arrFiles);

		$this->saveEntityAttributeValues($intEntityModel->getId(), $arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_ATTRIBUTE_DATA], $arrFiles);

		Mage:: getSingleton('core/session')->addSuccess(Mage:: helper('aurednik')->__('successfully saved'));

		$this->redirect('*/cms_home/index', true);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Löscht einen vorhandenen Banner
	 *
	 */
	public function deleteAction()
	{

		$intDeleteId = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID);

		if (is_null($intDeleteId) || $intDeleteId <= 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred entity id is null or empty'));

			$this->redirect('*/cms_home/index', true);
		}

		$objEntityModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

		$objEntityModel->load($intDeleteId);

		if (is_null($objEntityModel->getId()))
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred can not load entity'));

			$this->redirect('*/cms_home/index', true);
		}

		try
		{
			$objEntityModel->delete();
			$this->deleteExistingFiles($intDeleteId);

		}
		catch (Exception $objE)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while delete entity'));

			$this->redirect('*/cms_home/index', true);
		}

		Mage:: getSingleton('core/session')->addSuccess(Mage:: helper('aurednik')->__('successfully delete entity'));

		$this->redirect('*/cms_home/index', true);
	}
}