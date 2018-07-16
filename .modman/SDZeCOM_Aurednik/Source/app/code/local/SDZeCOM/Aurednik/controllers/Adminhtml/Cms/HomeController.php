<?php

/**
 *
 * @author akniss
 *
 */
class SDZeCOM_Aurednik_Adminhtml_Cms_HomeController extends Mage_Adminhtml_Controller_Action
{

	//---------------------------------- public area -----------------------------------------------------
	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 * Laden des layout XML, setzen des aktiven Menue-Eintrages, Breadcrumb aufbauen
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
	 */
	protected function _initAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('cms');
		$this->renderLayout();

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 * Laden des layout XML, setzen des aktiven Menue-Eintrages, Breadcrumb aufbauen
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
	 */
	public function indexAction()
	{

		$this->_initAction();

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
			!isset ($arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_ATTRIBUTE_DATA])
		)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while saving') . $objEx->getMessage());

			$this->redirect('*/cms_home/index', true);

		}

		$intEntityModel = $this->saveEntity($arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_DATA]);

		if (!$intEntityModel)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while saving'));

			$this->redirect('*/cms_home/index', true);
		}

		$arrFiles = $this->getUploadFiles();

		$this->saveFiles($intEntityModel->getId(), $arrFiles);

		$this->saveEntityAttributeValues($intEntityModel->getId(), $arrPost [SDZeCOM_Aurednik_Block_Adminhtml_Cms_Home_New_Form_Tab_Form :: POST_ENTITY_ATTRIBUTE_DATA]);

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
	 * Setzt den Status der Banners | Highlight auf 'Aktiv' oder 'Inaktiv'
	 *
	 */
	public function massSetEntityActiveStatusAction()
	{

		$entityIds = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID);

		$status = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ACTIVE);

		if (is_array($entityIds) || count($entityIds) == 0)
		{

			foreach ($entityIds as $current)
			{
				$entityModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

				$entityModel->load($current);

				$entityModel->setActive($status);

				try
				{
					$entityModel->save();
				}
				catch (Exception $e)
				{
					Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while saving entity'));

					$this->redirect('*/cms_home/index', true);
				}

			}
		}

		Mage:: getSingleton('core/session')->addSuccess(Mage:: helper('aurednik')->__('entities successfully updated'));

		$this->redirect('*/cms_home/index', true);
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access public
	 *
	 * Löscht Enitities
	 *
	 */
	public function massDeleteEntityAction()
	{

		$entityIds = $this->getRequest()->getParam(SDZeCOM_Aurednik_Model_Cms_Home_Entity::TABLE_COLUMN_ID);

		if (is_array($entityIds) || count($entityIds) == 0)
		{

			foreach ($entityIds as $current)
			{
				$entityModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

				$entityModel->load($current);

				try
				{
					$entityModel->delete();
					$this->deleteExistingFiles($current);
				}
				catch (Exception $e)
				{
					Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error Occurred while delete entity'));

					$this->redirect('*/cms_home/index', true);
				}

			}
		}

		Mage:: getSingleton('core/session')->addSuccess(Mage:: helper('aurednik')->__('entities successfully deleted'));

		$this->redirect('*/cms_home/index', true);
	}

	//---------------------------------- protected area ---------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Lädt eine Datei auf den Server hoch
	 *
	 * @param int $intEntityId
	 *
	 * @param int $arrFile
	 *
	 * @return $strPath den Dateipfad der hochgeladenen Datei | Mage_Adminhtml_Controller_Action
	 */
	protected function uploadFile($intEntityId, $arrFile)
	{

		$strPath = SDZeCOM_Library_Helper_Directory:: joinPaths(
			Mage:: getBaseDir('media'),
			Mage:: getStoreConfig(SDZeCOM_Aurednik_Helper_Data::CMS_HOME_UPLOAD_IMAGE_FILE_PATH),
			$intEntityId);

		$arrFiles = glob($strPath . "/*.*");

		if (is_array($arrFiles) && count($arrFiles) > 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error: Delete the existing file first'));

			$this->redirect('*/cms_edit/index/' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID . '/' . $intEntityId, true);
		}

		$strFileName = time() . "." . pathinfo($arrFile ['name'], PATHINFO_EXTENSION);

		try
		{
			$objUploader = new Varien_File_Uploader ($arrFile);

			$objUploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));

			$objUploader->save($strPath, $strFileName);
		}
		catch (Exception $objEx)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred when uploading a file message : ') . $objEx->getMessage());

			$this->redirect('*/cms_edit/index/' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID . '/' . $intEntityId, true);
		}
		$strPath = SDZeCOM_Library_Helper_Directory:: joinPaths($strPath, $strFileName);

		if (!file_exists($strPath))
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error uploaded file does not exist'));

			$this->redirect('*/cms_edit/index/' . SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ID . '/' . $intEntityId, true);
		}

		$strPath = SDZeCOM_Library_Helper_Directory:: joinPaths(
			Mage:: getStoreConfig(SDZeCOM_Aurednik_Helper_Data::CMS_HOME_UPLOAD_IMAGE_FILE_PATH),
			$intEntityId,
			$strFileName);

		return $strPath;

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Löscht eine Datei
	 *
	 * @param int $intValueId , Value Id
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
	 */
	protected function deleteFile($intValueId)
	{

		if (is_null($intValueId) || $intValueId <= 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred entity attribute value id is null or empty'));

			$this->redirect('*/cms_home/index', true);
		}

		$objEntityAttrValue = new SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values ();

		$objEntityAttrValue->load($intValueId);

		if (is_null($objEntityAttrValue->getId()))
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred entity attribute value can not load'));

			$this->redirect('*/cms_home/index', true);
		}

		$strFilePath = SDZeCOM_Library_Helper_Directory:: joinPaths(
			Mage:: getBaseDir('media'),
			$objEntityAttrValue->getAttribute_value());

		if (!file_exists($strFilePath))
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred can not delete file'));

			$this->redirect('*/cms_home/index', true);
		}

		unlink($strFilePath);

		return $this;

	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Speichert eine neuen Banner | Highlight usw. SDZeCOM_Aurednik_Model_Cms_Home_Entity
	 *
	 * @param array $arrData , Enitity Daten
	 *
	 * @param $intEntityId , EnitityId
	 *
	 * @return SDZeCOM_Aurednik_Model_Cms_Home_Entity
	 */
	protected function saveEntity($arrData, $intEntityId = 0)
	{

		if (!is_array($arrData) || count($arrData) == 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('no data to save cms home entity'));

			return $this->redirect('*/cms_home/index', true);
		}

		if (!isset ($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE]))
		{

			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('data error to save cms home entity'));

			return $this->redirect('*/cms_home/index', true);
		}

		$objEntityModel = new SDZeCOM_Aurednik_Model_Cms_Home_Entity ();

		if ($intEntityId > 0)
		{
			$objEntityModel->load($intEntityId);
		}

		if (isset ($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE]))
		{
			$objEntityModel->setType_id(( int )$arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_TYPE]);
		}

		if (isset ($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_SORT]))
		{
			$objEntityModel->setSort(( int )$arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_SORT]);
		}

		if (isset ($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_NAME]))
		{
			$objEntityModel->setEntity_name($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_NAME]);
		}


		if (isset ($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store :: TABLE_COLUMN_STORE_ID]) && count($arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store :: TABLE_COLUMN_STORE_ID]) > 0)
		{
			$objEntityModel->setData(SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity::ENTITY_STORES, $arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity_Store :: TABLE_COLUMN_STORE_ID]);
		}
		else
		{
			$objEntityModel->setData(SDZeCOM_Aurednik_Model_Resource_Cms_Home_Entity::ENTITY_STORES, array(SDZeCOM_Aurednik_Model_Cms_Home_Entity::ALL_STORE_VIEWS_ID));
		}

		$objEntityModel->setActive(( int )$arrData [SDZeCOM_Aurednik_Model_Cms_Home_Entity :: TABLE_COLUMN_ACTIVE]);

		try
		{
			return $objEntityModel->save();
		}
		catch (Exception $objEx)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred when saving the cms home entity message: ') . $objEx->getMessage());

			return $this->redirect('*/cms_home/index', true);

		}
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * Liefert alle Dateien, die hochgeladen werden müssen
	 *
	 * @return array alle Dateien, die hochgeladen werden müssen
	 */
	protected function getUploadFiles()
	{

		if (count($_FILES) == 0)
		{
			return array();
		}
		$arrUploadFiles = array();

		foreach ($_FILES as $strFileKey => $arrFile)
		{

			foreach ($arrFile as $intAttributeKey => $arrAttributeValues)
			{

				if (!is_array($arrAttributeValues) || count($arrAttributeValues) == 0)
				{
					continue;
				}

				foreach ($arrAttributeValues as $intAttributeId => $strAttributeValue)
				{
					$strAttributeValue = trim($strAttributeValue);

					if (strlen($strAttributeValue) == 0)
					{
						continue;
					}

					$arrUploadFiles [$intAttributeId] [$intAttributeKey] = $strAttributeValue;
				}
			}
		}

		return $arrUploadFiles;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param int $intEntityId
	 *
	 * @param array $arrSaveData
	 *
	 *
	 * Speichert alle Attribute von einer gegebenenen Entity
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_NewController
	 */
	protected function saveEntityAttributeValues($intEntityId, $arrSaveData)
	{

		if (is_null($intEntityId) || $intEntityId <= 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('no entity id is given to update'));

			return $this->_redirect('*/cms_home/index');
		}

		if (!is_array($arrSaveData) || count($arrSaveData) == 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('no update data is given to save cms home attribute'));

			$this->redirect('*/cms_home/index', true);
		}

		foreach ($arrSaveData as $intAttributeId => $strAttributeValue)
		{

			$objAttributeValuesModel = new  SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values ();


			$objAttributeValuesModelCollection = $objAttributeValuesModel->getCollection()->getByEntityIdAndAttributeId($intEntityId, $intAttributeId);

			if ($objAttributeValuesModelCollection->count() == 0)
			{
				$this->saveEntityAttributeAttribute($intEntityId, $intAttributeId, $strAttributeValue);
			}
			else
			{
				foreach ($objAttributeValuesModelCollection as $objCurrentAttrValueCollection)
				{
					$this->saveEntityAttributeAttribute($intEntityId, $intAttributeId, $strAttributeValue, $objCurrentAttrValueCollection->getId());
				}
			}
		}

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param int $intEntityId
	 *
	 * @param array $arrSaveFiles
	 *
	 * Lädt Dateien auf den Server hoch und speichert den Dateipfad dazu ab
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_NewController
	 */
	protected function saveFiles($intEntityId, $arrSaveFiles)
	{

		if (!is_array($arrSaveFiles) || count($arrSaveFiles) == 0)
		{
			return $this;
		}

		$objAttributeValuesModel = Mage:: getModel('aurednik/cms_home_entity_attribute_values');


		foreach ($arrSaveFiles as $intAttributeId => $arrFile)
		{

			if (!isset ($arrFile ['name']) ||
				!isset ($arrFile ['type']) ||
				!isset ($arrFile ['tmp_name']) ||
				!isset ($arrFile ['error']) ||
				!isset ($arrFile ['size']) ||
				$arrFile ['size'] == 0 ||
				$arrFile ['error'] > 0
			)
			{

				continue;
			}

			$strFilePath = $this->uploadFile($intEntityId, $arrFile);

			$objAttributeValuesModelCollection = $objAttributeValuesModel->getCollection()->getByEntityIdAndAttributeId($intEntityId, $intAttributeId);

			if ($objAttributeValuesModelCollection->count() == 0)
			{
				$this->saveEntityAttributeAttribute($intEntityId, $intAttributeId, $strFilePath);
			}
			else
			{
				foreach ($objAttributeValuesModelCollection as $current)
				{
					$this->saveEntityAttributeAttribute($intEntityId, $intAttributeId, $strFilePath, $current->getId());
				}
			}
		}

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access protected
	 *
	 * @param int $intEntityId
	 *
	 *
	 * Löscht alle hochgeladenen Dateien und das Upload Verzeichnis vom Sever
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
	 */
	protected function deleteExistingFiles($intEntityId)
	{

		if (is_null($intEntityId) || $intEntityId <= 0)
		{
			return $this;
		}

		$strPath = SDZeCOM_Library_Helper_Directory:: joinPaths(
			Mage:: getBaseDir('media'),
			Mage:: getStoreConfig(SDZeCOM_Aurednik_Helper_Data :: CMS_HOME_UPLOAD_IMAGE_FILE_PATH),
			$intEntityId);

		if (!file_exists($strPath))
		{

			return $this;
		}

		$arrFiles = glob($strPath . "/*.*");

		if (count($arrFiles) == 0)
		{
			@rmdir($dirname);
		}

		foreach ($arrFiles as $file)
		{
			@unlink($file);
		}

		@rmdir($strPath);

		return $this;
	}


	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @param string $strPath
	 *
	 * @param bool $boolNowRedirect
	 * Ausgabe wird auf einen bestimmten Controller umgeleitet
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
	 */
	protected function redirect($strPath, $boolNowRedirect = false)
	{

		$this->_redirect($strPath);

		if ($boolNowRedirect)
		{
			$this->getResponse()->sendResponse();
			die ();
		}

		return $this;

	}

	//---------------------------------- private area -----------------------------------------------------

	/**
	 * @author akniss
	 *
	 * @version 1.0
	 *
	 * @access private
	 *
	 * @param int $intEntityId
	 *
	 * @param int $intAttributeId
	 *
	 * @param string $strAttributeValue
	 *
	 * @param int $intValueId = 0
	 *
	 * @param array $arrSaveFiles = array ()
	 *
	 * Speichert ein Attribut Wert
	 *
	 * @return SDZeCOM_Aurednik_Adminhtml_Cms_HomeController
	 */
	private function saveEntityAttributeAttribute($intEntityId, $intAttributeId, $strAttributeValue, $intValueId = 0)
	{

		if (is_null($intEntityId) || $intEntityId <= 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred entity id is null or empty'));

			$this->redirect('*/cms_home/index', true);
		}

		if (is_null($intAttributeId) || $intAttributeId <= 0)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred entity attribute id is null or empty'));

			$this->redirect('*/cms_home/index', true);
		}

		if (is_array($strAttributeValue) && isset ($strAttributeValue ['delete']))
		{
			$this->deleteFile($intValueId);
			$strAttributeValue = "";
		}

		$objAttributeValuesModel = new  SDZeCOM_Aurednik_Model_Cms_Home_Entity_Attribute_Values ();

		if ($intValueId > 0)
		{
			$objAttributeValuesModel->load($intValueId);

			$strCurrentValue = $objAttributeValuesModel->getAttribute_value();
		}

		if (is_array($strAttributeValue))
		{
			return $this;
		}

		$objAttributeValuesModel->setEntity_id($intEntityId);

		$objAttributeValuesModel->setAttribute_id($intAttributeId);

		$objAttributeValuesModel->setAttribute_value($strAttributeValue);

		try
		{
			$objAttributeValuesModel->save();
		}
		catch (Exception $objEx)
		{
			Mage:: getSingleton('core/session')->addError(Mage:: helper('aurednik')->__('Error occurred when save the cms home entity attribute'));
			$this->redirect('*/cms_home/index', true);
		}

		return $this;

	}
}