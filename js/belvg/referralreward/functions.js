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

    function blurFormValidate(type, thisF)
    {
        var error = 0;
        var value = $(thisF).value;
        switch (type) {
            case 'validate-email':
                var ck_email = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
                if (!ck_email.test(value)) {
                    error = 1;
                }
                break;

            case 'validate-password':
                if (value.length <= 3) {
                    error = 1;
                }
                break;

            case 'validation-name':
                if (value.length == 0) {
                    error = 1;
                }
                break;

            default:
                break;
        }

        if (error) {
            $(thisF).up('.email-form li').removeClassName('checked');
            $(thisF).up('.email-form li').addClassName('checked-error');
        } else {
            $(thisF).up('.email-form li').removeClassName('checked-error');
            $(thisF).up('.email-form li').select('.validation-advice').each(function(item){item.remove();});
            $(thisF).up('.email-form li').addClassName('checked');
        }
    }

    function change_all_checked()
    {
        $('table-invitation-emails').select('input').each(function(index) {
            index.checked = $('select-all').checked;
        });
    }

    function fb_change_all_checked()
    {
        $('friendslist').select('input').each(function(index) {
            index.checked = $('fb-select-all').checked;
        });
    }

    function modalwindow_description(blockId, width)
    {
        loader.show();
        Dialog.info($(blockId).innerHTML, {
            draggable:    true,
            resizable:    false,
            closable:     true,
            className:    "lighting_window",
            width:        width,
            zIndex:       1000,
            recenterAuto: false,
            onShow:       function() {
                $(this.id + '_focus_anchor').focus = function() {
                    // be happy, internet explorer, we don't need to focus here
                };
                loader.hide();
            },
            hideEffect:    Element.hide,
            showEffect:    Element.show,
            id:            "modalwindow_description",
            buttonClass:   "form-button button"
        });
    }

    function modalwindow_url(url,width)
    {
        loader.show();
        Dialog.info({url:url}, {
            draggable:    true,
            resizable:    false,
            closable:     true,
            className:    "lighting_window",
            width:        width,
            zIndex:       1000,
            recenterAuto: false,
            onShow:       function() {
                $(this.id + '_focus_anchor').focus = function() {
                    // be happy, internet explorer, we don't need to focus here
                };
                loader.hide();
            },
            hideEffect:   Element.hide,
            showEffect:   Element.show,
            id:           "modalwindow_url",
            buttonClass:  "form-button button"
        });
    }

    function validate_rename_form()
    {
        loader.show();
        var parametrs = $('invite-link-rename-form').serialize();
        var url       = $('invite-link-rename-form').readAttribute('action');
        new Ajax.Request(url, {
            method:     'post',
            parameters: parametrs,
            onSuccess:  function(result) {
                var result = eval('(' + result.responseText + ')');
                if (result.error == '1') {
                    loader.hide();
                    if (typeof result.message != 'undefined') {
                        alert(result.message);
                    }
                } else {
                    window.location.href = $('current-url').value;
                }
            }
        });

        return false;
    }

    function strpos(haystack, needle, offset)
    {
        var i = haystack.indexOf(needle, offset);

        return i >= 0 ? i : false;
    }

    function removeSelectedFriendsEmails(url)
    {
        if ($$('.friendslist-block input:checked').length) {
            loader.show();
            var parametrs = '';
            $$('.friendslist-block input:checked').select(function(index){parametrs += "&email[]=" + index.value;});
            new Ajax.Request(url, {
                method:     'post',
                parameters: parametrs,
                onSuccess:  function(result){
                    window.location.href = $('current-url').value;
                    loader.hide();
                }
            });
        } else {
            alert('select your friends');
        }
    }

    function showErrorMessage(msg)
    {
        $$('.messages li span')[0].update(msg);
        $$('.messages li')[0].addClassName('error-msg');
        $$('.messages li')[0].removeClassName('success-msg');
        $('invite-messages').show();
    }

    function addFriendsEmails()
    {
        if (strpos($('invite-textarea-to').value,'@')==false || strpos($('invite-textarea-to').value,'.')==false) {
            showErrorMessage('Please enter valid email address');

            return false;
        } else {
            $('invite-messages').hide();
            $('form-friends-add').submit();

            return true;
        }
    }

    function inviteSelectedFriends(url)
    {
        var error = 0;
        if ($$('.friendslist-block input:checked').length) {
            loader.show();

            var parametrs = "invite-textarea-message=" + $('invite-textarea-message').value;
            $$('.friendslist-block input:checked').select(function(index){parametrs += "&email[]=" + index.value;});
            new Ajax.Request(url, {
                method:     'post',
                parameters: parametrs,
                onSuccess:  function(result) {
                    loader.hide();
                    if ($$('.fb-friendslist-block input:checked').length==0) {
                        window.location.href = $('current-url').value;
                    }
                }
            });
        } else {
            error += 1;
        }

        if ($$('.fb-friendslist-block input:checked').length) {
            // template\referralreward\invite-content-block\fb-block.phtml
            // Facebook javascripts
            facebook_send_dialog();
        } else {
            error += 2;
        }

        if (error==3) {
            showErrorMessage('select your friends');
        }
    }
