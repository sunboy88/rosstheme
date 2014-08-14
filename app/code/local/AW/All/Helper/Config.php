<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Collpur
 * @version    1.0.6
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_All_Helper_Config extends Mage_Core_Helper_Abstract
{
    /** Extensions feed path */
    const EXTENSIONS_FEED_URL = 'http://media.aheadworks.com/feeds/extensions.xml';
    /** Updates Feed path */
    const UPDATES_FEED_URL = 'http://media.aheadworks.com/feeds/updates.xml';
    /** Estore URL */
    const STORE_URL = 'http://ecommerce.aheadworks.com/estore/';

    /** EStore response cache key*/
    const STORE_RESPONSE_CACHE_KEY = 'aw_all_store_response_cache_key';


}
