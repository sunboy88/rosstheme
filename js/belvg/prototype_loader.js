/*
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

var prototypeLoader = Class.create();
prototypeLoader.prototype = {
    initialize: function(config)
    {
        this.options = Object.extend({
            loader:		'/images/prototype_loader/ajax-loader.gif'
        }, arguments[0] || {});
        this.options.loader	= config;
        //var loader		= this.createLoader();
        this.createLoader();
    },
    createLoader: function()
    {
        var imgLoader = '<div id="prototypeLoader" style="display:none"><img src="'+this.options.loader+'"></div>';	
        //$$('body')[0].insert(imgLoader);
        $$('body').each(function(el) {
            new Insertion.Top(el, imgLoader);
        });
        $('prototypeLoader').addClassName('prototypeLoader');
    },  
    show: function()
    {
        Event.observe(document, 'click', positionLoader);
        Event.observe(document, 'mousemove', positionLoader);
    },
    hide: function()
    {
        $('prototypeLoader').hide();
        Event.stopObserving(document, 'click', positionLoader);
        Event.stopObserving(document, 'mousemove', positionLoader);
    }
}

function _getScroll()
{
    /*if (self.pageYOffset) {
        return {scrollTop:self.pageYOffset,scrollLeft:self.pageXOffset};
    } else*/
    if (document.documentElement && document.documentElement.scrollTop) {
        return {scrollTop:document.documentElement.scrollTop,scrollLeft:document.documentElement.scrollLeft}; // Explorer 6 Strict
    } else if (document.body) {
        return {scrollTop:document.body.scrollTop,scrollLeft:document.body.scrollLeft}; // all other Explorers
    };
};

function positionLoader(e)
{
    $('prototypeLoader').show();
    scrollPos = _getScroll();
    e		= e ? e : window.event;
    cur_x	= (e.clientX) ? e.clientX : cur_x;
    cur_y	= (e.clientY) ? e.clientY : cur_y;
    left_pos	= cur_x + 13 + scrollPos['scrollLeft'];
    top_pos		= cur_y + 13 + scrollPos['scrollTop'];
    $('prototypeLoader').setStyle({
        top:top_pos+'px',
        left:left_pos+'px'
    });
}
