<?php
/**
 * Contacts Form Captcha index controller
 *
 * @category    OlegKoval
 * @package     OlegKoval_ContactsFormCaptcha
 * @copyright   Copyright (c) 2012 Oleg Koval
 * @author      Oleg Koval <oleh.koval@gmail.com>
 */
//include controller to override it
require_once(Mage::getBaseDir('app') . DS .'code'. DS .'core'. DS .'Mage'. DS .'Contacts'. DS .'controllers'. DS .'IndexController.php');

class OlegKoval_ContactsFormCaptcha_IndexController extends Mage_Contacts_IndexController {
    const XML_PATH_CFC_ENABLED     = 'contacts/olegkoval_contactsformcaptcha/enabled';
    const XML_PATH_CFC_PUBLIC_KEY  = 'contacts/olegkoval_contactsformcaptcha/public_key';
    const XML_PATH_CFC_PRIVATE_KEY = 'contacts/olegkoval_contactsformcaptcha/private_key';
    const XML_PATH_CFC_THEME       = 'contacts/olegkoval_contactsformcaptcha/theme';
    const XML_PATH_CFC_LANG        = 'contacts/olegkoval_contactsformcaptcha/lang';

    /**
     * Check if "Contacts Form Captcha" is enabled
     */
    public function preDispatch() {
        parent::preDispatch();
    }

    /**
     * Method which handle action of displaying contact form
     */
    public function indexAction() {
        $this->loadLayout();

        $this->getLayout()->getBlock('contactForm')->setFormAction(Mage::getUrl('*/*/post'));

        if (Mage::getStoreConfigFlag(self::XML_PATH_CFC_ENABLED)) {
            //include reCaptcha library
            require_once(Mage::getBaseDir('lib') . DS .'reCaptcha'. DS .'recaptchalib.php');
            
            //create captcha html-code
            $publickey = Mage::getStoreConfig(self::XML_PATH_CFC_PUBLIC_KEY);
            $captcha_code = recaptcha_get_html($publickey);

            //get reCaptcha theme name
            $theme = Mage::getStoreConfig(self::XML_PATH_CFC_THEME);
            if (strlen($theme) == 0 || !in_array($theme, array('red', 'white', 'blackglass', 'clean'))) {
                $theme = 'red';
            }

            //get reCaptcha lang name
            $lang = Mage::getStoreConfig(self::XML_PATH_CFC_LANG);
            if (strlen($lang) == 0 || !in_array($lang, array('en', 'nl', 'fr', 'de', 'pt', 'ru', 'es', 'tr'))) {
                $lang = 'en';
            }
            //small hack for language feature - because it's not working as described in documentation
            $captcha_code = str_replace('?k=', '?hl='. $lang .'&amp;k=', $captcha_code);

            $this->getLayout()->getBlock('contactForm')->setCaptchaCode($captcha_code)
                                                        ->setCaptchaTheme($theme)
                                                        ->setCaptchaLang($lang);
        }

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /**
     * Handle post request of Contact form
     * @return [type] [description]
     */
    public function postAction() {
        if (Mage::getStoreConfigFlag(self::XML_PATH_CFC_ENABLED)) {
            try {
                $post  = $this->getRequest()->getPost();
                $customerSession    = Mage::getSingleton('customer/session');
                $subscriberData = array();
                $subscriberData = $post;

                $formData = new Varien_Object();
                $formData->setData($post);
                Mage::getSingleton('core/session')->setData('contactForm', $formData);
                
                if ($post) {
                    //include reCaptcha library
                    require_once(Mage::getBaseDir('lib') . DS .'reCaptcha'. DS .'recaptchalib.php');
                    
                    //validate captcha
                    $privatekey = Mage::getStoreConfig(self::XML_PATH_CFC_PRIVATE_KEY);
                    $remote_addr = $this->getRequest()->getServer('REMOTE_ADDR');
                    $captcha = recaptcha_check_answer($privatekey, $remote_addr, $post["recaptcha_challenge_field"], $post["recaptcha_response_field"]);

                    if (!$captcha->is_valid) {
                        throw new Exception($this->__("The reCAPTCHA wasn't entered correctly. Go back and try it again."), 1);
                    }

                    // Trennen von Vor- und Nachnamen
                    if(isset($_POST['name'])){
                    	$newName = $this->trimContactName($_POST['name']);
                    	$subscriberData[subscriber_firstname] = $newName[0];
                    	$subscriberData[subscriber_lastname] = $newName[1];
                    }
                    
                    /*
                     * ====================================================================*
                     *  Newsletter anmeldung - Code nicht vom Original Plugin
                     * ====================================================================*
                     */
                    if(isset($_POST['newsletter_subscribe']) 
                    	&& $post['newsletter_subscribe'] == 1 
                    	&& !$this->isSubscribed($_POST['email'])){
                    	
                    	$email = $_POST['email'];
                    	$status = Mage::getModel('newsletter/subscriber')->subscribe($email, $subscriberData);

                    	if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                    		Mage::getSingleton('core/session')
                    		->addSuccess($this->__('Thank you for your newsletter subscription.'));
                    	}
                    	else {
                    		Mage::getSingleton('core/session')
                    		->addSuccess($this->__('Thank you for your subscription.'));
                    	}
                    }
                    else{
						$ownerId = Mage::getModel('newsletter/subscriber')->loadByEmail($_POST['email'])->getId();

                    	if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    		Mage::throwException($this->__('This email address is already subscribed to a newsletter.'));
                    	}
                    }
                    /*====================================================================*/
                    
                    Mage::getSingleton('core/session')->unsetData('contactForm');
                }
                else {
                    throw new Exception('', 1);
                }         
            }
            catch (Exception $e) {
                if (strlen($e->getMessage()) > 0) {
                    Mage::getSingleton('customer/session')->addError($this->__($e->getMessage()));
                }
                $this->_redirect('*/*/');
                return;
            }
        }

        //everything is OK - call parent action
        parent::postAction();
    }
    
    
    /**
     * @author egutsche
     * 
     * Falls die Person mit der eingegebenen Email bereits den Newsletter abonniert hat, 
     * ist ein wiederholtes abonnieren nicht möglich
     * 
     * @param $post $email
     * @return boolean
     */
    private function isSubscribed ( $email ) {
    	
    	if ( is_null( $email ) || strlen( trim( $email ) ) == 0 ) {
    		return false;
    	}
    	
    	$email = trim( $email );
    	$subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail( $email );
    	
    	if ( is_null( $subscriber ) || $subscriber->getId() <= 0 ) {
    		return false;
    	}
    	
    	$post['newsletter_subscribe'] == 0;
    	
    	return true;
    	
    	
    	
    }
    /**
     * @author egutsche
     * 
     * Den eingegebenen Namen in Vor- und Nachnamen trennen
     * 
     * @param $name
     * @return $newName
     */
    private function trimContactName ($name){
    	
    	$newName = explode(" ", $name);
    	
    	return $newName;
    }
    
    
    
    
}