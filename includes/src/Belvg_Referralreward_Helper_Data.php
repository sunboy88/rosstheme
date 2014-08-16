<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
/******************************************
 *      MAGENTO EDITION USAGE NOTICE      *
 ******************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
/******************************************
 *      DISCLAIMER                        *
 ******************************************/
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 ******************************************
 * @category   Belvg
 * @package    Belvg_Referralreward
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

class Belvg_Referralreward_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_settings = FALSE;

    public function convertPoints($points)
    {
        $settings = $this->getSettings();

        return $points * $settings['pointCost'];
    }

    public function convertAmount($amount)
    {
        $settings = $this->getSettings();

        return round(abs($amount) / $settings['pointCost']);;
    }

    public function getLogModel($key)
    {
        return Mage::getModel('referralreward/points_log_' . $key);
    }

    public function decodeStoreConfigMoveGroupTo($data = FALSE)
    {
        if (!$data) {
            $data = $this->storeConfig('move_group/to');
        }

        if ($data) {
            try {
                $data = '[' . str_replace(array('\\', 'g', 'i', '-'), array('', '"g"', '"i"', '"-"'), $data) . ']';
                $data = Mage::helper('core')->jsonDecode($data);

                if (count($data)) {
                    $values = array();
                    foreach ($data AS $value) {
                        $values[] = array(
                            'group'   => (int) $value['g'],
                            'invited' => (int) $value['i'],
                        );
                    }

                    //$values = $this->_sortValues($data);

                    return $values;
                }
            } catch (Exception $e) {
            
            }
        }

        return array(array('group' => 0, 'invited' => 0));
    }

    /**
     * Send email
     *
     * @param string System Config email id
     * @param string Sender email
     * @param string Sender name
     * @param string Recepient email
     * @param string Recepient name
     * @param array Params
     */
    public function sendEmail($templateConfigId, $senderEmail, $senderName, $recepientEmail, $recepientName, $vars)
    {
        $translate = Mage::getSingleton('core/translate');
        $storeId   = Mage::app()->getStore()->getId();
        $sender    = array(
            'name'  => $senderName,
            'email' => $senderEmail,
        );

        //Send Transactional Email
        $template  = Mage::getModel('core/email_template')
            //->setTemplateSubject("{{var sendername}} has invited you")
            ->sendTransactional($templateConfigId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
        $translate->setTranslateInline(TRUE);
    }

    /**
     * Get 'Point' object for a current customer
     *
     * @return Mage_Referralreward_Model_Points
     */
    public function getItemCurrentCustomer()
    {
        $customerId = (int) Mage::getSingleton('customer/session')->getId();

        return Mage::getModel('referralreward/points')->getItem($customerId);
    }

    public function hasOrderCurrentCustomer()
    {
        $customerId = (int) Mage::getSingleton('customer/session')->getId();

        $orders = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('entity_id')
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE);

        return $orders->count();
    }

    public function getTime($dateFormat = FALSE)
    {
        $time = Mage::app()->getLocale()->storeTimeStamp(Mage::app()->getStore()->getId());

        if ($dateFormat) {
            return date('Y-m-d H:i:s', $time);
        }

        return $time;
    }

    public function getEndTime($time)
    {
        if ($this->storeConfig('settings/point_lifetime_enabled')) {
            $liveTime = (int) $this->storeConfig('settings/point_lifetime');
        } else {
            $liveTime = 365 * 10; // 10 years
        }

        return $time + $liveTime * 24 * 60 * 60;
    }

    /**
     * The extension is enabled/disabled
     *
     * @return boolean
     */
    public function getReferralUrl()
    {
        return $this->_getUrl('referralreward/customer');
    }

    /**
     * The extension is enabled/disabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfig('referralreward/settings/enabled', Mage::app()->getStore());
    }

    /**
     * Get extension config
     *
     * @return array
     */
    public function getSettings()
    {
        if (!$this->_settings) {
            $storeId = Mage::app()->getStore();
            $this->_settings = array(
                'minOrder'        => round(str_replace(',', '.', Mage::getStoreConfig('referralreward/points/minorder', $storeId)), 2),
                'pointCost'       => round(str_replace(',', '.', Mage::getStoreConfig('referralreward/settings/pointcost', $storeId)), 2),
                'attributeCode'   => Mage::getStoreConfig('referralreward/settings/product_points_attribute_code', $storeId),
                'discardPoints'   => Mage::getStoreConfig('referralreward/settings/discard_points', $storeId),
                'transfer'        => Mage::getStoreConfig('referralreward/settings/transfer', $storeId),
                'use_coupon'      => Mage::getStoreConfig('referralreward/settings/use_coupon', $storeId),
                'cart_or_onepage' => Mage::getStoreConfig('referralreward/settings/cart_or_onepage', $storeId),
                'sliderUse'       => TRUE,//Mage::getStoreConfig('referralreward/settings/slider_use', $storeId),
            );
        }

        return $this->_settings;
    }

    public function getLabelTotal()
    {
        return $this->storeConfig('settings/total_label');
    }

    public function storeConfig($path)
    {
        return Mage::getStoreConfig('referralreward/' . $path, Mage::app()->getStore());
    }

    /**
     * @param inf The length of a coupon code
     * @param boolean Only numbers
     * @return string
     */
    public function createCouponCode($length = 10, $only_numbers = FALSE)
    {
        if ($only_numbers) {
            $symbols = range(0, $length - 1);
        } else {
            $symbols = array_merge(range(0, 9), range('A', 'Z'));
        }

        $size        = count($symbols) - 1;
        $code        = '';
        for ($i=0; $i<$length; ++$i) {
            $rand    = rand(0, $size);
            $code   .= $symbols[$rand];
        }

        return $code;
    }

    /**
     * Shopping cart price rules remove
     * example: extension is disable
     *
     * @return boolean
     */
    public function removeCouponsForReferral()
    {
        $pointsItems = Mage::getModel('referralreward/points')->getCollection();
        foreach ($pointsItems AS $pointsItem) {
            $coupon = Mage::getModel('salesrule/coupon')->load($pointsItem->getCouponCode(), 'code');
            $rule   = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
            if ($rule->getId()) {
                $rule->delete();
            }

            $pointsItem->delete();
        }

        return $this;
    }

    /**
     * Shopping cart price rules create/update
     *
     * @return boolean
     */
    public function createCouponForReferral($discountPoints = FALSE)
    {
        $customerId     = (int) Mage::getSingleton('customer/session')->getId();
        $groupId        = (int) Mage::getSingleton('customer/session')->getCustomerGroupId();
        $customer       = Mage::getModel('customer/customer')->load($customerId);

        $pointsItem     = Mage::getModel('referralreward/points')->getItem($customerId);
        $settings       = Mage::helper('referralreward')->getSettings();
        if ($discountPoints && $discountPoints <= $pointsItem->getPoints()) {
            $myCredit   = $discountPoints * $settings['pointCost'];
        } else {
            $myCredit   = $pointsItem->getPoints() * $settings['pointCost'];
        }

        $coupon         = Mage::getModel('salesrule/coupon')->load($pointsItem->getCouponCode(), 'code');
        $rule           = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
        if ($myCredit > 0) {
            if ($rule->getId()) {
                $rule->setDiscountAmount($myCredit);
                $rule->setCustomerGroupIds($groupId); //'0,1,2,3,4'
                $rule->save();
            } else {
                $model  = Mage::getModel('salesrule/rule');
                $model->setName('Discount for ' . $customer->getName());
                $model->setDescription('Discount for ' . $customer->getName());
                $model->setFromDate(date('Y-m-d'));
                $model->setCouponType(2);
                $model->setCouponCode($pointsItem->getCouponCode());
                $model->setUsesPerCoupon(0);
                //$model->setUsesPerCustomer(1);
                $model->setCustomerGroupIds($groupId);
                $model->setIsActive(1);
                //$model->setConditionsSerialized('a:6:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}');
                //$model->setActionsSerialized('a:6:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";}');
                $model->setStopRulesProcessing(0);
                $model->setIsAdvanced(1);
                $model->setProductIds('');
                $model->setSortOrder(1);
                $model->setSimpleAction('cart_fixed');
                $model->setDiscountAmount($myCredit);
                $model->setDiscountStep(0);
                $model->setSimpleFreeShipping(0);
                $model->setTimesUsed(0);
                $model->setIsRss(0);
                $model->setWebsiteIds(Mage::app()->getStore()->getWebsiteId());
                $model->save();
            }

            return TRUE;
        } else {
            if ($rule->getId()) {
                $rule->delete();
            }

            return FALSE;
        }
    }

    public function checkMageVersion()
    {
        $val = 1;
        if (Mage::getVersion() < 1.3) {
            $val = 0;
        } elseif(Mage::getVersion() > 1.3) {
            if (Mage::getVersion() == '1.4.0.1') {
                $val = 1;
            } else {
                $val = 2;
            }
        }

        return $val;
    }

    public function saveFriendsUrl($provider)
    {
        //return Mage::getUrl('referralreward/' . $provider . '/save');
        $url = explode('?', Mage::getUrl('referralreward/' . $provider . '/save'));

        return $url[0];
    }
    
    public function policyUrl()
    {
        return Mage::getUrl('referralreward/policy');
    }

    public function getQuotePoints()
    {
        $quote  = Mage::getSingleton('checkout/session')->getQuote();
        $items  = $quote->getAllVisibleItems();
        $points = 0;
        $attributeCode = $this->storeConfig('settings/product_points_attribute_code');
        if ($attributeCode) {
            foreach ($items as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                $points += (int) $item->getQty() * (int) $product->getData($attributeCode);
            }
        }

        return $points;
    }

    public function getOrderPoints($order)
    {
        //$order  = Mage::getModel("sales/order")->load($order->getId());
        $items  = $order->getAllItems();
        $points = 0;
        $attributeCode   = $this->storeConfig('settings/product_points_attribute_code');
        if ($attributeCode) {
            foreach ($items as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $points += (int) $item->getQtyOrdered() * (int) $product->getData($attributeCode);
            }
        }

        $settings = $this->getSettings();
        if ($settings['discardPoints']) {
            $discount   = abs($order->getDiscountAmount());
            $pointsItem = Mage::getModel('referralreward/points')->getItem($order->getCustomerId());
            $subtotal   = $order->getSubtotal();
            if ($discount && $order->getCouponCode() == $pointsItem->getCouponCode()) {
                $points = round($points * (1 - $discount / $subtotal));
            }
        }

        return $points;
    }

    public function getInvitationLink($url)
    {
        $invitationLink = explode('/invite/', $url);
        $invitationLink = trim($invitationLink[1], " /");

        return $invitationLink;
    }

    public function createNewPointsObject($customerId)
    {
        if (!$this->isEnabled()) {
            return $this;
        }

        $customerPoints = Mage::getModel('referralreward/points')->getItem($customerId);
        if (!$customerPoints->getId()) {
            $new = Mage::getModel('referralreward/points');
            $new->setCustomerId($customerId)
                ->setUrl('c' . $customerId)
                ->setCouponCode($this->createCouponCode(12, FALSE));
            $new->save();

            Mage::getModel('referralreward/points')->calculate($customerId);
        }

        return $this;
    }
}