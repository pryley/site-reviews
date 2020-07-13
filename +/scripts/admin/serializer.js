/** global: GLSR, jQuery */
/**
 * Based on jQuery serializeObject
 * @version 2.5.0
 * @link https://github.com/macek/jquery-serialize-object
 */
;(function ($) {
    'use strict';

    GLSR.Serializer = function (form) {
        this.data = {};
        this.form = $(form);
        this.pushes = {};
        return this.init();
    };

    GLSR.Serializer.prototype = {

        patterns: {
            validate: /^[a-z_-][a-z0-9_-]*(?:\[(?:\d*|[a-z0-9_-]+)\])*$/i,
            key: /[a-z0-9_-]+|(?=\[\])/gi,
            named: /^[a-z0-9_-]+$/i,
            push: /^$/,
            fixed: /^\d+$/,
        },

        /** @return void */
        addPair: function (pair) {
            if (!this.patterns.validate.test(pair.name)) return;
            this.data = $.extend(true, this.data, this.makeObject(pair.name, this.encode(pair)));
        },

        /** @return array|object */
        build: function (base, key, value) {
            base[key] = value;
            return base;
        },

        /** @return mixed */
        encode: function (pair) {
            switch($('[name="'+pair.name+'"]', this.form).attr('type')) {
                case 'checkbox':
                    return pair.value === 'on' ? true : pair.value;
                default:
                    return pair.value;
            }
        },

        /** @return int */
        incrementPush: function (key) {
            if (this.pushes[key] === undefined) {
                this.pushes[key] = 0;
            }
            return this.pushes[key]++;
        },

        /** @return object */
        init: function () {
            var pairs = this.form.serializeArray();
            if ($.isArray(pairs)) {
                for (var i = 0, len = pairs.length; i < len; i++) {
                    this.addPair(pairs[i]);
                }
            }
            return this.data;
        },

        /** @return array|object */
        makeObject: function (root, value) {
            var k;
            var keys = root.match(this.patterns.key);
            // nest, nest, ..., nest
            while ((k = keys.pop()) !== undefined) {
                // foo[]
                if (this.patterns.push.test(k)) {
                    var idx = this.incrementPush(root.replace(/\[\]$/, ''));
                    value = this.build([], idx, value);
                }
                // foo[n]
                else if (this.patterns.fixed.test(k)) {
                    value = this.build([], k, value);
                }
                // foo; foo[bar]
                else if (this.patterns.named.test(k)) {
                    value = this.build({}, k, value);
                }
            }
            return value;
        },
    };

})(jQuery);
