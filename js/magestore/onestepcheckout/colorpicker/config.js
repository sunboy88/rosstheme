// load color for input text in custom style

function loadColor_onestepcheckout(click_id){       
           
    new colorPicker('onestepcheckout_style_management_custom',{
         previewElement:'onestepcheckout_style_management_custom',
         inputElement:'onestepcheckout_style_management_custom',
         eventName:click_id ,        
         color:'#'+$('onestepcheckout_style_management_custom').value,        
     });      
}

function loadColor_onestepcheckoutbutton(click_id){       
                     
    new colorPicker('onestepcheckout_style_management_custombutton',{
         previewElement:'onestepcheckout_style_management_custombutton',
         inputElement:'onestepcheckout_style_management_custombutton',
         eventName:click_id ,        
         color:'#'+$('onestepcheckout_style_management_custombutton').value,        
     });       
}

function toggleCustomValueElements(checkbox, container, excludedElements, checked){
    if(container && checkbox){
        var ignoredElements = [checkbox];
        if (typeof excludedElements != 'undefined') {
            if (Object.prototype.toString.call(excludedElements) != '[object Array]') {
                excludedElements = [excludedElements];
            }
            for (var i = 0; i < excludedElements.length; i++) {
                ignoredElements.push(excludedElements[i]);
            }
        }
        //var elems = container.select('select', 'input');
        var elems = Element.select(container, ['select', 'input', 'textarea', 'button', 'img']);
        var isDisabled = (checked != undefined ? checked : checkbox.checked);
        elems.each(function (elem) {
            if (checkByProductPriceType(elem)) {
                var isIgnored = false;
                for (var i = 0; i < ignoredElements.length; i++) {
                    if (elem == ignoredElements[i]) {
                        isIgnored = true;
                        break;
                    }
                }
                if (isIgnored) {
                    return;
                }
                elem.disabled=isDisabled;
                if (isDisabled) {
                    elem.addClassName('disabled');
                } else {
                    elem.removeClassName('disabled');
                }
                if(elem.tagName == 'IMG') {
                    isDisabled ? elem.hide() : elem.show();
                }
            }
        })
    }
} 


