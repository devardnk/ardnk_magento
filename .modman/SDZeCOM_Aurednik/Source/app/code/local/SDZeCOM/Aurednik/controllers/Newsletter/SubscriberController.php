<?php

require_once(Mage::getBaseDir('app') . DS . 'code' . DS . 'core' . DS . 'Mage' . DS . 'Newsletter' . DS . 'controllers' . DS . 'SubscriberController.php');

class SDZeCOM_Aurednik_Newsletter_SubscriberController extends Mage_Newsletter_SubscriberController
{
	public function newAction()
	{
		if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email'))
		{
			$session = Mage::getSingleton('core/session');
			$customerSession = Mage::getSingleton('customer/session');
			$email = (string)$this->getRequest()->getPost('email');

			// Zusätzliche Daten des Abonnenten
			$subscriberData = array();
			$subscriberData = $this->getRequest()->getPost();


			$ownerId = Mage::getModel('newsletter/subscriber')->loadByEmail($_POST['email'])->getId();


			try
			{
				if (!Zend_Validate::is($email, 'EmailAddress'))
				{
					Mage::throwException($this->__('Please enter a valid email address.'));
				}

				if ($ownerId !== null && $ownerId != $customerSession->getId())
				{
					Mage::throwException($this->__('This email address is already subscribed to a newsletter.'));
				}

				if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 &&
					!$customerSession->isLoggedIn()
				)
				{
					Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
				}

				$status = Mage::getModel('newsletter/subscriber')->subscribe($email, $subscriberData);


				if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE)
				{
					$session->addSuccess($this->__('Confirmation request has been sent.'));
				}
				else
				{
					$session->addSuccess($this->__('Thank you for your subscription.'));
				}

//                 $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
//                 $subscriber->setCompany((string) $this->getRequest()->getPost('subscriber_company'));
//                 $subscriber->save();

			}

			catch (Mage_Core_Exception $e)
			{
				$session->addException($e, $e->getMessage());
			}
			catch (Exception $e)
			{
				$session->addException($e, $this->__('There was a problem with the subscription.'));
			}
		}

		$this->_redirectReferer();
	}
}
