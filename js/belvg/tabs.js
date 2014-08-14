
    Varien.BTabs = Class.create();
    Varien.BTabs.prototype = {
        initialize: function(selector)
        {
            if ($$(selector+' a').size()) {
                $$(selector+' a').each(this.initTab.bind(this));
            } else {
                $$(selector+' li').each(this.initTab.bind(this));
            }
        },

        initTab: function(el)
        {
            if (el.tagName == 'A') {
                el.href = 'javascript:void(0)';
                var tab = el.parentNode;
            } else {
                var tab = el;
            }

            if ($(tab).hasClassName('active')) {
                this.showContent(tab);
            }

            el.observe('click', this.showContent.bind(this, el));
        },

        showContent: function(li)
        {
            if (li.tagName == 'A') {
                li = $(li.parentNode);
            }

            var ul = $(li.parentNode);
            ul.getElementsBySelector('li', 'ul').each(function(el) {
                var contents = $(el.id + '_content');
                if (el == li) {
                    el.addClassName('active');
                    contents.show();
                } else {
                    el.removeClassName('active');
                    contents.hide();
                }
            });
        },

    }

