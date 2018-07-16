<?php

/**
 *
 */
class SDZeCOM_Aurednik_CatalogController extends Mage_Core_Controller_Front_Action
{

	const XML_PATH_EMAIL_RECIPIENT = 'contacts/aurednikcatalogorder/recipient_email';
	const XML_PATH_EMAIL_SENDER = 'contacts/aurednikcatalogorder/sender_email_identity';
	const XML_PATH_EMAIL_TEMPLATE = 'contacts/aurednikcatalogorder/email_template';


	public function postAction()
	{
		$post = $this->getRequest()->getPost();
		if ($post)
		{
			$translate = Mage::getSingleton('core/translate');
			/* @var $translate Mage_Core_Model_Translate */
			$translate->setTranslateInline(false);
			try
			{
				$postObject = new Varien_Object();
				$postObject->setData($post);

				$error = false;


				if ($error)
				{
					throw new Exception(__LINE__);
				}
				$mailTemplate = Mage::getModel('core/email_template');
				/* @var $mailTemplate Mage_Core_Model_Email_Template */
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($post['email'])
					->sendTransactional(
						Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
						Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
						Mage::getStoreConfig(self::XML_PATH_EMAIL_RECIPIENT),
						null,
						array('data' => $postObject)
					);

				if (!$mailTemplate->getSentSuccess())
				{
					throw new Exception(__LINE__);
				}

				$translate->setTranslateInline(true);

				Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
				$this->_redirectReferer();

				return;
			}
			catch (Exception $e)
			{
				$translate->setTranslateInline(true);
				vd::d($e->getMessage());
				exit;
				Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
				$this->_redirect('*/*/');
				return;
			}

		}
		else
		{
			$this->_redirect('*/*/');
		}
	}

}
