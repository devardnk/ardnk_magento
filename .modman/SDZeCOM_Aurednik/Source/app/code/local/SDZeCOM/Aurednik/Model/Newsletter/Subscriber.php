<?php

/**
 *
 * @author egutsche
 *
 */
class SDZeCOM_Aurednik_Model_Newsletter_Subscriber extends Mage_Newsletter_Model_Subscriber
{
	/**
	 * @author egutsche
	 *
	 * @version 1.0
	 *
	 * @param integer $email
	 * @param array $subscriberData
	 * @throws Exception
	 */
	public function subscribe($email, $subscriberData = array())
	{

		$this->loadByEmail($email);
		$customerSession = Mage::getSingleton('customer/session');

		if (!$this->getId())
		{
			$this->setSubscriberConfirmCode($this->randomSequence());
		}

		$isConfirmNeed = (Mage::getStoreConfig(self::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
		$isOwnSubscribes = false;
		$ownerId = Mage::getModel('customer/customer')
			->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
			->loadByEmail($email)
			->getId();
		$isSubscribeOwnEmail = $customerSession->isLoggedIn() && $ownerId == $customerSession->getId();

		if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
			|| $this->getStatus() == self::STATUS_NOT_ACTIVE
		)
		{
			if ($isConfirmNeed === true)
			{
				// if user subscribes own login email - confirmation is not needed
				$isOwnSubscribes = $isSubscribeOwnEmail;
				if ($isOwnSubscribes == true)
				{
					$this->setStatus(self::STATUS_SUBSCRIBED);
				}
				else
				{
					$this->setStatus(self::STATUS_NOT_ACTIVE);
				}
			}
			else
			{
				$this->setStatus(self::STATUS_SUBSCRIBED);
			}
			$this->setSubscriberEmail($email);
		}

		if ($isSubscribeOwnEmail)
		{
			$this->setStoreId($customerSession->getCustomer()->getStoreId());
			$this->setCustomerId($customerSession->getCustomerId());
		}
		else
		{
			$this->setStoreId(Mage::app()->getStore()->getId());
			$this->setCustomerId(0);
		}

		$this->setIsStatusChanged(true);


		try
		{

			// Falls der Benutzer zusätzliche Daten eingegeben hat,
			// werden diese zu den Abonnentendaten hinzugefügt
			if (is_array($subscriberData) && count($subscriberData) > 0)
			{
				foreach ($subscriberData as $attributeCode => $attributeValue)
				{
					//vd::d($attributeValue,' --> '. $attributeCode);
					$this->setData($attributeCode, $attributeValue);
				}
			}
			$this->save();

			if ($isConfirmNeed === true
				&& $isOwnSubscribes === false
			)
			{
				$this->sendConfirmationRequestEmail();
			}
			else
			{
				$this->sendConfirmationSuccessEmail();
			}

			return $this->getStatus();
		}
		catch (Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

}
