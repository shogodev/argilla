/**
 * Tabs - jQuery plugin for accessible, unobtrusive tabs
 * @requires jQuery v1.1.1
 *
 * http://stilbuero.de/tabs/
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Version: 2.7.4
 */

;(function($) { // block scope

$.extend({
    tabs: {
        remoteCount: 0 // TODO in Tabs 3 this is going to be more cleanly in one single namespace
    }
});

/**
 * Create an accessible, unobtrusive tab interface based on a particular HTML structure.
 *
 * The underlying HTML has to look like this:
 *
 * <div id="container">
 *     <ul>
 *         <li><a href="#fragment-1">Section 1</a></li>
 *         <li><a href="#fragment-2">Section 2</a></li>
 *         <li><a href="#fragment-3">Section 3</a></li>
 *     </ul>
 *     <div id="fragment-1">
 *
 *     </div>
 *     <div id="fragment-2">
 *
 *     </div>
 *     <div id="fragment-3">
 *
 *     </div>
 * </div>
 *
 * Each anchor in the unordered list points directly to a section below represented by one of the
 * divs (the URI in the anchor's href attribute refers to the fragment with the corresponding id).
 * Because such HTML structure is fully functional on its own, e.g. without JavaScript, the tab
 * interface is accessible and unobtrusive.
 *
 * A tab is also bookmarkable via hash in the URL. Use the History/Remote plugin (Tabs will
 * auto-detect its presence) to fix the back (and forward) button.
 *
 * @example $('#container').tabs();
 * @desc Create a basic tab interface.
 * @example $('#container').tabs(2);
 * @desc Create a basic tab interface with the second tab initially activated.
 * @example $('#container').tabs({disabled: [3, 4]});
 * @desc Create a tab interface with the third and fourth tab being disabled.
 * @example $('#container').tabs({fxSlide: true});
 * @desc Create a tab interface that uses slide down/up animations for showing/hiding tab
 *       content upon tab switching.
 *
 * @param Number initial An integer specifying the position of the tab (no zero-based index) that
 *                       gets activated at first (on page load). Two alternative ways to specify
 *                       the active tab will overrule this argument. First a li element
 *                       (representing one single tab) belonging to the selected tab class, e.g.
 *                       set the selected tab class (default: "tabs-selected", see option
 *                       selectedClass) for one of the unordered li elements in the HTML source.
 *                       In addition if a fragment identifier/hash in the URL of the page refers
 *                       to the id of a tab container of a tab interface the corresponding tab will
 *                       be activated and both the initial argument as well as an eventually
 *                       declared class attribute will be overruled. Defaults to 1 if omitted.
 * @param Object settings An object literal containing key/value pairs to provide optional settings.
 * @option Array<Number> disabled An array containing the position of the tabs (no zero-based index)
 *                                that should be disabled on initialization. Default value: null.
 *                                A tab can also be disabled by simply adding the disabling class
 *                                (default: "tabs-disabled", see option disabledClass) to the li
 *                                element representing that particular tab.
 * @option Boolean bookmarkable Boolean flag indicating if support for bookmarking and history (via
 *                              changing hash in the URL of the browser) is enabled. Default value:
 *                              false, unless the History/Remote plugin is included. In that case the
 *                              default value becomes true. @see $.ajaxHistory.initialize
 * @option Boolean remote Boolean flag indicating that tab content has to be loaded remotely from
 *                        the url given in the href attribute of the tab menu anchor elements.
 * @option String spinner The content of this string is shown in a tab while remote content is loading.
 *                        Insert plain text as well as an img here. To turn off this notification
 *                        pass an empty string or null. Default: "Loading&#8230;".
 * @option String hashPrefix A String that is used for constructing the hash the link's href attribute
 *                           of a remote tab gets altered to, such as "#remote-1".
 *                           Default value: "remote-tab-".
 * @option Boolean fxFade Boolean flag indicating whether fade in/out animations are used for tab
 *                        switching. Can be combined with fxSlide. Will overrule fxShow/fxHide.
 *                        Default value: false.
 * @option Boolean fxSlide Boolean flag indicating whether slide down/up animations are used for tab
 *                         switching. Can be combined with fxFade. Will overrule fxShow/fxHide.
 *                         Default value: false.
 * @option String|Number fxSpeed A string representing one of the three predefined speeds ("slow",
 *                               "normal", or "fast") or the number of milliseconds (e.g. 1000) to
 *                               run an animation. Default value: "normal".
 * @option Object fxShow An object literal of the form jQuery's animate function expects for making
 *                       your own, custom animation to reveal a tab upon tab switch. Unlike fxFade
 *                       or fxSlide this animation is independent from an optional hide animation.
 *                       Default value: null. @see animate
 * @option Object fxHide An object literal of the form jQuery's animate function expects for making
 *                       your own, custom animation to hide a tab upon tab switch. Unlike fxFade
 *                       or fxSlide this animation is independent from an optional show animation.
 *                       Default value: null. @see animate
 * @option String|Number fxShowSpeed A string representing one of the three predefined speeds
 *                                   ("slow", "normal", or "fast") or the number of milliseconds
 *                                   (e.g. 1000) to run the animation specified in fxShow.
 *                                   Default value: fxSpeed.
 * @option String|Number fxHideSpeed A string representing one of the three predefined speeds
 *                                   ("slow", "normal", or "fast") or the number of milliseconds
 *                                   (e.g. 1000) to run the animation specified in fxHide.
 *                                   Default value: fxSpeed.
 * @option Boolean fxAutoHeight Boolean flag that if set to true causes all tab heights
 *                              to be constant (being the height of the tallest tab).
 *                              Default value: false.
 * @option Function onClick A function to be invoked upon tab switch, immediatly after a tab has
 *                          been clicked, e.g. before the other's tab content gets hidden. The
 *                          function gets passed three arguments: the first one is the clicked
 *                          tab (e.g. an anchor element), the second one is the DOM element
 *                          containing the content of the clicked tab (e.g. the div), the third
 *                          argument is the one of the tab that gets hidden. If this callback
 *                          returns false, the tab switch is canceled (use to disallow tab
 *                          switching for the reason of a failed form validation for example).
 *                          Default value: null.
 * @option Function onHide A function to be invoked upon tab switch, immediatly after one tab's
 *                         content got hidden (with or without an animation) and right before the
 *                         next tab is revealed. The function gets passed three arguments: the
 *                         first one is the clicked tab (e.g. an anchor element), the second one
 *                         is the DOM element containing the content of the clicked tab, (e.g. the
 *                         div), the third argument is the one of the tab that gets hidden.
 *                         Default value: null.
 * @option Function onShow A function to be invoked upon tab switch. This function is invoked
 *                         after the new tab has been revealed, e.g. after the switch is completed.
 *                         The function gets passed three arguments: the first one is the clicked
 *                         tab (e.g. an anchor element), the second one is the DOM element
 *                         containing the content of the clicked tab, (e.g. the div), the third
 *                         argument is the one of the tab that gets hidden. Default value: null.
 * @option String navClass A CSS class that is used to identify the tabs unordered list by class if
 *                         the required HTML structure differs from the default one.
 *                         Default value: "tabs-nav".
 * @option String selectedClass The CSS class attached to the li element representing the
 *                              currently selected (active) tab. Default value: "tabs-selected".
 * @option String disabledClass The CSS class attached to the li element representing a disabled
 *                              tab. Default value: "tabs-disabled".
 * @option String containerClass A CSS class that is used to identify tab containers by class if
 *                               the required HTML structure differs from the default one.
 *                               Default value: "tabs-container".
 * @option String hideClass The CSS class used for hiding inactive tabs. A class is used instead
 *                          of "display: none" in the style attribute to maintain control over
 *                          visibility in other media types than screen, most notably print.
 *                          Default value: "tabs-hide".
 * @option String loadingClass The CSS class used for indicating that an Ajax tab is currently
 *                             loading, for example by showing a spinner.
 *                             Default value: "tabs-loading".
 * @option String tabStruct @deprecated A CSS selector or basic XPath expression reflecting a
 *                          nested HTML structure that is different from the default single div
 *                          structure (one div with an id inside the overall container holds one
 *                          tab's content). If for instance an additional div is required to wrap
 *                          up the several tab containers such a structure is expressed by "div>div".
 *                          Default value: "div".
 * @type jQuery
 *
 * @name tabs
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.fn.tabs = function(initial, settings) {

    // settings
    if (typeof initial == 'object') settings = initial; // no initial tab given but a settings object
    settings = $.extend({
        initial: (initial && typeof initial == 'number' && initial > 0) ? --initial : 0,
        disabled: null,
        bookmarkable: $.ajaxHistory ? true : false,
        remote: false,
        spinner: 'Loading&#8230;',
        hashPrefix: 'remote-tab-',
        fxFade: null,
        fxSlide: null,
        fxShow: null,
        fxHide: null,
        fxSpeed: 'normal',
        fxShowSpeed: null,
        fxHideSpeed: null,
        fxAutoHeight: false,
        onClick: null,
        onHide: null,
        onShow: null,
        navClass: 'tabs-nav',
        selectedClass: 'tabs-selected',
        disabledClass: 'tabs-disabled',
        containerClass: 'tabs-container',
        hideClass: 'tabs-hide',
        loadingClass: 'tabs-loading',
        tabStruct: 'div'
    }, settings || {});

    $.browser.msie6 = $.browser.msie && ($.browser.version && $.browser.version < 7 || /MSIE 6.0/.test(navigator.userAgent)); // do not check for 6.0 alone, userAgent in Windows Vista has "Windows NT 6.0"

    // helper to prevent scroll to fragment
    function unFocus() {
        scrollTo(0, 0);
    }

    // initialize tabs
    return this.each(function() {

        // remember wrapper for later
        var container = this;

        // callbacks
        var onClick = settings.onClick, onHide = settings.onHide, onShow = settings.onShow;

        // setup nav
        var nav = $('ul.' + settings.navClass, container);
        nav = nav.size() && nav || $('>ul:eq(0)', container); // fallback to default structure
        var tabs = $('a', nav);

        // prepare remote tabs
        if (settings.remote) {
            tabs.each(function() {
                var id = settings.hashPrefix + (++$.tabs.remoteCount), hash = '#' + id, url = this.href;
                this.href = hash;
                $('<div id="' + id + '" class="' + settings.containerClass + '"></div>').appendTo(container);

                $(this).bind('loadRemoteTab', function(e, callback) {
                    var $$ = $(this).addClass(settings.loadingClass), span = $('span', this)[0], tabTitle = span.innerHTML;
                    if (settings.spinner) {
                        // TODO if spinner is image
                        span.innerHTML = '<em>' + settings.spinner + '</em>'; // WARNING: html(...) crashes Safari with jQuery 1.1.2
                    }
                    setTimeout(function() { // Timeout is again required in IE, "wait" for id being restored
                        $(hash).load(url, function() {
                            if (settings.spinner) {
                                span.innerHTML = tabTitle; // WARNING: html(...) crashes Safari with jQuery 1.1.2
                            }
                            $$.removeClass(settings.loadingClass);
                            callback && callback();
                        });
                    }, 0);
                });

            });
        }

        // set up containers
        var containers = $('div.' + settings.containerClass, container);
        containers = containers.size() && containers || $('>' + settings.tabStruct, container); // fallback to default structure

        // attach classes for styling if not present
        nav.is('.' + settings.navClass) || nav.addClass(settings.navClass);
        containers.each(function() {
            var $$ = $(this);
            $$.is('.' + settings.containerClass) || $$.addClass(settings.containerClass);
        });

        // try to retrieve active tab from class in HTML
        var hasSelectedClass = $('li', nav).index( $('li.' + settings.selectedClass, nav) );
        if (hasSelectedClass >= 0) {
           settings.initial = hasSelectedClass;
        }

        // try to retrieve active tab from hash in url, will override class in HTML
        if (location.hash) {
            tabs.each(function(i) {
                if (this.hash == location.hash) {
                    settings.initial = i;
                    // prevent page scroll to fragment
                    if (($.browser.msie || $.browser.opera) && !settings.remote) {
                        var toShow = $(location.hash);
                        var toShowId = toShow.attr('id');
                        toShow.attr('id', '');
                        setTimeout(function() {
                            toShow.attr('id', toShowId); // restore id
                        }, 500);
                    }
                    unFocus();
                    return false; // break
                }
            });
        }
        if ($.browser.msie) {
            unFocus(); // fix IE focussing bottom of the page for some unknown reason
        }

        // highlight tab accordingly
        var startCallback = function() {
            if (typeof onShow == 'function') {
                onShow(tabs.eq(settings.initial)[0], containers.filter(':eq(' + settings.initial + ')')[0], null);
            }
        };
        containers.filter(':eq(' + settings.initial + ')').show().end().not(':eq(' + settings.initial + ')').addClass(settings.hideClass);
        $('li', nav).removeClass(settings.selectedClass).eq(settings.initial).addClass(settings.selectedClass); // we need to remove classes eventually if hash takes precedence over class
        if (!settings.remote) startCallback();

        // trigger load of initial tab
        tabs.eq(settings.initial).trigger('loadRemoteTab', [startCallback]).end();

        // setup auto height
        if (settings.fxAutoHeight) {
            // helper
            var _setAutoHeight = function(reset) {
                // get tab heights in top to bottom ordered array
                var heights = $.map(containers.get(), function(el) {
                    var h, jq = $(el);
                    if (reset) {
                        if ($.browser.msie6) {
                            el.style.removeExpression('behaviour');
                            el.style.height = '';
                            el.minHeight = null;
                        }
                        h = jq.css({'min-height': ''}).height(); // use jQuery's height() to get hidden element values
                    } else {
                        h = jq.height(); // use jQuery's height() to get hidden element values
                    }
                    return h;
                }).sort(function(a, b) {
                    return b - a;
                });
                if ($.browser.msie6) {
                    containers.each(function() {
                        this.minHeight = heights[0] + 'px';
                        this.style.setExpression('behaviour', 'this.style.height = this.minHeight ? this.minHeight : "1px"'); // using an expression to not make print styles useless
                    });
                } else {
                    containers.css({'min-height': heights[0] + 'px'});
                }
            };
            // call once for initialization
            _setAutoHeight();
            // trigger auto height adjustment if needed
            var cachedWidth = container.offsetWidth;
            var cachedHeight = container.offsetHeight;
            var watchFontSize = $('#tabs-watch-font-size').get(0) || $('<span id="tabs-watch-font-size">M</span>').css({display: 'block', position: 'absolute', visibility: 'hidden'}).appendTo(document.body).get(0);
            var cachedFontSize = watchFontSize.offsetHeight;
            setInterval(function() {
                var currentWidth = container.offsetWidth;
                var currentHeight = container.offsetHeight;
                var currentFontSize = watchFontSize.offsetHeight;
                if (currentHeight > cachedHeight || currentWidth != cachedWidth || currentFontSize != cachedFontSize) {
                    _setAutoHeight((currentWidth > cachedWidth || currentFontSize < cachedFontSize)); // if heights gets smaller reset min-height
                    cachedWidth = currentWidth;
                    cachedHeight = currentHeight;
                    cachedFontSize = currentFontSize;
                }
            }, 50);
        }

        // setup animations
        var showAnim = {}, hideAnim = {}, showSpeed = settings.fxShowSpeed || settings.fxSpeed, hideSpeed = settings.fxHideSpeed || settings.fxSpeed;
        if (settings.fxSlide || settings.fxFade) {
            if (settings.fxSlide) {
                showAnim['height'] = 'show';
                hideAnim['height'] = 'hide';
            }
            if (settings.fxFade) {
                showAnim['opacity'] = 'show';
                hideAnim['opacity'] = 'hide';
            }
        } else {
            if (settings.fxShow) {
                showAnim = settings.fxShow;
            } else { // use some kind of animation to prevent browser scrolling to the tab
                showAnim['min-width'] = 0; // avoid opacity, causes flicker in Firefox
                showSpeed = 1; // as little as 1 is sufficient
            }
            if (settings.fxHide) {
                hideAnim = settings.fxHide;
            } else { // use some kind of animation to prevent browser scrolling to the tab
                hideAnim['min-width'] = 0; // avoid opacity, causes flicker in Firefox
                hideSpeed = 1; // as little as 1 is sufficient
            }
        }

        // attach activateTab event, required for activating a tab programmatically
        tabs.bind('triggerTab', function() {

            // if the tab is already selected or disabled or animation is still running stop here
            var li = $(this).parents('li:eq(0)');
            if (container.locked || li.is('.' + settings.selectedClass) || li.is('.' + settings.disabledClass)) {
                return false;
            }

            var hash = this.hash;

            if ($.browser.msie) {

                $(this).trigger('click');
                if (settings.bookmarkable) {
                    $.ajaxHistory.update(hash);
                    location.hash = hash.replace('#', '');
                }

            } else if ($.browser.safari) {

                // Simply setting location.hash puts Safari into the eternal load state... ugh! Submit a form instead.
                // var tempForm = $('<form action="' + hash + '"><div><input type="submit" value="h" /></div></form>').get(0); // no need to append it to the body
                // tempForm.submit(); // does not trigger the form's submit event...
                $(this).trigger('click'); // ...thus do stuff here
                if (settings.bookmarkable) {
                    $.ajaxHistory.update(hash);
                }

            } else {

                if (settings.bookmarkable) {
                    location.hash = hash.replace('#', '');
                } else {
                    $(this).trigger('click');
                }

            }

        });

        // attach disable event, required for disabling a tab
        tabs.bind('disableTab', function() {
            var li = $(this).parents('li:eq(0)');
            if ($.browser.safari) { /* fix opacity of tab after disabling in Safari... */
                li.animate({ opacity: 0 }, 1, function() {
                   li.css({opacity: ''});
                });
            }
            li.addClass(settings.disabledClass);

        });

        // disabled from settings
        if (settings.disabled && settings.disabled.length) {
            for (var i = 0, k = settings.disabled.length; i < k; i++) {
                tabs.eq(--settings.disabled[i]).trigger('disableTab').end();
            }
        };

        // attach enable event, required for reenabling a tab
        tabs.bind('enableTab', function() {
            var li = $(this).parents('li:eq(0)');
            li.removeClass(settings.disabledClass);
            if ($.browser.safari) { /* fix disappearing tab after enabling in Safari... */
                li.animate({ opacity: 1 }, 1, function() {
                    li.css({opacity: ''});
                });
            }
        });

        // attach click event
        tabs.bind('click', function(e) {

            var trueClick = e.clientX; // add to history only if true click occured, not a triggered click
            var clicked = this, li = $(this).parents('li:eq(0)'), toShow = $(this.hash), toHide = containers.filter(':visible');

            // if animation is still running, tab is selected or disabled or onClick callback returns false stop here
            // check if onClick returns false last so that it is not executed for a disabled tab
            if (container['locked'] || li.is('.' + settings.selectedClass) || li.is('.' + settings.disabledClass) || typeof onClick == 'function' && onClick(this, toShow[0], toHide[0]) === false) {
                this.blur();
                return false;
            }

            container['locked'] = true;

            // show new tab
            if (toShow.size()) {

                // prevent scrollbar scrolling to 0 and than back in IE7, happens only if bookmarking/history is enabled
                if ($.browser.msie && settings.bookmarkable) {
                    var toShowId = this.hash.replace('#', '');
                    toShow.attr('id', '');
                    setTimeout(function() {
                        toShow.attr('id', toShowId); // restore id
                    }, 0);
                }

                var resetCSS = { display: '', overflow: '', height: '' };
                if (!$.browser.msie) { // not in IE to prevent ClearType font issue
                    resetCSS['opacity'] = '';
                }

                // switch tab, animation prevents browser scrolling to the fragment
                function switchTab() {
                    if (settings.bookmarkable && trueClick) { // add to history only if true click occured, not a triggered click
                        $.ajaxHistory.update(clicked.hash);
                    }
                    toHide.animate(hideAnim, hideSpeed, function() { //
                        $(clicked).parents('li:eq(0)').addClass(settings.selectedClass).siblings().removeClass(settings.selectedClass);
                        toHide.addClass(settings.hideClass).css(resetCSS); // maintain flexible height and accessibility in print etc.
                        if (typeof onHide == 'function') {
                            onHide(clicked, toShow[0], toHide[0]);
                        }
                        if (!(settings.fxSlide || settings.fxFade || settings.fxShow)) {
                            toShow.css('display', 'block'); // prevent occasionally occuring flicker in Firefox cause by gap between showing and hiding the tab containers
                        }
                        toShow.animate(showAnim, showSpeed, function() {
                            toShow.removeClass(settings.hideClass).css(resetCSS); // maintain flexible height and accessibility in print etc.
                            if ($.browser.msie) {
                                toHide[0].style.filter = '';
                                toShow[0].style.filter = '';
                            }
                            if (typeof onShow == 'function') {
                                onShow(clicked, toShow[0], toHide[0]);
                            }
                            container['locked'] = null;
                        });
                    });
                }

                if (!settings.remote) {
                    switchTab();
                } else {
                    $(clicked).trigger('loadRemoteTab', [switchTab]);
                }

            } else {
                if(window.console && console.firebug) console.error('There is no such container.');
            }

            // Set scrollbar to saved position - need to use timeout with 0 to prevent browser scroll to target of hash
            var scrollX = window.pageXOffset || document.documentElement && document.documentElement.scrollLeft || document.body.scrollLeft || 0;
            var scrollY = window.pageYOffset || document.documentElement && document.documentElement.scrollTop || document.body.scrollTop || 0;
            setTimeout(function() {
                window.scrollTo(scrollX, scrollY);
            }, 0);

            this.blur(); // prevent IE from keeping other link focussed when using the back button

            return settings.bookmarkable && !!trueClick; // convert undefined to Boolean for IE

        });

        // enable history support if bookmarking and history is turned on
        if (settings.bookmarkable) {
            $.ajaxHistory.initialize(function() {
                tabs.eq(settings.initial).trigger('click').end();
            });
        }

    });

};

/**
 * Activate a tab programmatically with the given position (no zero-based index)
 * or its id, e.g. the URL's fragment identifier/hash representing a tab, as if the tab
 * itself were clicked.
 *
 * @example $('#container').triggerTab(2);
 * @desc Activate the second tab of the tab interface contained in <div id="container">.
 * @example $('#container').triggerTab(1);
 * @desc Activate the first tab of the tab interface contained in <div id="container">.
 * @example $('#container').triggerTab();
 * @desc Activate the first tab of the tab interface contained in <div id="container">.
 * @example $('#container').triggerTab('fragment-2');
 * @desc Activate a tab via its URL fragment identifier representation.
 *
 * @param String|Number tab Either a string that matches the id of the tab (the URL's
 *                          fragment identifier/hash representing a tab) or an integer
 *                          specifying the position of the tab (no zero-based index) to
 *                          be activated. If this parameter is omitted, the first tab
 *                          will be activated.
 * @type jQuery
 *
 * @name triggerTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Disable a tab, so that clicking it has no effect.
 *
 * @example $('#container').disableTab(2);
 * @desc Disable the second tab of the tab interface contained in <div id="container">.
 *
 * @param String|Number tab Either a string that matches the id of the tab (the URL's
 *                          fragment identifier/hash representing a tab) or an integer
 *                          specifying the position of the tab (no zero-based index) to
 *                          be disabled. If this parameter is omitted, the first tab
 *                          will be disabled.
 * @type jQuery
 *
 * @name disableTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Enable a tab that has been disabled.
 *
 * @example $('#container').enableTab(2);
 * @desc Enable the second tab of the tab interface contained in <div id="container">.
 *
 * @param String|Number tab Either a string that matches the id of the tab (the URL's
 *                          fragment identifier/hash representing a tab) or an integer
 *                          specifying the position of the tab (no zero-based index) to
 *                          be enabled. If this parameter is omitted, the first tab
 *                          will be enabled.
 * @type jQuery
 *
 * @name enableTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

var tabEvents = ['triggerTab', 'disableTab', 'enableTab'];
for (var i = 0; i < tabEvents.length; i++) {
    $.fn[tabEvents[i]] = (function(tabEvent) {
        return function(tab) {
            return this.each(function() {
                var nav = $('ul.tabs-nav' , this);
                nav = nav.size() && nav || $('>ul:eq(0)', this); // fallback to default structure
                var a;
                if (!tab || typeof tab == 'number') {
                    a = $('li a', nav).eq((tab && tab > 0 && tab - 1 || 0)); // fall back to 0
                } else if (typeof tab == 'string') {
                    a = $('li a[@href$="#' + tab + '"]', nav);
                }
                a.trigger(tabEvent);
            });
        };
    })(tabEvents[i]);
}

/**
 * Get the position of the currently selected tab (no zero-based index).
 *
 * @example $('#container').activeTab();
 * @desc Get the position of the currently selected tab of an interface
 * contained in <div id="container">.
 *
 * @type Number
 *
 * @name activeTab
 * @cat Plugins/Tabs
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

$.fn.activeTab = function() {
    var selectedTabs = [];
    this.each(function() {
        var nav = $('ul.tabs-nav' , this);
        nav = nav.size() && nav || $('>ul:eq(0)', this); //fallback to default structure
        var lis = $('li', nav);
        selectedTabs.push(lis.index( lis.filter('.tabs-selected')[0] ) + 1);
    });
    return selectedTabs[0];
};

})(jQuery);



/**
 * History/Remote - jQuery plugin for enabling history support and bookmarking
 * @requires jQuery v1.0.3
 *
 * http://stilbuero.de/jquery/history/
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Version: 0.2.3
 */

(function($) { // block scope

/**
 * Initialize the history manager. Subsequent calls will not result in additional history state change 
 * listeners. Should be called soonest when the DOM is ready, because in IE an iframe needs to be added
 * to the body to enable history support.
 *
 * @example $.ajaxHistory.initialize();
 *
 * @param Function callback A single function that will be executed in case there is no fragment
 *                          identifier in the URL, for example after navigating back to the initial
 *                          state. Use to restore such an initial application state.
 *                          Optional. If specified it will overwrite the default action of 
 *                          emptying all containers that are used to load content into.
 * @type undefined
 *
 * @name $.ajaxHistory.initialize()
 * @cat Plugins/History
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.ajaxHistory = new function() {

    var RESET_EVENT = 'historyReset';

    var _currentHash = location.hash;
    var _intervalId = null;
    var _observeHistory; // define outside if/else required by Opera

    this.update = function() { }; // empty function body for graceful degradation

    // create custom event for state reset
    var _defaultReset = function() {
        $('.remote-output').empty();
    };
    $(document).bind(RESET_EVENT, _defaultReset);
    
    // TODO fix for Safari 3
    // if ($.browser.msie)
    // else if hash != _currentHash
    // else check history length

    if ($.browser.msie) {

        var _historyIframe, initialized = false; // for IE

        // add hidden iframe
        $(function() {
            _historyIframe = $('<iframe style="display: none;"></iframe>').appendTo(document.body).get(0);
            var iframe = _historyIframe.contentWindow.document;
            // create initial history entry
            iframe.open();
            iframe.close();
            if (_currentHash && _currentHash != '#') {
                iframe.location.hash = _currentHash.replace('#', '');
            }
        });

        this.update = function(hash) {
            _currentHash = hash;
            var iframe = _historyIframe.contentWindow.document;
            iframe.open();
            iframe.close();
            iframe.location.hash = hash.replace('#', '');
        };

        _observeHistory = function() {
            var iframe = _historyIframe.contentWindow.document;
            var iframeHash = iframe.location.hash;
            if (iframeHash != _currentHash) {
                _currentHash = iframeHash;
                if (iframeHash && iframeHash != '#') {
                    // order does matter, set location.hash after triggering the click...
                    $('a[@href$="' + iframeHash + '"]').click();
                    location.hash = iframeHash;
                } else if (initialized) {
                    location.hash = '';
                    $(document).trigger(RESET_EVENT);
                }
            }
            initialized = true;
        };

    } else if ($.browser.mozilla || $.browser.opera) {

        this.update = function(hash) {
            _currentHash = hash;
        };

        _observeHistory = function() {
            if (location.hash) {
                if (_currentHash != location.hash) {
                    _currentHash = location.hash;
                    $('a[@href$="' + _currentHash + '"]').click();
                }
            } else if (_currentHash) {
                _currentHash = '';
                $(document).trigger(RESET_EVENT);
            }
        };

    } else if ($.browser.safari) {

        var _backStack, _forwardStack, _addHistory; // for Safari

        // etablish back/forward stacks
        $(function() {
            _backStack = [];
            _backStack.length = history.length;
            _forwardStack = [];

        });
        var isFirst = false, initialized = false;
        _addHistory = function(hash) {
            _backStack.push(hash);
            _forwardStack.length = 0; // clear forwardStack (true click occured)
            isFirst = false;
        };

        this.update = function(hash) {
            _currentHash = hash;
            _addHistory(_currentHash);
        };

        _observeHistory = function() {
            var historyDelta = history.length - _backStack.length;
            if (historyDelta) { // back or forward button has been pushed
                isFirst = false;
                if (historyDelta < 0) { // back button has been pushed
                    // move items to forward stack
                    for (var i = 0; i < Math.abs(historyDelta); i++) _forwardStack.unshift(_backStack.pop());
                } else { // forward button has been pushed
                    // move items to back stack
                    for (var i = 0; i < historyDelta; i++) _backStack.push(_forwardStack.shift());
                }
                var cachedHash = _backStack[_backStack.length - 1];
                $('a[@href$="' + cachedHash + '"]').click();
                _currentHash = location.hash;
            } else if (_backStack[_backStack.length - 1] == undefined && !isFirst) {
                // back button has been pushed to beginning and URL already pointed to hash (e.g. a bookmark)
                // document.URL doesn't change in Safari
                if (document.URL.indexOf('#') >= 0) {
                    $('a[@href$="' + '#' + document.URL.split('#')[1] + '"]').click();
                } else if (initialized) {
                    $(document).trigger(RESET_EVENT);
                }
                isFirst = true;
            }
            initialized = true;
        };

    }

    this.initialize = function(callback) {
        // custom callback to reset app state (no hash in url)
        if (typeof callback == 'function') {
            $(document).unbind(RESET_EVENT, _defaultReset).bind(RESET_EVENT, callback);
        }
        // look for hash in current URL (not Safari)
        if (location.hash && typeof _addHistory == 'undefined') {
            $('a[@href$="' + location.hash + '"]').trigger('click');
        }
        // start observer
        if (_observeHistory && _intervalId == null) {
            _intervalId = setInterval(_observeHistory, 200); // Safari needs at least 200 ms
        }
    };

};

/**
 * Implement Ajax driven links in a completely unobtrusive and accessible manner (also known as "Hijax")
 * with support for the browser's back/forward navigation buttons and bookmarking.
 *
 * The link's href attribute gets altered to a fragment identifier, such as "#remote-1", so that the browser's
 * URL gets updated on each click, whereas the former value of that attribute is used to load content via
 * XmlHttpRequest from and update the specified element. If no target element is found, a new div element will be
 * created and appended to the body to load the content into. The link informs the history manager of the 
 * state change on click and adds an entry to the browser's history.
 *
 * jQuery's Ajax implementation adds a custom request header of the form "X-Requested-With: XmlHttpRequest"
 * to any Ajax request so that the called page can distinguish between a standard and an Ajax (XmlHttpRequest)
 * request.
 *
 * @example $('a.remote').remote('#output');
 * @before <a class="remote" href="/path/to/content.html">Update</a>
 * @result <a class="remote" href="#remote-1">Update</a>
 * @desc Alter a link of the class "remote" to an Ajax-enhanced link and let it load content from
 *       "/path/to/content.html" via XmlHttpRequest into an element with the id "output".
 * @example $('a.remote').remote('#output', {hashPrefix: 'chapter'});
 * @before <a class="remote" href="/path/to/content.html">Update</a>
 * @result <a class="remote" href="#chapter-1">Update</a>
 * @desc Alter a link of the class "remote" to an Ajax-enhanced link and let it load content from
 *       "/path/to/content.html" via XmlHttpRequest into an element with the id "output".
 *
 * @param String expr A string containing a CSS selector or basic XPath specifying the element to load
 *                    content into via XmlHttpRequest.
 * @param Object settings An object literal containing key/value pairs to provide optional settings.
 * @option String hashPrefix A String that is used for constructing the hash the link's href attribute
 *                           gets altered to, such as "#remote-1". Default value: "remote-".
 * @param Function callback A single function that will be executed when the request is complete. 
 * @type jQuery
 *
 * @name remote
 * @cat Plugins/Remote
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Implement Ajax driven links in a completely unobtrusive and accessible manner (also known as "Hijax")
 * with support for the browser's back/forward navigation buttons and bookmarking.
 *
 * The link's href attribute gets altered to a fragment identifier, such as "#remote-1", so that the browser's
 * URL gets updated on each click, whereas the former value of that attribute is used to load content via
 * XmlHttpRequest from and update the specified element. If no target element is found, a new div element will be
 * created and appended to the body to load the content into. The link informs the history manager of the 
 * state change on click and adds an entry to the browser's history.
 *
 * jQuery's Ajax implementation adds a custom request header of the form "X-Requested-With: XmlHttpRequest"
 * to any Ajax request so that the called page can distinguish between a standard and an Ajax (XmlHttpRequest)
 * request.
 *
 * @example $('a.remote').remote( $('#output > div')[0] );
 * @before <a class="remote" href="/path/to/content.html">Update</a>
 * @result <a class="remote" href="#remote-1">Update</a>
 * @desc Alter a link of the class "remote" to an Ajax-enhanced link and let it load content from
 *       "/path/to/content.html" via XmlHttpRequest into an element with the id "output".
 * @example $('a.remote').remote('#output', {hashPrefix: 'chapter'});
 * @before <a class="remote" href="/path/to/content.html">Update</a>
 * @result <a class="remote" href="#chapter-1">Update</a>
 * @desc Alter a link of the class "remote" to an Ajax-enhanced link and let it load content from
 *       "/path/to/content.html" via XmlHttpRequest into an element with the id "output".
 *
 * @param Element elem A DOM element to load content into via XmlHttpRequest.
 * @param Object settings An object literal containing key/value pairs to provide optional settings.
 * @option String hashPrefix A String that is used for constructing the hash the link's href attribute
 *                           gets altered to, such as "#remote-1". Default value: "remote-".
 * @param Function callback A single function that will be executed when the request is complete. 
 * @type jQuery
 *
 * @name remote
 * @cat Plugins/Remote
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.fn.remote = function(output, settings, callback) {

    callback = callback || function() {};
    if (typeof settings == 'function') { // shift arguments
        callback = settings;
    }
    
    settings = $.extend({
        hashPrefix: 'remote-'
    }, settings || {});

    var target = $(output).size() && $(output) || $('<div></div>').appendTo('body');
    target.addClass('remote-output');

    return this.each(function(i) {
        var href = this.href, hash = '#' + (this.title && this.title.replace(/\s/g, '_') || settings.hashPrefix + (i + 1)),
            a = this;
        this.href = hash;
        $(this).click(function(e) {
            // lock target to prevent double loading in Firefox
            if (!target['locked']) {
                // add to history only if true click occured, not a triggered click
                if (e.clientX) {
                    $.ajaxHistory.update(hash);
                }
                target.load(href, function() {
                    target['locked'] = null;
                    callback.apply(a);
                });
            }
        });
    });

};

/**
 * Provides the ability to use the back/forward navigation buttons in a DHTML application.
 * A change of the application state is reflected by a change of the URL fragment identifier.
 *
 * The link's href attribute needs to point to a fragment identifier within the same resource,
 * although that fragment id does not need to exist. On click the link changes the URL fragment
 * identifier, informs the history manager of the state change and adds an entry to the browser's
 * history.
 *
 * @param Function callback A single function that will be executed as the click handler of the 
 *                          matched element. It will be executed on click (adding an entry to 
 *                          the history) as well as in case the history manager needs to trigger 
 *                          it depending on the value of the URL fragment identifier, e.g. if its 
 *                          current value matches the href attribute of the matched element.
 *                           
 * @type jQuery
 *
 * @name history
 * @cat Plugins/History
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
$.fn.history = function(callback) {
    return this.click(function(e) {        
		// add to history only if true click occured,
		// not a triggered click...
        if (e.clientX) {
	        // ...and die if already active
			if (this.hash == location.hash) {
				return false;
			} 
           	$.ajaxHistory.update(this.hash);
        }
		if (typeof callback == 'function') {
			callback.call(this);
		}
    });
};

})(jQuery);