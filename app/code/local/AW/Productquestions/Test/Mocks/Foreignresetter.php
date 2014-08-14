<?php
/**
* aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Productquestions
 * @version    1.5.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */


class AW_Productquestions_Test_Model_Mocks_Foreignresetter extends Mage_Core_Model_Abstract {

    public static $counter = 0;

    public static function dropForeignKeys() {

        if (!self::$counter) {

            $resource = Mage::getModel('core/resource');
            $connection = $resource->getConnection('core_write');


            $FKscope = array(
                'cataloginventory_stock_status' => array('FK_CATALOGINVENTORY_STOCK_STATUS_STOCK', 'FK_CATALOGINVENTORY_STOCK_STATUS_WEBSITE', 'FK_CATINV_STOCK_STS_STOCK_ID_CATINV_STOCK_STOCK_ID'),
                'catalog_product_website' => array('FK_CATALOG_PRODUCT_WEBSITE_WEBSITE'),
                'catalog_product_entity_int' => array('FK_CATALOG_PRODUCT_ENTITY_INT_ATTRIBUTE', 'FK_CATALOG_PRODUCT_ENTITY_INT_STORE', 'FK_CATALOG_PRODUCT_ENTITY_INT_PRODUCT_ENTITY'),
                    //'core_store_group'=> array('FK_CORE_STORE_GROUP_WEBSITE_ID_CORE_WEBSITE_WEBSITE_ID', 'FK_CORE_STORE_GROUP_ID_CORE_STORE_GROUP_GROUP_ID'),
                    //'core_store' => array('FK_CORE_STORE_GROUP_ID_CORE_STORE_GROUP_GROUP_ID')
            );

            foreach ($FKscope as $table => $fks) {
                foreach ($fks as $fk) {
                    try {
                        $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`"));
                        $connection->exec(new Zend_Db_Expr("ALTER TABLE `{$table}` DROP KEY `{$fk}`"));
                    } catch (Exception $e) {
                        
                    }
                }
            }


            self::$counter = 1;
        }
    }

}