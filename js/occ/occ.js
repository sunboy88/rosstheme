var OccTools = Class.create();
OccTools.prototype = {
	initialize: function(){
		this.isTablet = false;		
		this.isMobile = false;
		this.runHidePopup = true;	
	},	  

	isIE: function() {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0)      
            return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
        else              
            return false;
	},
    
	showPopup: function(id){
		var block = $(id+'-popup-content');
		var blockContainer = $(id+'-popup-container');
		var viewport = document.viewport.getDimensions(); // Gets the viewport as an object literal
		var width = viewport.width; // Usable window width
		var height = viewport.height; // Usable window height
		
		var boxHeight = block.getHeight();
		var boxWidth = block.getWidth();
		
		var top = boxHeight/2;

	    if (boxHeight>=height){
	    	blockContainer.style.position = 'absolute';
	    	block.setStyle({'top' : '0%' });
	    	block.setStyle({'margin' : '50px auto 50px' });
	    } else {
	    	blockContainer.style.position = 'fixed';
	    	block.setStyle({'top' : '50%', 'margin' : '-'+top+'px auto 0'});
	    }	

		$(id+'-popup-container').setStyle({ "height" : 100+"%" });	

		//show popup
		$(id+'-popup-container').setStyle({ "left" : '0' });	

		//run scale up effect
		if (!occTools.isMobile && !occTools.isTablet && (!this.isIE() || (this.isIE() && this.isIE() >= 10))) {
	 		$(id+'-popup-content').addClassName('grow');
	 		setTimeout(function(){ $(id+'-popup-content').addClassName('shrink-to-normal'); }, 200);
	 	} else if (occTools.isMobile || occTools.isTablet) {
		 	$(id+'-popup-content').addClassName('grow-mobile');
	 	} else {
		 	$(id+'-popup-content').addClassName('shrink-to-normal');
	 	}

	 	//add popup background				
    	if (!occ.successTransition) {
    		$('occ-popup-wrapper-bkg').style.left = '0px';
    		if (!this.isIE() || (this.isIE() && this.isIE() >= 9)) {
    			$('occ-popup-wrapper-bkg').fade({ duration: 0.3, from: 0, to: 1 });
    		}
    	}
	},

	hidePopup: function(id,instant){
		if (occTools.runHidePopup){
		 	//remove popup background 
	    	if (!occ.successTransition) {
	    		$('occ-popup-wrapper-bkg').style.left = '-999999px';
    			if (!this.isIE() || this.isIE() >= 9) {
	    			$('occ-popup-wrapper-bkg').setOpacity(0);
	    		}
	    	}

			//hide popup
			if (instant) {				
				$(id+'-popup-content').removeClassName('grow');	
				$(id+'-popup-content').removeClassName('grow-mobile');
				$(id+'-popup-content').removeClassName('shrink-to-normal');
			 	$(id+'-popup-container').setStyle({ "left" : '-999999px' }); 
			 	
			} else {
			 	$(id+'-popup-content').addClassName('shrink');
			 	setTimeout(function() { 		 		
					//hide popup
			 		$(id+'-popup-container').setStyle({ "left" : '-999999px' }); 
			 		$(id+'-popup-content').removeClassName('shrink'); 
					$(id+'-popup-content').removeClassName('shrink-to-normal');
					$(id+'-popup-content').removeClassName('grow');	
					$(id+'-popup-content').removeClassName('grow-mobile');	

			 	}, 200);
			 }

			//if close address popup, set the dropdowns to the default billing and shipping addresses
			if (id=='occ-address' && occAddress.step != 'saveAddress') {
				var billingOptions = $$('select#billing-address-select option');
				billingOptions[0].selected = true;
				for (var i = 0; i < billingOptions.length; i++) {
				    if (billingOptions[i].value==$('occ_default_billing').value) {
						billingOptions[i].selected = true;
					}
				}
				
				if ($('shipping-address-select')) {
					var shippingOptions = $$('select#shipping-address-select option');
					shippingOptions[0].selected = true;
					for (var i = 0; i < shippingOptions.length; i++) {
					    if (shippingOptions[i].value==$('occ_default_shipping').value) {
							shippingOptions[i].selected = true;
						}
					}
				}
			}
			if (id=="occ"){
				$('occ-popup-content').removeClassName('success-popup');
			}
		} else {
			occTools.runHidePopup = true;
		}
	},

	setLoadWaiting: function(step, keepDisabled) {
		if ($(step+'-buttons-container')) {
			if (keepDisabled) {
				occ.disableQtyElements();
				if ($('use_points')) {
					$('use_points').disabled = true;
				}
				if ($('points_amount')) {
					$('points_amount').disabled = true;
				}

				var container = $(step+'-buttons-container');
				container.addClassName('disabled');
				container.setStyle({opacity:.5});
				this._disableEnableAll(container, true);
				Element.show(step+'-please-wait');
			} else {
				if ($('use_points')) {
					$('use_points').disabled = false;
				}
				if ($('points_amount') && $('use_points') && $('use_points').checked == true) {
					$('points_amount').disabled = false;
				}
				var container = $(step+'-buttons-container');
				var isDisabled = (keepDisabled ? true : false);
				if (!isDisabled) {
					container.removeClassName('disabled');
					container.setStyle({opacity:1});
				}
				this._disableEnableAll(container, isDisabled);
				Element.hide(step+'-please-wait');
			}
		}
	},

	resetLoadWaiting: function(step){
		if ($(step+'-buttons-container')) {
			this.setLoadWaiting(step, false);
		}
	},	
	
	_disableEnableAll: function(element, isDisabled) {
		var descendants = element.descendants();
		for (var k in descendants) {
			descendants[k].disabled = isDisabled;
		}
		element.disabled = isDisabled;
	},
	
	updateSection: function(content, id) {
        var js_scripts = content.extractScripts();
        content = content.stripScripts();        
        
		$(id).update(content);
        
		for (var i=0; i< js_scripts.length; i++){
            if (typeof(js_scripts[i]) != 'undefined'){
            	var js_script = js_scripts[i]
            	if (id=='occ-shipping-method'){
            		js_script = js_script.replace('if(shippingMethod && shippingMethod.validator) {', 'if(occ && occ.shippingMethodValidator) {').replace('shippingMethod.validator.reset(item);', 'occ.shippingMethodValidator.reset(item);').replace("if (typeof shippingMethod != 'undefined' && shippingMethod.validator) {",'if(occ && occ.shippingMethodValidator) {');
            	}
                this.jsEval(js_script);
            }
        }
	},
	
	jsEval: function(src){
    	if (window.execScript) {
    	    window.execScript(src);
    	    return;
    	}
    	var run = function() {
    	    window.eval.call(window,src);
    	};
    	run();
	},
	
	pulsate: function(element) {
	    element = $(element);
	    var options    = arguments[1] || { },
	        oldOpacity = element.getInlineOpacity(),
	        transition = options.transition || Effect.Transitions.linear,
	        reverser   = function(pos){
	        	return 1 - transition((-Math.cos((pos*(options.pulses||1)*2)*Math.PI)/2) + .5);
	        };
	    
	    return new Effect.Opacity(element,
	        Object.extend(Object.extend({  duration: 0.5, from: 0,
	        	afterFinishInternal: function(effect) { effect.element.setStyle({opacity: oldOpacity}); }
	        }, options), {transition: reverser}));
	}
}

var occTools = new OccTools();

var OccLogin = Class.create();
OccLogin.prototype = {
	//main functions
	initialize: function(urls){
        this.loginUrl = urls.login;     
        this.postLoginUrl = urls.post_login;
        this.logoutUrl = urls.logout;
        this.logoutText = urls.logout_text;
        this.failureUrl = urls.failure; 
        this.onSave = this.save.bindAsEventListener(this);
    },	   
	
	save: function(transport){
		if (transport && transport.responseText){
			try{
				response = eval('(' + transport.responseText + ')');
			}
			catch (e) {
				response = {};
			}
		}

		if (response.error){
			if ((typeof response.message) == 'string') {
				alert(response.message);
			} else {
				alert(response.message.join("\n"));
			}
			
			occTools.resetLoadWaiting('login');
			occTools.resetLoadWaiting('login-mini');
			return false;
		}	

		if (response.notice){
			alert(response.notice);
			$('occ-customer-info').hide();
		}

		//redirect in case ajax expired
		if (response.redirect) {
			location.href = response.redirect;
            return;
        }
        
        //update occ/address blocks
		if (response.update_section) {
			if (response.update_section.html_login) {
				occTools.updateSection(response.update_section.html_login, 'occ-login-popup-content');
			}
			
			if (response.update_section.html_address_select || response.notice) {
				if (response.update_section.html_address_select) {
				    var billingSelect = $('occ-billing-address');
	    		    if (billingSelect) {	
	    		    	billingSelect.update(response.update_section.html_address_select.billing);
	    		    	$('occ_default_billing').value = response.update_section.html_address_select.default_billing;
	    		    }  	
		        	if ($('billing-address-select')) {
		        		$('billing-address-select').removeClassName('required-entry');
		        	}
		        	
				    var shippingSelect = $('occ-shipping-address');
	    		    if (shippingSelect) {	
	    		    	shippingSelect.update(response.update_section.html_address_select.shipping);
	    		    	$('occ_default_shipping').value = response.update_section.html_address_select.default_shipping;
	    		    }      
		        	if ($('shipping-address-select')) {
		        		$('shipping-address-select').removeClassName('required-entry');
		        	}
		        }
		
	    		//update welcome message
	    		if (response.update_section.welcome && $$('.welcome-msg')[0]) {
			    	var welcomeMsg = $$('.welcome-msg')[0];
    		    	welcomeMsg.innerHTML = response.update_section.welcome;
    		    }
    		    
    		    //update login link
    			this.updateLoginLink();	    				    
    		    
    		    //update cart sidebar
    		    occ.updateCart(response);
	    		
	    		//display address fields
    		    if (!response.notice) {
    		    	document.getElementById('occ-customer-login').style.display = 'none';	
    		    	document.getElementById('occ-customer-address').style.display = 'block';	
    		    }
			}
				
		}
		
		if (response.popup) {
			occTools.resetLoadWaiting('login');
			occTools.showPopup(response.popup);	
		}
		
    	occTools.resetLoadWaiting('login-mini');
		if (response.close_popup) {
			occTools.hidePopup('occ-login', true);
		}
	},	
	
	failure: function(){
		location.href = this.failureUrl;
	},
	
	loadLoginPopup: function() {
		occTools.setLoadWaiting('login', true);		
		new Ajax.Request(this.loginUrl, {
			 	method: 'post',
				onSuccess: this.onSave,
			    onFailure: this.failure.bind(this)
			}
		); 
	},	
	
	postLogin: function() {
    	var dataForm = new VarienForm('login-form-validate', true);
        if (dataForm.validator.validate()){
			occTools.setLoadWaiting('login-mini', true);
			new Ajax.Request(this.postLoginUrl, {
			 	method: 'post',
				parameters: Form.serialize('login-form-validate'),
				onSuccess: this.onSave,
				onFailure: this.failure.bind(this) 
			});
		}
	},
	
	updateLoginLink: function() {   	
    	var logoutTxt = this.logoutText;
    	var logoutLink = this.logoutUrl;
    	if ($$('.links li')[0]) {
	    	var links = $$('.links li');
	    	links.each(function (elem) {
	    	    var loginElement = $(elem).down();
	    	    if (loginElement && loginElement.href.indexOf('customer/account/login/')>0) {
	    	    	loginElement.innerHTML = logoutTxt;
	    	    	loginElement.href = logoutLink;
	    	    }    			
	    	});
    	}
	}
}

var OccAddress = Class.create();
OccAddress.prototype = {
	//main functions
	initialize: function(urls){
        this.initUrl = urls.initialize;      
        this.addressUrl = urls.address;
        this.saveAddressUrl = urls.save_address;
        this.failureUrl = urls.failure; 
        this.onSave = this.save.bindAsEventListener(this);
        this.step = '';
        
		Event.observe(window, 'load', function() {
        	if ($('billing-address-select')) {
        		$('billing-address-select').removeClassName('required-entry');
        	}
        	if ($('shipping-address-select')) {
        		$('shipping-address-select').removeClassName('required-entry');
        	}
        });
    },	  
	
	save: function(transport){
		if (transport && transport.responseText){
			try{
				response = eval('(' + transport.responseText + ')');
			}
			catch (e) {
				response = {};
			}
		}

		if (response.error){
			if ((typeof response.message) == 'string') {
				alert(response.message);
			} else {
				alert(response.message.join("\n"));
			}
			
			occTools.resetLoadWaiting('customer-info');
			$('billing-address-select').disabled = false;
			if ($('shipping-address-select')) {
				$('shipping-address-select').disabled = false;
			}
			occTools.resetLoadWaiting('address');
			return false;
		}

		//redirect in case ajax expired
		if (response.redirect) {
			location.href = response.redirect;
            return;
        }
        
        //update occ/address blocks
		if (response.update_section) {
			if (response.update_section.html_address && this.step != 'saveAddress') {
				occTools.updateSection(response.update_section.html_address, 'occ-address-popup-content');
			}
			
			if (response.update_section.html_address_select) {
			    var billingSelect = $('occ-billing-address');
    		    if (billingSelect) {	
    		    	billingSelect.innerHTML = response.update_section.html_address_select.billing;
    		    }
			    var shippingSelect = $('occ-shipping-address');
    		    if (shippingSelect) {	
    		    	shippingSelect.innerHTML = response.update_section.html_address_select.shipping;
    		    }
			}
			
			if (response.update_section.html_review) {
				occ.updateSections(response);
				occ.init();
			}
		}
		
		if (response.popup) {
			occTools.resetLoadWaiting('customer-info');
			$('billing-address-select').disabled = false;
			if ($('shipping-address-select')) {
				$('shipping-address-select').disabled = false;
			}
			occTools.showPopup(response.popup);	
		}
		
		if (response.close_popup) {
			occTools.hidePopup('occ-address', true);
		}
		
		if (this.step == 'saveAddress') {
			this.step = '';
		} else {
			occTools.resetLoadWaiting('address');
		}
	},	
	
	failure: function(){
		location.href = this.failureUrl;
	},
	
	// Address funtions
	newAddress: function(value, type) {
		if (value=='addAddress') {
			this.loadAddressPopup(type);
		} else {
			return false;
		}
	},	
	
	loadAddressPopup: function(type) {
		occTools.setLoadWaiting('customer-info', true);
		$('billing-address-select').disabled = true;
		if ($('shipping-address-select')) {
			$('shipping-address-select').disabled = true;
		}
		
		new Ajax.Request(this.addressUrl, {
			 	method: 'post',
				parameters: {address_type: type},
				onSuccess: this.onSave,
			    onFailure: this.failure.bind(this)
			}
		); 
	},	
	
	saveAddress: function() {
    	var dataForm = new VarienForm('form-validate', true);
    	this.step = 'saveAddress';
        if (dataForm.validator.validate()){
			occTools.setLoadWaiting('address', true);
			new Ajax.Request(this.saveAddressUrl, {
			 	method: 'post',
				parameters: Form.serialize('form-validate'),
				onSuccess: this.onSave,
				onFailure: this.failure.bind(this)
			});
		}
	},
	
	// Initialize Occ
	initOcc: function(skip){
		//add product to cart via AW Ajax Cart Pro if extension is enabled
		if ($('product_addtocart_form')) {
			if (typeof(AW_AjaxCartProUI) !== 'undefined' && !skip) {    
				this.originalBeforeUpdate = AW_AjaxCartProUI.blocks['add_confirmation'].beforeUpdate;
				AW_AjaxCartProUI.blocks['add_confirmation'].beforeUpdate = function(args) {occAddress.initOcc(true);};
			    AW_AjaxCartProUI._show = function _show(el) {
					occTools.setLoadWaiting('customer-info', true);
			    };

		        productAddToCartForm.submit(this);
				return;
			} else if (typeof(AW_AjaxCartProUI) !== 'undefined' && skip) {
				setTimeout( function () {
					AW_AjaxCartProUI._show = function _show(el) {
			        	el.removeClassName(AW_AjaxCartProUI.hideCls);
			        	el.addClassName(AW_AjaxCartProUI.showCls);
				    };
				    AW_AjaxCartProUI._clickOnOverlay();
				    AW_AjaxCartProUI.blocks['add_confirmation'].beforeUpdate = this.originalBeforeUpdate;
				}, 100);
			}
		}

		var billing_address_id = $('billing-address-select').value;
		if ($('shipping-address-select')) {
			var shipping_address_id = $('shipping-address-select').value;
		} else {
			var shipping_address_id = billing_address_id;		
		}
	 
	 	if (billing_address_id=='' || shipping_address_id=='') {
	 		alert(Translator.translate('Please select an address.'));
			occTools.resetLoadWaiting('customer-info');
	 		return;
	 	}
	 	
		var params = new Array();
		if ($('product_addtocart_form')) {		
	        var occProductAddToCartForm = new VarienForm('product_addtocart_form');		
			if (!occProductAddToCartForm.validator.validate()) {
            	return false;
	 		}
		    params = Form.serialize('product_addtocart_form');
		} 
		
		params.billing_address_id = billing_address_id;
		params.shipping_address_id = shipping_address_id;
		
		$('billing-address-select').disabled = true;
		if ($('shipping-address-select')) {
		    $('shipping-address-select').disabled = true;
		}			
		occTools.setLoadWaiting('customer-info', true);
		
		new Ajax.Request(this.initUrl, {
		  method: 'post',
		  onSuccess: this.onSave,
		  onFailure: this.failure.bind(this),
		  parameters: params
		});
	}
}

//OCC class
var Occ = Class.create();
Occ.prototype = {
	//main functions
	initialize: function(urls){
		this.form = payment.form;		
        this.validator = new Validation(this.form);
        this.shippingMethodValidator = new Validation('occ-shipping-method');
        this.paymentMethodValidator = new Validation('occ-payment-method');
        
        this.updateBlocksUrl = urls.updateBlocks;
        this.updateCartUrl = urls.updateCart;
        this.saveShippingMethodUrl = urls.saveShippingMethod;
        this.savePaymentMethodUrl = urls.savePaymentMethod;
        this.savePointsUrl = urls.savePoints;
        this.reviewUrl = urls.review;
        this.saveUrl = urls.save;
        this.successUrl = urls.success; 
        this.failureUrl = urls.failure; 
        this.onSave = this.save.bindAsEventListener(this);
        this.successTransition = false;

        this.cartSidebar = 'occ-cart-sidebar';
    },
    
    init: function(){
    	//clear previous success block
		$('occ-success').innerHTML = '';
		
    	//set observer for whenever shipping method is changed to save it
        $$('#occ-shipping-method input[name="shipping_method"]').invoke('observe','click', this.saveShippingMethod.bind(this));
    	
    	//save shipping method if only one is present
    	var methods = document.getElementsByName('shipping_method');
        if (methods.length==1 && !this.reinitialize) {
    		this.saveShippingMethod();
		} else if (this.reinitialize) {
			this.reinitialize = false;	
		}

    	//set observer for whenever payment method is changed to save it
		// var elements = Form.getElements(this.form);
  		// for (var i=0; i<elements.length; i++) {
  		//     if (elements[i].name=='payment[method]' && elements[i].value=='checkmo') {
  		//         Event.observe(elements[i].id, 'click', this.savePaymentMethod.bind(this));
  		//     }
  		// }  
		var elements = Form.getElements(this.form);
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[use_points]') {
                Event.observe(elements[i].id, 'change', this.savePoints.bind(this));
            }
            if (elements[i].name=='payment[points_amount]') {   
                Event.observe(elements[i].id, 'change', this.savePoints.bind(this));
            }
        }  
     
		
        //observe when form submit
		if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);});
        }  
        
        //remove form tag from checkout agreements
        if ($('checkout-agreements')) {
        	var agreementsElement = $('checkout-agreements');
        	var content = agreementsElement.innerHTML;
	    	var tempDiv = document.createElement('div');
	    	tempDiv.innerHTML = content;
			
        	var agreementsParent = agreementsElement.parentNode;
	    	agreementsParent.replaceChild(tempDiv.children[0], agreementsElement);
        }
        
        payment.initWhatIsCvvListeners();        
    },
    
	save: function(transport){
		if (transport && transport.responseText){
	        try{
	            response = eval('(' + transport.responseText + ')');
	        }
	        catch (e) {
	            response = {};
	        }
	    }
	    
		var validateResult = this.validate(response);
		
		if (response.redirect) {
            location.href = response.redirect;
            return;
        } 

		if (this.step == 'initPlaceOrder' && validateResult){	
			this.step = '';
			this.placeOrder();
			return;
		} 	
		
		if (this.step == 'updateQty') {
			this.step = '';
			if (response.close_popup) {
			    this.close_popup = true;
			}
			this.updateBlocks();
			return;
		}
		
		if (response.update_section) {
			this.updateSections(response);
		}
		
		//load success page with ajax or redirect to success page if on shopping cart page
		if (response.success) {
			if ($('product_addtocart_form')) {
				this.successRedirect();
				return;
			} else {
				window.location = this.successUrl;
				return;
			}
		}		
		
		if (this.close_popup) {
			occTools.hidePopup('occ', true);
			this.close_popup = false;
		} else if(response.close_popup == 'success') {
			this.successTransition = true;
			occTools.hidePopup('occ', false);
			setTimeout(function() {occTools.showPopup('occ');occTools.resetLoadWaiting('occ-review'); occ.successTransition = false; }, 320);
			return;
		}
				
		this.step = '';
		occTools.resetLoadWaiting('occ-review');
	},	
		
	validate: function(response){
	    if (response.error){
			if (this.step == 'savePaymentMethod' || this.step == 'initPlaceOrder'){
				this.step = '';
				if (response.fields) {
					var fields = response.fields.split(',');
					for (var i=0;i<fields.length;i++) {
						var field = null;
						if (field = $(fields[i])) {
							Validation.ajaxError(field, response.error);
						}
					}
					return false;
				}
				alert(response.error);
				return false;	
			} else if (this.step == 'placeOrder'){
				this.step = '';
                var msg = response.error_messages;
                if (typeof(msg)=='object') {
                    msg = msg.join("\n");
                }
                if (msg) {
                    alert(msg);
                }		
				return false;				
			} else {
				if ((typeof response.message) == 'string') {
					alert(response.message);
				} else {
					alert(response.message.join("\n"));
				}
				return false;				
			}
	    }
		return true;
	},
	
	failure: function(){
		location.href = this.failureUrl;
	},
	
	//update block functions
	updateSections: function(response){
		if (response.update_section.html_layout_messages) {
		    $('occ-layout-messages').update(response.update_section.html_layout_messages);
		} else {
			$('occ-layout-messages').update('');
		}
		if (response.update_section.html_shipping_method) {
			occTools.updateSection(response.update_section.html_shipping_method, 'occ-shipping-method');
		}
		if (response.update_section.html_available) {
			occTools.updateSection(response.update_section.html_available, 'checkout-shipping-method-load');	
		}
		if (response.update_section.remove_shipping) {
			$('occ-shipping-method').update('');
		}
		if (response.update_section.html_payment) {
			occTools.updateSection(response.update_section.html_payment, 'occ-payment-method');	
		}
		if (response.update_section.html_review) {
			occTools.updateSection(response.update_section.html_review, 'occ-review');			
		}
		if (response.update_section.html_review_info) {
			occTools.updateSection(response.update_section.html_review_info, 'occ-review-load');			
		}
		if (response.update_section.html_success) {
			setTimeout(function(){occTools.updateSection(response.update_section.html_success, 'occ-success'); $('occ-popup-content').select('button.button').each(function(section) {$(section).onclick = function() {occTools.hidePopup('occ',true)}}); },200)						
		}
		if (response.points_amount && $('points_amount')) {
        	$('points_amount').value = response.points_amount;
		}
		if (this.reinitialize) {		
			this.init();
		}
		this.updateCart(response);
		/* this.updateCartPage(response); */
	},
    
/* 	updateCartPage: function(response){
		if (response.update_section.html_cart_page) {
    		var oldCart = $$('.cart')[0];
			
    		if (oldCart) {	
	    	    var tempCartDiv = document.createElement('div');
	    	    tempCartDiv.innerHTML = response.update_section.html_cart_page;
			
	    	    var tempParent = oldCart.parentNode;
	    	    tempParent.replaceChild(tempCartDiv.children[0], oldCart); 
	    	}
	    }
	}, */	
	
	updateCart: function(response){
	    if (response.update_section.html_cart) {
			for (var i=0;i<response.update_section.html_cart.length;i++) {
				if (response.update_section.html_cart[i] && document.getElementById(this.cartSidebar+i)) {
			    	var content = response.update_section.html_cart[i];  			 				    					 						    	
					occTools.updateSection(content,(this.cartSidebar+i));	

				    truncateOptions();						        
			    }
		    }
	    }

	    this.updateCartLink(response);
	},
	
	updateCartLink: function(response){
		if (response.update_section.html_cart_link) {
    		var link = $$('.top-link-cart')[0];
			
    		if (link) {	
    			link.innerHTML = response.update_section.html_cart_link;
    		}

    		if ($$('#header .header-minicart .count').length>0) {
	    		var str = response.update_section.html_cart_link;
				var pattern = /[0-9]+/g;
				var count = str.match(pattern);
				if (count!=null) {
					jQuery('#header .header-minicart .count').parent().removeClass('no-count');
					jQuery('#header .header-minicart .count').text(count);
				} else {
					jQuery('#header .header-minicart .count').parent().addClass('no-count');
				}
			}
    	}
	},
	
	//update cart qty functions
	updateQty: function(){ 
		var params = Form.serialize(this.form);
		occTools.setLoadWaiting('occ-review', true);
		this.step = 'updateQty';
		var request = new Ajax.Request(
			this.updateCartUrl,
			{
				method:'post',
				onSuccess: this.onSave,
				onFailure: this.failure.bind(this),
				parameters: params
			}
		);
    },   
	
	updateBlocks: function(){ 
		this.reinitialize = true;	
		var request = new Ajax.Request(
			this.updateBlocksUrl,
			{
				method:'post',
				onSuccess: this.onSave,
				onFailure: this.failure.bind(this),
				parameters: Form.serialize(this.form)
			}
		);
    },    
	
	qtyUp: function(id){   
		var oldValue = parseFloat($('occ_item_'+id).value);
		$('occ_item_'+id).value = oldValue + 1;
	    this.updateQty();
    }, 
	
	qtyDown: function(id){   
		var oldValue = parseFloat($('occ_item_'+id).value);
		if (oldValue>1 || (oldValue==1 && confirm('Are you sure you would like to remove this item from the shopping cart?'))) {
	 		$('occ_item_'+id).value = oldValue - 1;
		    this.updateQty();
		} 
    }, 
    
    handleEnter: function(inField, e) {
	    var charCode;
	    
	    if(e && e.which){
	        charCode = e.which;
	    }else if(window.event){
	        e = window.event;
	        charCode = e.keyCode;
	    }
	
	    if(charCode == 13) {        	
	        this.updateQty();
	    }
	},
    
    disableQtyElements: function() {
	    var elements = $$('.occ-qty');
        for (var i=0; i<elements.length; i++) {
            elements[i].readOnly = true;
        }
	    var elements = $$('.occ-qty-up');
        for (var i=0; i<elements.length; i++) {
            elements[i].disabled = true;
            elements[i].addClassName('disabled');
        }
	    var elements = $$('.occ-qty-down');
        for (var i=0; i<elements.length; i++) {
            elements[i].disabled = true;
            elements[i].addClassName('disabled');
        }
    },
	
	//checkout functions
	//shipping method functions	
	validateShippingMethod: function() {
        var methods = document.getElementsByName('shipping_method');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.'));
            return false;
        }

        if(!this.shippingMethodValidator.validate()) {
            return false;
        }

        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        
        alert(Translator.translate('Please specify shipping method.').stripTags());
        return false;
    },	
	
	saveShippingMethod: function(){
        if (this.validateShippingMethod()) {
        	var params = Form.serialize(this.form);
			occTools.setLoadWaiting('occ-review', true);
            var request = new Ajax.Request(
                this.saveShippingMethodUrl,
                {
                    method:'post',
                    onSuccess: this.onSave,
                    onFailure: this.failure.bind(this),
                    parameters: params
                }
            );
        } 
    },
	
	//payment method funtions
    //set non-payment elements to enabled
	afterPaymentInit: function(){
        var elements = Form.getElements(this.form);
        for (var i=0; i<elements.length; i++) {
            elements[i].disabled = false;
        }
        
        var paymentElements = Form.getElements('occ-payment-method');
        for (var i=0; i<paymentElements.length; i++) {
            if (paymentElements[i].name!='payment[method]' && paymentElements[i].name!='payment[use_points]' && paymentElements[i].name!='payment[points_amount]') {                
                paymentElements[i].disabled = true;
            }
        }

    },
	
    savePaymentMethod: function(){
        if (payment.validate() && this.paymentMethodValidator.validate()) {
        	var params = Form.serialize(this.form);
			occTools.setLoadWaiting('occ-review', true);
			this.step = 'savePaymentMethod';
            var request = new Ajax.Request(
                this.savePaymentMethodUrl,
                {
                    method:'post',
                    onSuccess: this.onSave,
                    onFailure: this.failure.bind(this),
                    parameters: params
                }
            );
        }
    },
	
    savePoints: function(){
    	if ($('points_amount') && $('use_points') && $('use_points').checked == true) {
			$('points_amount').disabled = false;
		}
    	var params = Form.serialize(this.form);
		occTools.setLoadWaiting('occ-review', true);
		this.step = 'savePoints';
        var request = new Ajax.Request(
            this.savePointsUrl,
            {
                method:'post',
                onSuccess: this.onSave,
                onFailure: this.failure.bind(this),
                parameters: params
            }
        );
    },
	
	/* Review Functions */
	initPlaceOrder: function(){
        if (payment.validate() && this.validator.validate()) {
			this.step = 'initPlaceOrder';
			var params = Form.serialize(this.form);
			occTools.setLoadWaiting('occ-review', true);
            var request = new Ajax.Request(
                this.savePaymentMethodUrl,
                {
                    method:'post',
                    onSuccess: this.onSave,
                    onFailure: this.failure.bind(this),
                    parameters: params
                }
            );
        }
    },
	
	placeOrder: function(){ 
		this.step = 'placeOrder';
    	if ($('use_points')) {
			$('use_points').disabled = false;
		}
    	if ($('points_amount')) {
			$('points_amount').disabled = false;
		}
		var params = Form.serialize(this.form);
		params.save = true;
		occTools.setLoadWaiting('occ-review', true);
		var request = new Ajax.Request(
			this.saveUrl,
			{
				method:'post',
				onSuccess: this.onSave,
				onFailure: this.failure.bind(this),
				parameters: params
			}
		);
    },
	
	successRedirect: function(){
		var request = new Ajax.Request(
			this.successUrl,
			{
				method:'post',
				onSuccess: this.onSave,
				onComplete:this.updateSuccess.bind(this),
				onFailure: this.failure.bind(this)
			}
		);
	},
	
	updateSuccess: function(){
		var self = this;
		setTimeout(function(){ self.clearBlocks(); }, 200);
	},
	
	//clear previous occ blocks
	clearBlocks: function(){
		$('occ-layout-messages').innerHTML = '';
		$('occ-shipping-method').innerHTML = '';
		$('occ-payment-method').innerHTML = '';
		$('occ-review').innerHTML = '';
		$('occ-popup-content').addClassName('success-popup');
	}

}

