(function(d, h, l) {
    var HISTORY_SUPPORT = !!(h && h.pushState);

    var anchorScrolls = {
        ANCHOR_REGEX: /^#[^ ]+$/,
        OFFSET_HEIGHT_PX: 80,

        /**
         * Establish events, and fix initial scroll position if a hash is provided.
         */
        init: function() {
            this.scrollToCurrent();
            $(window).on('hashchange', $.proxy(this, 'scrollToCurrent'));
            $('body').on('click', 'a', $.proxy(this, 'delegateAnchors'));
        },

        /**
         * Return the offset amount to deduct from the normal scroll position.
         * Modify as appropriate to allow for dynamic calculations
         */
        getFixedOffset: function() {
            return this.OFFSET_HEIGHT_PX;
        },

        /**
         * If the provided href is an anchor which resolves to an element on the
         * page, scroll to it.
         * @param  {String} href
         * @return {Boolean} - Was the href an anchor.
         */
        scrollIfAnchor: function(href, pushToHistory) {
            var match, anchorOffset;

            if(!this.ANCHOR_REGEX.test(href)) {
                return false;
            }

            match = d.getElementById(href.slice(1));

            if(match) {
                anchorOffset = $(match).offset().top - this.getFixedOffset();
                $('html, body').animate({ scrollTop: anchorOffset});

                // Add the state to history as-per normal anchor links
                if(HISTORY_SUPPORT && pushToHistory) {
                    h.pushState({}, d.title, l.pathname + href);
                }
            }

            return !!match;
        },

        /**
         * Attempt to scroll to the current location's hash.
         */
        scrollToCurrent: function(e) {
            if(this.scrollIfAnchor(window.location.hash) && e) {
                e.preventDefault();
            }
        },

        /**
         * If the click event's target was an anchor, fix the scroll position.
         */
        delegateAnchors: function(e) {
            var elem = e.target;

            if(this.scrollIfAnchor(elem.getAttribute('href'), true)) {
                e.preventDefault();
            }
        }
    };

    $(d).ready();
    $(document).ready(function() {
        $.proxy(anchorScrolls, 'init')();
        var $tableOfContents = $('#table-of-contents');
        function fixTableOfContents() {
            $tableOfContents.css({
                position: 'relative',
                width: '',
            });
            var width = $tableOfContents.outerWidth();
            $tableOfContents.css({
                position: 'fixed',
                width: width + 'px',
            });
        }
        fixTableOfContents();

        $(window).resize(fixTableOfContents);
    });
})(document, history, location);
