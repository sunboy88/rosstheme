function changeFormValue(){
	if (!$('widget-ajax-url')) return false;
	if (!affiliateplusWidgetForm.validator.validate())
		return false;
	//$('widget-container').hide();
	//$('widget-ajax-load').show();
	var url = $('widget-ajax-url').value;
	new Ajax.Request(url,{
		method: 'post',
		postBody: $('affiliateplus-widget-form').serialize(),
		parameters: $('affiliateplus-widget-form').serialize(),
		onException: function (xhr, e){
			alert('Exception: ' + e);
		},
		onComplete: function (xhr){
			$('widget-container').update(xhr.responseText);
			//$('widget-ajax-load').hide();
			//$('widget-container').show();
		}
	});
}

function affiliatepluswidgetInstanceSearch(){
	$('affiliatepluswidget_product_list').hide();
	$('widget-products-ajax-load').show();
	var url = $('affiliatepluswidget_search_url').value+'&search='+$('affiliatepluswidget_search').value;
	new Ajax.Request(url,{
		method: 'post',
		postBody: '',
		onException: function (xhr, e){
			alert('Exception: ' + e);
		},
		onComplete: function (xhr){
			$('affiliatepluswidget_product_list').update(xhr.responseText);
			$('widget-products-ajax-load').hide();
			$('affiliatepluswidget_product_list').show();
		}
	});
}

document.observe("dom:loaded",changeFormValue);