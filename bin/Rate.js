/**
 * Rating
 *
 * @author www.pcsg.de (Henning Leutz)
 * @module package/quiqqer/rating/bin/Rate
 *
 * @require qui/controls/Control
 */
define('package/quiqqer/rating/bin/Rate', [

    'qui/controls/Control'

], function (Control) {
    "use strict";

    return new Class({

        Extends: Control,
        Type   : 'package/quiqqer/rating/bin/Rate',

        Binds: [
            '$onImport',
            '$build'
        ],

        options: {
            stars: 5,
            name : "",
            value: 0
        },

        initialize: function (options) {
            this.parent(options);

            this.$elms      = [];
            this.$Container = null;
            this.$Title     = null;

            this.$__building = true;

            this.addEvents({
                onImport: this.$onImport
            });
        },

        /**
         * create the domnode of the control
         *
         * @return {HTMLElement}
         */
        create: function () {

            this.$Elm = new Element('div', {
                'class': 'qui-rating',
                html   : '<div class="qui-rating-star-container"></div>' +
                         '<div class="qui-rating-star-title"></div>'
            });

            this.$Container = this.$Elm.getElement('.qui-rating-star-container');
            this.$Title     = this.$Elm.getElement('.qui-rating-star-title');

            this.$build();

            return this.$Elm;
        },

        /**
         * refresh the display
         */
        refresh: function () {
            var value = this.getAttribute('value');

            for (var i = 0, len = this.$elms.length; i < len; i++) {

                if (!this.$elms[i]) {
                    continue;
                }

                if (i < value) {
                    this.$elms[i].addClass('qui-rating-star-select');
                    continue;
                }

                this.$elms[i].removeClass('qui-rating-star-select');
            }
        },

        /**
         * event : on import
         */
        $onImport: function () {

            var Elm = this.getElm();

            this.$Container = Elm.getElement('.qui-rating-star-container');
            this.$Title     = Elm.getElement('.qui-rating-star-title');

            var Value = Elm.getElement('[itemprop="ratingValue"]');

            this.setAttribute('value', Value.get('content'));
            this.$build();
        },

        /**
         * build events and stars
         */
        $build: function () {
            var i, len, Star;
            var self = this;

            var starClick = function () {
                self.$click(this);
            };

            for (i = 0, len = this.getAttribute('stars'); i < len; i++) {

                Star = new Element('span', {
                    'class'    : 'icon-star qui-rating-star fa fa-star',
                    'data-star': i,
                    events     : {
                        click: starClick
                    }
                }).inject(this.$Container);

                this.$elms.push(Star);
            }

            this.refresh();

            this.$__building = false;
        },

        /**
         * event : click on a star
         */
        $click: function (Elm) {

            var no = Elm.get('data-star').toInt() + 1;

            for (var i = 0, len = this.$elms.length; i < len; i++) {

                if (!this.$elms[i]) {
                    continue;
                }

                if (i < no) {
                    this.$elms[i].addClass('qui-rating-star-select');
                    continue;
                }

                this.$elms[i].removeClass('qui-rating-star-select');
            }

            this.setAttribute('value', no);
            this.update();
        },

        /**
         * Return the value
         *
         * @return {Number}
         */
        getValue: function () {
            return this.getAttribute('value');
        },

        /**
         * Update the rating
         */
        update: function () {

            if (this.$__building) {
                return;
            }

            var self = this;

            self.$Title.set(
                'html',
                '<span class="icon-spinner icon-spin fa fa-spinner fa-spin"></span>'
            );

            require(['Ajax', 'Locale'], function (Ajax, QUILocale) {

                Ajax.post('package_quiqqer_rating_ajax_addRating', function (data) {

                    self.$Title.set(
                        'html',
                        QUILocale.get('quiqqer/rating', 'control.rate.text', {
                            from: data.average,
                            max : self.getAttribute('stars')
                        })
                    );

                    self.setAttribute('value', data.average);
                    self.refresh();

                }, {
                    'package': 'quiqqer/rating',
                    project  : JSON.encode(QUIQQER_PROJECT),
                    siteId   : QUIQQER_SITE.id,
                    rating   : self.getAttribute('value')
                });

            });
        }
    });
});
