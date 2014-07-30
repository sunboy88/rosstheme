try {

	(function ($) {

		var methods = {
			init: function (options) {

				var opts = $.extend( {}, $.fn.colorSwatches.defaults, options );

				return this.each(function () {

					var $this = $(this),
						data = $this.data('colorswatches');

					if (!data) {
						$(this).data('colorswatches', {
							target: $this,
							options: opts
						});

						var label = $.trim($this.find('label').justText());
						var select = $this.next().find('select');
						if (select.length && $.inArray(label, opts.swatchKeys) > -1) {
							createSwatches(opts, label, select);
							if (select.hasClass('super-attribute-select')) {
								select.on('change.colorswatches', function () {
									setTimeout(function () {
										recreateSwatches(opts, $(select)[0].nextSetting);
									}, 100);
								});
							}
							if ( !opts.hideDropdowns ) {
								select.on('change.colorswatches', function () {
									var data = $(this).data('swatch');
									if (data.swatchElement) {
										$(data.swatchElement).children().removeClass('current');
										$(data.swatchElement).children().eq($(this)[0].selectedIndex-1).addClass('current');
									}
									replaceImages(opts);
								});
							}
						}
					}
				});
			},
			destroy: function () {

				return this.each(function () {
					var $this = $(this),
						data = $this.data('colorswatches');

					$(window).unbind('.colorswatches');
					data.colorswatches.remove();
					$this.removeData('colorswatches');

				});

			}
		};

		var findSwatch = function(key, value, swatches) {
			for (var i in swatches) {
				if (swatches[i].key == key && swatches[i].value == value)
					return swatches[i];
			}
			return null;
		};

		var findProductId = function() {
			//check that all dropdowns has value
			var allSelected = true,
				$dropdowns = $('#product-options-wrapper select');
			$dropdowns.each(function(){
				if ( isNaN(parseInt($(this).val(), 10))) {
					allSelected = false;
				}
			});
			if ( !allSelected ) return '';

			var attribute = $dropdowns.last().attr('id').replace(/[a-z]*/, ''),
				value = $dropdowns.last().val();
			options = spConfig['config']['attributes'][attribute]['options'];
			for (var i in options ) {
				if (options[i].id == value)
					return options[i].products[0];
			}
			return '';
		};

		var createSwatches = function (opts, label, select) {

			var $swatchesContainer = $('<div/>').attr({'class': 'colorswatches-container'});
			select.parent().append($swatchesContainer);
			select.data('swatch', {
				swatchLabel: label,
				swatchElement: $swatchesContainer
			});

			if ( opts.hideDropdowns ) {
				select.css({visibility: 'hidden', position: 'absolute'});
			}

			$('option', select).each(function (i) {
				if ( !isNaN(parseInt($(this).attr('value'), 10))) {
					var $swatchContainer = $('<div/>').attr({'class': 'colorswatch'});
					var key = $(this).text();
					if ($(this).attr('price')) key = $.trim(key.replace(/\+([^+]+)$/, ''));

					var item = findSwatch(label, key, opts.swatches);
					if (item) {
						$swatchContainer.append( $('<img/>').attr({
							src: opts.swatchDir + item.img,
							alt: key,
							title: key
						}) );
						if ( opts.attributeTitle ) {
							$swatchContainer.append( $('<span>'+key+'</span>') );
						}
					} else {
						$swatchContainer.append( $('<a>'+key+'</a>') );
					}
					if ( $(this).attr('selected') == 'selected' ) $swatchContainer.addClass('current');

					$swatchContainer.on('click', function () {
						$(select)[0].selectedIndex = i;
						fireEvent($(select)[0], 'change');
						$swatchesContainer.find('.colorswatch').removeClass('current');
						$swatchContainer.addClass('current');
						replaceImages(opts);
						return false;
					});
					$swatchesContainer.append($swatchContainer);
				}
			});
		};

		var lastProductID = 0;
		var replaceImages = function(opts) {
			if ( typeof spConfig == 'undefined' || !opts.replaceImage ) return;

			var productID = findProductId();
			if ( !productID || lastProductID == productID) return;
			lastProductID = productID;

			var url = opts.imagesUrl.replace('PRODUCT_ID', productID);
			if ('https:' == document.location.protocol) {
				url = url.replace('http:', 'https:');
			}
			$('.colorswatches-loading').clone().prependTo('.product-img-box').show();
			$('.product-img-box').first().load(url);
		}

		var recreateSwatches = function (opts, select) {
			if (typeof select == 'undefined' || !select) {
				return;
			}

			var data = $(select).data('swatch');
			if (data.swatchElement) {
				$(data.swatchElement).remove();
				data.swatchElement = null;
			}

			if (!$(select).prop("disabled") )
				createSwatches(opts, data.swatchLabel, $(select));
			if (select.nextSetting)
				recreateSwatches(opts, select.nextSetting);
		};

		var fireEvent = function(element,event){
			if (document.createEventObject) {
				var evt = document.createEventObject();
				return element.fireEvent('on'+event,evt)
			} else {
				var evt = document.createEvent("HTMLEvents");
				evt.initEvent(event, true, true );
				return !element.dispatchEvent(evt);
			}
		}

		$.fn.colorSwatches = function (method) {

			if (methods[method]) {
				return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
			} else if (typeof method === 'object' || !method) {
				return methods.init.apply(this, arguments);
			} else {
				$.error('Method ' + method + ' does not supported by Olegnax colorswatches');
			}

		};

		$.fn.colorSwatches.defaults = {
			hideDropdowns: 1,
			replaceImage: 1,
			attributeTitle: 0,
			swatches: [],
			swatchKeys: []
		};

		$.fn.justText = function () {
			return $(this).clone()
				.children()
				.remove()
				.end()
				.text();
		}

	})(jQuery);

}
catch (e) {
	alert("Olegnax ColorSwatches error:" + e.message);
}