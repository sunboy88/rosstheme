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

class Belvg_Referralreward_FacebookController extends Mage_Core_Controller_Front_Action
{
    public function likesAction()
    {
        $likes = Mage::helper('referralreward/facebook')->getShared();

        print_r($likes); die;
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Sending messages to friends' facebook walls
     */
    public function sendidsAction()
    {
        $helper     = Mage::helper('referralreward/facebook');
        $facebook   = $helper->getFacebook();
        $request    = $this->getRequest();
        $attachment = array(
            'access_token' => $request->getParam('access_token'),
            'message'      => '',
            'name'         => $request->getParam('name'),
            'picture'      => $request->getParam('picture'),
            'link'         => $request->getParam('link'),
            'description'  => $request->getParam('description'),
        );
        $count      = 0;
        $result     = array();
        try {
            foreach ($request->getParam('recipients') AS $friendId) {
                // Sending invitations
                $facebook->api($friendId . '/feed', 'POST', $attachment);
                $count++;
            }

            $result['error']       = 0;
            if ($count > 1) {
                $result['message'] = $this->__("%s facebook invitations have been send", $count);
            } else {
                $result['message'] = $this->__("%s facebook invitation has been send", $count);
            }

            $this->_getSession()->addSuccess($result['message']);
        } catch (Exception $e) {
            $result['error']       = 1;
            $result['message']     = $e->getMessage();
            $this->_getSession()->addError($result['message']);
        }

        $this->getResponse()->setBody( Mage::helper('core')->jsonEncode($result) );
    }
}