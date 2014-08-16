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

class Belvg_Referralreward_Block_Account_Log_Item extends Mage_Core_Block_Template
{
    protected $_model = array();

    public function convertDate($date)
    {
        return Mage::helper('core')->formatDate($date, 'short', FALSE);
    }

    public function getRowClass()
    {
        $item   = $this->getItem();
        $class  = '';
        $expire = floor((strtotime($item->getEndAt()) - time()) / (60*60*24));

        if ($item->getPoints() == 0) {
            $class .= ' points-empty';
        } else if ($expire < 3) {
            $class .= ' points-soon-expire points-soon-expire' . $expire;
        }

        if ($item->getPointsOrig() < 0) {
            $class .= ' points-order-refund';
        }

        return $class;
    }

    protected function getModel()
    {
        $item = $this->getItem();
        $id   = $item->getId();
        if (!isset($this->_model[$id])) {
            try {
                $this->_model[$id] = Mage::helper('referralreward')->getLogModel($item->getType())->load($item->getObjectId());
            } catch (Exception $e) {
                //print_r($e);
                $this->_model[$id] = new Varien_Object;
                $this->_model[$id]->setData(array(
                    'log_title'       => $id,
                    'log_description' => $item->getObjectId(),
                ));
            }
        }

        return $this->_model[$id];
    }

    public function getTitle()
    {
        return $this->getModel()->getLogTitle();
    }

    public function getDescription()
    {
        return $this->getModel()->getLogDescription();
    }
}