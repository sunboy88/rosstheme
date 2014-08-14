var Popup = {
    init: function (url, page) {
        //default size window
        var width = 200;
        var height = 300;
        var windowId = $("aw_popup_window");
        new Ajax.Request(url, {
            method: 'get',
            onSuccess: function (transport) {
                var json = transport.responseText.evalJSON(true);
                if (!json.error) {
                    if (json.width > width) {
                        width = json.width;
                    }
                    if (json.height > height) {
                        height = json.height;
                    }

                    // set size window
                    windowId.style.width = width + 'px';
                    windowId.style.height = height + 'px';
                    windowId.style.marginLeft = '-' + (width / 2) + 'px';

                    // set position window
                    switch (json.align) {
                        case '2':
                            windowId.style.marginTop = '-' + (height / 2) + 'px';
                            windowId.style.top = '50%';
                            break;
                        case '3':
                            windowId.style.marginTop = '-' + ( parseInt(height) + 40) + 'px';
                            windowId.style.top = '100%';
                            break;
                        case '1':
                        default:
                            windowId.style.marginTop = '20px';
                    }
                    windowId.style.left = '50%';
                    $('aw_popup_title').innerHTML = json.title;
                    $('aw_popup_content').innerHTML = json.popup_content;

                    Popup.showWindow();

                    /* Hide Automatically */
                    if (json.auto_hide_time) {
                        setTimeout(function () {
                            Popup.hideWindow();
                        }, parseInt(json.auto_hide_time) * 1000);
                    }
                }
            }
        });
    },

    showWindow: function () {
        Effect.Appear($('aw_popup_wraper'), { duration: 0.3, from: 0.0, to: 0.5 });
        Effect.Appear($('aw_popup_window'), { duration: 0.3 });
    },

    hideWindow: function () {
        Effect.Fade($('aw_popup_window'), { duration: 0.3 });
        Effect.Fade($('aw_popup_wraper'), { duration: 0.3 });
    }
}