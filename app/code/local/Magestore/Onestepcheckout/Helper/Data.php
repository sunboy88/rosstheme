<?php

class Magestore_Onestepcheckout_Helper_Data extends Mage_Core_Helper_Abstract {

    public function __construct() {
        $this->settings = $this->getConfigData();
    }

    public function enabledOnestepcheckout() {
        if (Mage::getStoreConfig('onestepcheckout/general/active', Mage::app()->getStore(true)->getStoreId())) {
            return true;
        }
        return false;
    }

    public function enabledDelivery() {
        if (Mage::getStoreConfig('onestepcheckout/general/delivery_time_date', Mage::app()->getStore(true)->getStoreId())) {
            return true;
        }
        return false;
    }

    public function enableRegistration() {
        if ($this->settings['enable_registration']) {
            return true;
        }
        return false;
    }

    public function loadDataforDisabledFields(&$data) {
        $configData = $this->getConfigData();
        if (!$configData['show_city']) {
            $data['city'] = '-';
        }
        if (!$configData['show_zipcode']) {
            $data['postcode'] = '-';
        }
        if (!$configData['show_company']) {
            $data['company'] = '';
        }
        if (!$configData['show_fax']) {
            $data['fax'] = '';
        }
        if (!$configData['show_telephone']) {
            $data['telephone'] = '-';
        }
        if (!$configData['show_region']) {
            $data['region'] = '-';
            $data['region_id'] = '-';
        }
        return $data;
    }

    public function loadEmptyData(&$data) {
        if (!isset($data['city']) || $data['city'] == '') {
            if ($this->settings['city'] != '') {
                $data['city'] = $this->settings['city'];
            } else {
                $data['city'] = '-';
            }
        }
        if (!isset($data['telephone']) || trim($data['telephone']) == '') {
            $data['telephone'] = '-';
        }
        if (!isset($data['postcode']) || $data['postcode'] == '') {
            if ($this->settings['postcode'] != '') {
                $data['postcode'] = $this->settings['postcode'];
            } else {
                $data['postcode'] = '-';
            }
        }
        if (!isset($data['region']) || $data['region'] == '') {
            $data['region'] = '-';
        }
        if (!isset($data['region_id']) || $data['region_id'] == '') {
            if ($this->settings['region_id'] != '') {
                $data['region_id'] = $this->settings['region_id'];
            } else {
                $data['region_id'] = '-';
            }
        }
        if (!isset($data['country_id']) || $data['country_id'] == '') {
            if ($this->settings['country_id'] != '') {
                $data['country_id'] = $this->settings['country_id'];
            } else {
                $data['country_id'] = '-';
            }
        }
        return $data;
    }

    public function getConfigData() {
        $configData = array();
        $configItems = array('general/active', 'general/checkout_title', 'general/checkout_description',
            'general/show_shipping_address', 'general/country_id',
            'general/default_payment', 'general/default_shipping',
            'general/postcode', 'general/region_id', 'general/city',
            'general/use_for_disabled_fields', 'general/hide_shipping_method',
            'general/page_layout',
            'field_management/show_city', 'field_management/show_zipcode',
            'field_management/show_company', 'field_management/show_fax',
            'field_management/show_telephone', 'field_management/show_region',
            'general/show_comment', 'general/show_newsletter',
            'general/show_discount', 'general/newsletter_default_checked',
            'field_management/enable_giftmessage',
            'checkout_mode/show_login_link', 'checkout_mode/enable_registration',
            'checkout_mode/allow_guest', 'checkout_mode/login_link_title',
            'ajax_update/enable_ajax', 'ajax_update/ajax_fields',
            'ajax_update/update_payment',
            'ajax_update/reload_payment',
            'terms_conditions/enable_terms', 'terms_conditions/term_html',
            'terms_conditions/term_width', 'terms_conditions/term_height',
            'order_notification/enable_notification', 'order_notification/notification_email');
        foreach ($configItems as $configItem) {
            $config = explode('/', $configItem);
            $value = $config[1];
            $configData[$value] = Mage::getStoreConfig('onestepcheckout/' . $configItem);
        }
        return $configData;
    }

    public function isShowShippingAddress() {
        if ($this->getOnepage()->getQuote()->isVirtual()) {
            return false;
        }
        if ($this->settings['show_shipping_address']) {
            return true;
        }
        return false;
    }

    public function getOnePage() {
        return Mage::getSingleton('checkout/type_onepage');
    }

    public function getCheckoutUrl() {
        return Mage::getUrl('onestepcheckout');
    }

    public function savePaymentMethod($data) {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }
        $onepage = Mage::getSingleton('checkout/session')->getQuote();
        if ($onepage->isVirtual()) {
            $onepage->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $onepage->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }
        $payment = $onepage->getPayment();
        $payment->importData($data);

        $onepage->save();

        return array();
    }

    public function saveShippingMethod($shippingMethod) {
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        $rate = $this->getOnepage()->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid shipping method.'));
        }
        $this->getOnepage()->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod);
        $this->getOnepage()->getQuote()->collectTotals()->save();
        return array();
    }

    public function allowGuestCheckout() {
        $_quote = $this->getOnepage()->getQuote();
        $_isAllowed = $this->settings['allow_guest'];
        if ($_isAllowed) {
            $isContain = false;
            foreach ($_quote->getAllItems() as $item) {
                if (($product = $item->getProduct()) &&
                        $product->getTypeId() == Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE) {
                    $isContain = true;
                }
            }
            $store = Mage::app()->getStore()->getId();
            if ($isContain && Mage::getStoreConfigFlag('catalog/downloadable/disable_guest_checkout', $store)) {
                $_isAllowed = false;
            }
        }
        return $_isAllowed;
    }

    public function isUseDefaultDataforDisabledFields() {
        return $this->settings['use_for_disabled_fields'];
    }

    public function isShowNewsletter() {
        if ($this->settings['show_newsletter'] && !$this->isSignUpNewsletter())
            return true;
        else
            return false;
    }

    public function isSignUpNewsletter() {
        if ($this->isCustomerLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (isset($customer))
                $customerNewsletter = Mage::getModel('newsletter/subscriber')->loadByEmail($customer->getEmail());
            if (isset($customerNewsletter) && $customerNewsletter->getId() != null && $customerNewsletter->getData('subscriber_status') == 1) {
                return true;
            }
        }
        return false;
    }

    public function isSubscribeByDefault() {
        return $this->settings['newsletter_default_checked'];
    }

    public function enableOrderComment() {
        return $this->settings['show_comment'];
    }

    public function showDiscount() {
        return $this->settings['show_discount'];
    }

    public function enableTermsAndConditions() {
        return $this->settings['enable_terms'];
    }

    public function getTermPopupWidth() {
        return $this->settings['term_width'];
    }

    public function getTermPopupHeight() {
        return $this->settings['term_height'];
    }

    public function getTermsConditionsHtml() {
        return $this->settings['term_html'];
    }

    public function enableNotifyAdmin() {
        return $this->settings['enable_notification'];
    }

    public function getEmailArray() {
        $email_string = (string) $this->settings['notification_email'];
        if ($email_string != '') {
            $email_array = explode(",", $email_string);
            return $email_array;
        }
        return array();
    }

    public function getEmailTemplate() {
        return Mage::getStoreConfig('onestepcheckout/order_notification/notification_email_template');
    }

    public function getStoreId() {
        return Mage::app()->getStore()->getId();
    }

    public function enableGiftMessage() {
        //return $this->settings['enable_giftmessage'];
//		return Mage::getStoreConfig('sales/gift_options/allow_order');
        $giftMessage = Mage::getStoreConfig('onestepcheckout/giftmessage/enable_giftmessage', $this->getStoreId());
        if ($giftMessage) {
            Mage::getConfig()->saveConfig('sales/gift_options/allow_order', 1);
            Mage::getConfig()->saveConfig('sales/gift_options/allow_items', 1);
            return true;
        } else {
            Mage::getConfig()->saveConfig('sales/gift_options/allow_order', 0);
            Mage::getConfig()->saveConfig('sales/gift_options/allow_items', 0);
            return false;
        }
    }

    public function enableCustomSize() {
        return Mage::getStoreConfig('onestepcheckout/terms_conditions/enable_custom_size', $this->getStoreId());
    }

    public function getTermTitle() {
        return Mage::getStoreConfig('onestepcheckout/terms_conditions/term_title', $this->getStoreId());
    }

    public function enableGiftWrap() {
        return Mage::getStoreConfig('onestepcheckout/giftwrap/enable_giftwrap', $this->getStoreId());
    }

    public function getGiftwrapType() {
        return Mage::getStoreConfig('onestepcheckout/giftwrap/giftwrap_type', $this->getStoreId());
    }

    public function getGiftwrapAmount() {
        return Mage::getStoreConfig('onestepcheckout/giftwrap/giftwrap_amount', $this->getStoreId());
    }

    public function isCustomerLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function showLoginLink() {
        return Mage::getStoreConfig('onestepcheckout/checkout_mode/show_login_link', $this->getStoreId());
    }

    public function checkGiftwrapSession() {
        $session = Mage::getSingleton('checkout/session');
        return $session->getData('onestepcheckout_giftwrap');
    }

    public function isHideShippingMethod() {
        $_isHide = $this->settings['hide_shipping_method'];
        if ($_isHide) {
            $_quote = $this->getOnepage()->getQuote();
            $rates = $_quote->getShippingAddress()->getShippingRatesCollection();
            $rateCodes = array();
            foreach ($rates as $rate) {
                if (!in_array($rate->getCode(), $rateCodes)) {
                    $rateCodes[] = $rate->getCode();
                }
            }
            if (count($rateCodes) > 1) {
                $_isHide = false;
            }
        }

        return $_isHide;
    }

    /*
     * Save customer comment to the order
     */

    public function saveOrderComment($observer) {
        $session = Mage::getSingleton('checkout/session');
        $billing = $this->_getRequest()->getPost('billing');
        $delivery = $this->_getRequest()->getPost('delivery');
        $session = Mage::getSingleton('checkout/session');
        if ($this->enableOrderComment()) {
            $comment = $billing['onestepcheckout_comment'];
            $comment = trim($comment);
            if ($comment != '') {
                $order = $observer->getEvent()->getOrder();
                try {
                    // use custom attribute to save customer comment - magento 1.3
//                    $order->setOnestepcheckoutOrderComment($comment)
                    $order->addStatusHistoryComment($comment, false);
                    //Magento 1.4.1.1 - can not use custom attribute to save customer comment
                    //$order->setCustomerNote($comment);
                    //$order->save();
                } catch (Exception $e) {
                    
                }
            }
        }

        if ($this->enableSurvey()) {
            $surveyQuestion = $this->getSurveyQuestion();
            $surveyValues = unserialize($this->getSurveyValues());
            $surveyValue = $billing['onestepcheckout-surveybilling'];
            $surveyFreeText = $billing['onestepcheckout-survey-freetext'];

            if (!empty($surveyValue)) {
                if ($surveyValue != 'freetext') {
                    $surveyAnswer = $surveyValues[$surveyValue]['value'];
                } else {
                    $surveyAnswer = $surveyFreeText;
                }
            }

            $order = $observer->getEvent()->getOrder();
            if ($surveyQuestion)
                $session->setData('survey_question', $surveyQuestion);
            if ($surveyAnswer)
                $session->setData('survey_answer', $surveyAnswer);
        }

        //Save delivery
        if ($this->enabledDelivery()) {
            if ($delivery['onestepcheckout-date']) {
                $delivery_date_time = $delivery['onestepcheckout-date'] . ' ' . $delivery['onestepcheckout-time'];
                $session->setData('delivery_date_time', $delivery_date_time);
            }
        }
    }

    /*
     * use to load default data for disabled fields
     * only use if it is enabled
     */

    public function setDefaultDataforDisabledFields(&$data) {
        if (!$this->settings['show_city']) {
            $data['city'] = $this->settings['city'];
        }
        if (!$this->settings['show_zipcode']) {
            $data['postcode'] = $this->settings['postcode'];
        }
        if (!$this->settings['show_region']) {
            $data['region_id'] = $this->settings['region_id'];
        }
        return $data;
    }

    public function getStyle() {
        $path = 'onestepcheckout/style_management/style';
        $value = Mage::getStoreConfig($path, Mage::app()->getStore()->getStoreId());
        return $value;
    }
    
    // ver 3.1 - Michael 20140610
    public function getStyleColor() {
        $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $storecode = Mage::app()->getStore()->getCode();
        $path = 'onestepcheckout/style_management/custom';
        $value = Mage::getModel('onestepcheckout/config')->getCollection()
                ->addFieldToFilter('scope', 'website')
                ->addFieldToFilter('path', $path)
                ->addFieldToFilter('scope_id', $websiteId)
                ->getFirstItem()
                ->getValue();

        if (!$value) {
			$value = Mage::getModel('onestepcheckout/config')->getCollection()
					->addFieldToFilter('path', $path)
					->addFieldToFilter('scope', 'stores')
					->addFieldToFilter('scope_id', $storeId)
					->getFirstItem()
					->getValue();
			if(!$value)
				$value = Mage::getModel('onestepcheckout/config')->getCollection()
						->addFieldToFilter('scope', 'default')
						->addFieldToFilter('path', $path)
						->addFieldToFilter('scope_id', 0)
						->getFirstItem()
						->getValue();
        }
        return $value;
    }
    
	public function getCheckoutButtonColor() {
        $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $storecode = Mage::app()->getStore()->getCode();
        $path = 'onestepcheckout/style_management/button';
        $value = Mage::getModel('onestepcheckout/config')->getCollection()
                ->addFieldToFilter('scope', 'website')
                ->addFieldToFilter('path', $path)
                ->addFieldToFilter('scope_id', $websiteId)
                ->getFirstItem()
                ->getValue();

        if (!$value) {
			$value = Mage::getModel('onestepcheckout/config')->getCollection()
					->addFieldToFilter('path', $path)
					->addFieldToFilter('scope', 'stores')
					->addFieldToFilter('scope_id', $storeId)
					->getFirstItem()
					->getValue();
			if(!$value)
				$value = Mage::getModel('onestepcheckout/config')->getCollection()
						->addFieldToFilter('scope', 'default')
						->addFieldToFilter('path', $path)
						->addFieldToFilter('scope_id', 0)
						->getFirstItem()
						->getValue();
        }
        if(!$value){
            $value = $this->getBackgroundColor('orange');
        }elseif($value == 'custom'){
            $pathButton = 'onestepcheckout/style_management/custombutton';
            $valueCustom = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('scope', 'website')
                    ->addFieldToFilter('path', $pathButton)
                    ->addFieldToFilter('scope_id', $websiteId)
                    ->getFirstItem()
                    ->getValue();

            if (!$valueCustom) {
                $valueCustom = Mage::getModel('onestepcheckout/config')->getCollection()
                        ->addFieldToFilter('scope', 'stores')
                        ->addFieldToFilter('path', $pathButton)
                        ->addFieldToFilter('scope_id', $storeId)
                        ->getFirstItem()
                        ->getValue();
				if(!$valueCustom)	
					$valueCustom = Mage::getModel('onestepcheckout/config')->getCollection()
                        ->addFieldToFilter('scope', 'default')
                        ->addFieldToFilter('path', $pathButton)
                        ->addFieldToFilter('scope_id', 0)
                        ->getFirstItem()
                        ->getValue();
            }
            if(!$valueCustom)
                $value = $this->getBackgroundColor('orange');
            else
                $value = '#'.$valueCustom;
        }else{
            $value = $this->getBackgroundColor($value);
        }        
        return $value;
    }
    
    //Onestepcheckout v2.0.0
    public function getFieldEnableBackEnd($number, $scope, $scopeId) {
        $path = 'onestepcheckout/field_position_management/row_' . $number;
        $value = Mage::getModel('onestepcheckout/config')->getCollection()
                ->addFieldToFilter('scope', $scope)
                ->addFieldToFilter('path', $path)
                ->addFieldToFilter('scope_id', $scopeId)
                ->getFirstItem()
                ->getValue();
        return $value;
    }

    public function getFieldEnables() {
        $path = 'onestepcheckout/field_position_management/row_';
        for ($i = 0; $i < 20; $i++) {
            $fields[$i]['value'] = Mage::getStoreConfig($path . $i, Mage::app()->getStore()->getStoreId());
            $fields[$i]['position'] = $i;
        }
        return $fields;
    }

    public function getFieldEnable($number) {
        $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $storecode = Mage::app()->getStore()->getCode();
        $path = 'onestepcheckout/field_position_management/row_' . $number;
        $value = Mage::getModel('onestepcheckout/config')->getCollection()
                ->addFieldToFilter('path', $path)
                ->addFieldToFilter('scope', 'stores')
                ->addFieldToFilter('scope_id', $storeId)
                ->getFirstItem()
                ->getValue();
        if (count($value) == 0 && $storecode == 'default') {
            $value = Mage::getModel('onestepcheckout/config')->getCollection()
                    ->addFieldToFilter('path', $path)
                    ->addFieldToFilter('scope', 'websites')
                    ->addFieldToFilter('scope_id', $websiteId)
                    ->getFirstItem()
                    ->getValue();
        }
        if (count($value) == 0) {
            $value = $this->getDefaultField($number);
        }

        return $value;
    }

    public function getDefaultField($number) {
        $path = 'onestepcheckout/field_position_management/row_' . $number;
        $value = Mage::getModel('onestepcheckout/config')->getCollection()
                ->addFieldToFilter('path', $path)
                ->addFieldToFilter('scope_id', 0)
                ->addFieldToFilter('scope', 'default')
                ->getFirstItem()
                ->getValue();
        return $value;
    }

    public function getFieldValue() {
        return array(
            '0' => Mage::helper('onestepcheckout')->__('Null'),
            'firstname' => Mage::helper('onestepcheckout')->__('First Name'),
            'lastname' => Mage::helper('onestepcheckout')->__('Last Name'),
            'prefix' => Mage::helper('onestepcheckout')->__('Prefix Name'),
            'middlename' => Mage::helper('onestepcheckout')->__('Middle Name'),
            'suffix' => Mage::helper('onestepcheckout')->__('Suffix Name'),
            'email' => Mage::helper('onestepcheckout')->__('Email Address'),
            'company' => Mage::helper('onestepcheckout')->__('Company'),
            'street' => Mage::helper('onestepcheckout')->__('Address'),
            'country' => Mage::helper('onestepcheckout')->__('Country'),
            'region' => Mage::helper('onestepcheckout')->__('State/Province'),
            'city' => Mage::helper('onestepcheckout')->__('City'),
            'postcode' => Mage::helper('onestepcheckout')->__('Zip/Postal Code'),
            'telephone' => Mage::helper('onestepcheckout')->__('Telephone'),
            'fax' => Mage::helper('onestepcheckout')->__('Fax'),
            'birthday' => Mage::helper('onestepcheckout')->__('Date of Birth'),
            'gender' => Mage::helper('onestepcheckout')->__('Gender'),
            'taxvat' => Mage::helper('onestepcheckout')->__('Tax/VAT number'),
        );
    }

    public function getFieldLabel($field) {
        if ($field == 'firstname')
            return Mage::helper('onestepcheckout')->__('First Name');
        if ($field == 'lastname')
            return Mage::helper('onestepcheckout')->__('Last Name');
        if ($field == 'prefix')
            return Mage::helper('onestepcheckout')->__('Prefix Name');
        if ($field == 'middlename')
            return Mage::helper('onestepcheckout')->__('Middle Name');
        if ($field == 'suffix')
            return Mage::helper('onestepcheckout')->__('Suffix Name');
        if ($field == 'email')
            return Mage::helper('onestepcheckout')->__('Email Address');
        if ($field == 'company')
            return Mage::helper('onestepcheckout')->__('Company');
        if ($field == 'street')
            return Mage::helper('onestepcheckout')->__('Address');
        if ($field == 'country')
            return Mage::helper('onestepcheckout')->__('Country');
        if ($field == 'region')
            return Mage::helper('onestepcheckout')->__('State/Province');
        if ($field == 'city')
            return Mage::helper('onestepcheckout')->__('City');
        if ($field == 'postcode')
            return Mage::helper('onestepcheckout')->__('Zip/Postal Code');
        if ($field == 'telephone')
            return Mage::helper('onestepcheckout')->__('Telephone');
        if ($field == 'fax')
            return Mage::helper('onestepcheckout')->__('Fax');
        if ($field == 'birthday')
            return Mage::helper('onestepcheckout')->__('Date of Birth');
        if ($field == 'gender')
            return Mage::helper('onestepcheckout')->__('Gender');
        if ($field == 'taxvat')
            return Mage::helper('onestepcheckout')->__('Tax/VAT number');
    }

    public function getFieldRequire($field) {
        return Mage::getStoreConfig('onestepcheckout/field_require_management/' . $field, Mage::app()->getStore()->getStoreId());
    }

    //Survey	
    public function enableSurvey() {
        return Mage::getStoreConfig('onestepcheckout/survey/enable_survey', $this->getStoreId());
    }

    public function getSurveyQuestion() {
        return Mage::getStoreConfig('onestepcheckout/survey/survey_question', $this->getStoreId());
    }

    public function enableFreeText() {
        return Mage::getStoreConfig('onestepcheckout/survey/enable_survey_freetext', $this->getStoreId());
    }

    public function getSurveyValues() {
        return Mage::getStoreConfig('onestepcheckout/survey/survey_values', $this->getStoreId());
    }

    public function enableGiftwrapModule() {
        $moduleGiftwrap = Mage::getConfig()->getModuleConfig('Magestore_Giftwrap')->is('active', 'true');
        return $moduleGiftwrap;
    }

    public function getOrderGiftwrapAmount() {
        $amount = $this->getGiftwrapAmount();
        $giftwrapAmount = 0;
        // $freeBoxes = 0;
        $items = Mage::getSingleton('checkout/cart')->getItems();
        if ($this->getGiftwrapType() == 1) {
            foreach ($items as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
                $giftwrapAmount += $amount * ($item->getQty());
            }
        } elseif (count($items) > 0) {
            $giftwrapAmount = $amount;
        }
        return $giftwrapAmount;
    }

    /*
      Geoip
     */

    public function enableGeoip() {
        return false;
        return Mage::getStoreConfig('onestepcheckout/geoip/enable', $this->getStoreId());
    }

    public function allowDetectCountry() {
        return Mage::getStoreConfig('onestepcheckout/geoip/detect_by_ip', $this->getStoreId());
    }

    public function allowDetectByPostcode() {
        return Mage::getStoreConfig('onestepcheckout/geoip/detect_by_postcode', $this->getStoreId());
    }

    public function allowDetectByCity() {
        return Mage::getStoreConfig('onestepcheckout/geoip/detect_by_city', $this->getStoreId());
    }

    public function getMaxItemsEachImport() {
        return Mage::getStoreConfig('onestepcheckout/geoip/rows', $this->getStoreId());
    }

    public function getMinCharsPostcode() {
        return Mage::getStoreConfig('onestepcheckout/geoip/postcode_characters', $this->getStoreId());
    }

    public function getMinCharsCity() {
        return Mage::getStoreConfig('onestepcheckout/geoip/city_characters', $this->getStoreId());
    }

    public function getRealIpAddr() {
        //check ip from share internet
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //to check ip is pass from proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function detectCountryIp() {
        $realIp = $this->getRealIpAddr();
        $geoip = Mage::getModel('onestepcheckout/country');
        $ipInteger = $geoip->convertIpToInteger($realIp);
        $countryips = Mage::getModel('onestepcheckout/country')->getCollection()
                ->addFieldToFilter('first_ip_number', array('lteq' => $ipInteger['ip']))
                ->addFieldToFilter('last_ip_number', array('gteq' => $ipInteger['ip']))
        ;
        if (isset($ipInteger['ip_lower'])) {
            if ($ipInteger['ip_lower'] > 0) {
                $countryips = $countryips->addFieldToFilter('last_ip_number_lower', array('gteq' => $ipInteger['ip_lower']))
                // ->addFieldToFilter('first_ip_number_lower', array('lteq'=>$ipInteger['ip_lower']))									 
                ;
            }
        }
        $countryip = $countryips->getFirstItem();
        if ($countryip->getId())
            return $countryip->getData('country');
        return false;
    }

    public function getStoreByCode($storeCode) {
        $stores = array_keys(Mage::app()->getStores());
        foreach ($stores as $id) {
            $store = Mage::app()->getStore($id);
            if ($store->getCode() == $storeCode) {
                return $store;
            }
        }
        return null; // if not found
    }

    public function getDefaultPositionArray() {
        $arrayDefault = array();
        $arrayDefault[0] = 'firstname';
        $arrayDefault[1] = 'lastname';
        $arrayDefault[2] = 'email';
        $arrayDefault[3] = 'telephone';
        $arrayDefault[4] = 'street';
        $arrayDefault[6] = 'country';
        $arrayDefault[7] = 'city';
        $arrayDefault[10] = 'postcode';
        $arrayDefault[11] = 'region';
        $arrayDefault[12] = 'company';
        $arrayDefault[13] = 'fax';
        return $arrayDefault;
    }

    public function getBackgroundColor($style) {
        if ($style == 'orange')
            return '#F39801';
        if ($style == 'green')
            return '#B6CE5E';
        if ($style == 'black')
            return '#000000';
        if ($style == 'blue')
            return '#3398CC';
        if ($style == 'darkblue')
            return '#004BA0';
        if ($style == 'pink')
            return '#E13B91';
        if ($style == 'red')
            return '#E10E03';
        if ($style == 'violet')
            return '#B962d5';

        return '#F39801';
    }

}