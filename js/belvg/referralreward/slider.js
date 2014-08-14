
var slider;

var referralSlider = Class.create();
referralSlider.prototype = {
    initialize: function(options)
    {
        this.total      = options.elements.checkout.total;
        if ($(this.total)) {
            this.totalBlock = $(this.total).up();
        } else {
            this.totalBlock = false;
        }

        this.totalUrl   = options.totalUrl;

        this.trackEl    = $(options.elements.slider.track);
        this.handleEl   = $(options.elements.slider.handle);
        this.zoomInEl   = $(options.elements.slider.zoomIn);
        this.zoomOutEl  = $(options.elements.slider.zoomOut);

        this.infoUsePointsEl    = $(options.elements.info.usePoints);
        this.infoUseDiscountEl  = $(options.elements.info.useDiscount);
        this.allPointDiscountEl = $(options.elements.info.applyAll);
        this.infoQuotePointsEl  = $(options.elements.info.quotePoints);

        this.zoomer         = null;
        this.sliderSpeed    = 0;
        this.sliderAccel    = 0;
        this.sliderMinValue = options.points.minValue;

        this.cachePrefix    = 'temp';
        this.points         = options.points;
        this.points.my     -= this.sliderMinValue;

        this.zoomBtnPressed = false;
        this.showFull       = false;
        this.waitCheckValue = false;
        this.waitCheckTime  = 2;
        this.waitAjaxValue  = false;
        this.waitAjaxTime   = 3;

        this.slider = new Control.Slider(options.elements.slider.handle, options.elements.slider.track, {
            axis        : 'horizontal',
            minimum     : 0,
            maximum     : Element.getDimensions(this.trackEl).width,
            alignX      : 0,
            increment   : 1,
            sliderValue : 0,
            onSlide     : this.scale.bind(this),
            onChange    : this.scale.bind(this)
        });

        if (this.allPointDiscountEl) {
            Event.observe(this.allPointDiscountEl, 'click', this.toggleFull.bind(this));
        }

        Event.observe(this.infoUseDiscountEl,  'keyup', this.waitCheck.bind(this));

        Event.observe(this.zoomInEl,  'mousedown', this.startZoomIn.bind(this));
        Event.observe(this.zoomInEl,  'mouseup',   this.stopZooming.bind(this));
        Event.observe(this.zoomInEl,  'mouseout',  this.stopZooming.bind(this));

        Event.observe(this.zoomOutEl, 'mousedown', this.startZoomOut.bind(this));
        Event.observe(this.zoomOutEl, 'mouseup',   this.stopZooming.bind(this));
        Event.observe(this.zoomOutEl, 'mouseout',  this.stopZooming.bind(this));
    },

    waitAjax: function (back)
    {
        var currentThis = this;
        if (back != 'back') {
            if (!this.waitAjaxValue) {
                setTimeout(function(){ currentThis.waitAjax('back'); }, 300);
            }

            this.waitAjaxValue = this.waitAjaxTime;
        } else {
            if (this.waitAjaxValue > 0) {
                this.waitAjaxValue -= 1;
                setTimeout(function() { currentThis.waitAjax('back'); }, 300);
            } else {
                this.waitAjaxValue  = false;
                this.ajaxTotal();
            }
        }

        return true;
    },

    ajaxTotal: function()
    {
        if (this.totalBlock) {
            loader.show();
            var discount = this.getCurrentPoints();

            //var html     = this.getCache(discount);
            /*if (html) {
                loader.hide();
                this.showTotal(html);
            } else {*/
                this.totalBlock.addClassName('loadCheckoutTotal');

                var currentThis = this;
                new Ajax.Request(this.totalUrl, {
                    method:      'post',
                    parameters: {'discount': discount},
                    onSuccess:   function(transport) {
                        loader.hide();

                        var result = eval('(' + transport.responseText + ')');
                        if (result.error == 0) {
                            currentThis.showTotal(result.total);
                            currentThis.setCache(discount, result.total);
                        }
                    },
                    onFailed:    function(transport) {
                        loader.hide();
                    }
                });
            //}
        }
        
        if (typeof IWD != 'undefined' && typeof IWD.OPC != 'undefined') {
            IWD.OPC.validatePayment();
        }
    },

    showTotal: function(html)
    {
        $(this.total).replace(html);
        this.totalBlock.removeClassName('loadCheckoutTotal');
    },

    getCache: function(key)
    {
        return $.jStorage.get(this.cachePrefix + key);
    },

    setCache: function(key, value)
    {
        /*if($.jStorage.storageSize() > 128000) // Clear cache
            $.jStorage.flush();*/
        $.jStorage.set(this.cachePrefix + key, value, {TTL: 200000}); // Removes a key from the storage after 3+ min
    },
    
    getCurrentPoints: function(v)
    {
        if (typeof v == 'undefined') {
            var v = this.slider.value;
        }

        return parseInt(v * this.points.my + this.sliderMinValue);
    },

    scale: function (v)
    {
        if (this.slider.value < 1 && !this.zoomer) {
            this.toggleCustom();
        }

        if (this.slider.value == 1 && this.zoomer) {
            this.stopZooming();
        }

        var setPoints   = this.getCurrentPoints(v);
        var setDiscount = this.points.cost * setPoints;

        this.infoUsePointsEl.update(setPoints);
        this.infoUseDiscountEl.value = setDiscount;

        if (this.infoQuotePointsEl && this.points.discard) {
            var quotePoints = Math.round(this.points.quote * (1 - setDiscount / this.points.subtotal));
            this.infoQuotePointsEl.update(quotePoints);
        }

        this.waitAjax('start');

        return true;
    },

    waitCheck: function (back)
    {
        var currentThis = this;
        if (back != 'back') {
            if (!this.waitCheckValue) {
                setTimeout(function() { currentThis.waitCheck('back'); }, 500);
            }

            this.waitCheckValue = this.waitCheckTime;
        } else {
            if (this.waitCheckValue > 0) {
                this.waitCheckValue -= 1;
                setTimeout(function() { currentThis.waitCheck('back'); }, 500);
            } else {
                this.waitCheckValue = false;
                this.checkValue();
            }
        }

        return true;
    },

    checkValue: function ()
    {
        var useDiscount = parseFloat(this.infoUseDiscountEl.value);
        var minDiscount = this.points.cost * this.sliderMinValue;
        var maxDiscount = this.points.cost * (this.points.my + this.sliderMinValue);
        var setDiscount = minDiscount;
        if (useDiscount) {
            if (useDiscount > maxDiscount) {
                setDiscount = maxDiscount;
            } else if (useDiscount >= minDiscount) {
                setDiscount = parseFloat(useDiscount);
            }
        }

        var setPoints       = Math.floor(setDiscount / this.points.cost);
        var v               = (setPoints - this.sliderMinValue) / this.points.my;

        this.slider.value   = v;
        this.slider.setValue(v);
        this.scale(v);
        this.waitCheckValue = false;

        return true;
    },

    toggleFull: function ()
    {
        this.showFull = !this.showFull;

        if (this.showFull) {
            this.zoomBtnPressed = true;
            this.sliderAccel    = .01;
            this.periodicalZoom();
            this.zoomer = new PeriodicalExecuter(this.periodicalZoom.bind(this), .05);
        }

        return this;
    },

    toggleCustom: function ()
    {
        this.allPointDiscountEl.checked = false;
        this.showFull = false;
        return this;
    },

    startZoomIn: function()
    {
        if (!this.slider.disabled) {
            this.zoomBtnPressed = true;
            this.sliderAccel    = .002;
            this.periodicalZoom();
            this.zoomer = new PeriodicalExecuter(this.periodicalZoom.bind(this), .05);
        }

        return this;
    },

    startZoomOut: function()
    {
        if (!this.slider.disabled) {
            this.zoomBtnPressed = true;
            this.sliderAccel    = -.002;
            this.periodicalZoom();
            this.zoomer = new PeriodicalExecuter(this.periodicalZoom.bind(this), .05);
        }

        return this;
    },

    stopZooming: function()
    {
        this.zoomBtnPressed = false;
        this.sliderAccel    = 0;
        this.sliderSpeed    = 0;
    },

    periodicalZoom: function()
    {
        if (!this.zoomer) {
            return this;
        }

        if (this.zoomBtnPressed) {
            this.sliderSpeed += this.sliderAccel;
        } else {
            this.sliderSpeed /= 1.5;
            if (Math.abs(this.sliderSpeed)<.001) {
                this.sliderSpeed = 0;
                this.zoomer.stop();
                this.zoomer = null;
            }
        }

        this.slider.value += this.sliderSpeed;
        this.slider.setValue(this.slider.value);

        return this;
    }
}

