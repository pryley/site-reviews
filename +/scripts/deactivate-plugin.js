jQuery($ => {

    const Data = _glsr_deactivate;

    const Model = Backbone.Model.extend({
        defaults: {
            details: '',
            name: '',
            reason: '',
            slug: '',
            version: '',
        },
    });

    const Collection = Backbone.Collection.extend({
        model: Model,
    });

    const View = Backbone.View.extend({
        className: 'glsr-dp-overlay',
        isBusy: false,
        model: null,
        target: null,
        template: null,

        events: {
            'click': 'closeOverlay',
            'click .expand-info': 'expandDetails',
            'click .glsr-dp-reason': 'selectReason',
            'click .submit': 'submit',
            'input input[name="reason"]': 'updateModel',
            'input textarea[name="details"]': 'updateModel',
        },

        initialize: function (options) {
            _.extend(this, _.pick(options, 'target'))
            this.template = wp.template('glsr-deativate');
        },

        render: function () {
            let data = _.extend({}, this.model.toJSON(), Data, {
                action: this.target.attr('href'),
            });
            this.$el.html(this.template(data));
            this.containFocus();
            return this
        },

        closeOverlay: function (ev) {
            if (event.keyCode !== 27
                && !$(ev.target).is('.close')
                && !$(ev.target).is('.deactivate')
                && !$(ev.target).is('.glsr-dp-backdrop')) {
                return;
            }
            $('body').addClass('closing-overlay')
            this.$el.fadeOut(130, () => {
                $('body').removeClass('closing-overlay')
                $('body').removeClass('modal-open')
                this.remove()
                this.unbind()
                if (this.target ) {
                    this.target.trigger('focus')
                }
            })
        },

        containFocus: function () {
            _.delay(() => $('.glsr-dp-overlay').trigger('focus'), 100);
            this.$el.on('keydown.glsr', (ev) => {
                const $firstFocusable = this.$el.find('.glsr-dp-header button').first();
                const $lastFocusable = this.$el.find('.glsr-dp-footer a').last();
                if (9 === ev.which) {
                    if ($firstFocusable[0] === ev.target && ev.shiftKey) {
                        $lastFocusable.trigger('focus');
                        ev.preventDefault();
                    } else if ($lastFocusable[0] === ev.target && !ev.shiftKey) {
                        $firstFocusable.trigger('focus');
                        ev.preventDefault();
                    }
                }
            });
        },

        expandDetails: function () {
            this.$('#glsr-dp-info').slideToggle('fast')
        },

        onChange: function (ev) {
            if (!this.isBusy) {
                this.updateModel(ev.currentTarget)
            }
        },

        selectReason: function (ev) {
            const option = $(ev.currentTarget);
            const placeholder = option.data('placeholder');
            const helpNotice = this.$('.glsr-dp-help');
            const value = option.find('input').val();
            const infoNotice = ['feature-missing'];
            const warnNotice = ['confused', 'looking-for-different', 'not-working'];
            this.$('.glsr-dp-reason').removeClass('is-selected')
            this.$('.glsr-dp-details textarea').attr('placeholder', placeholder)
            this.$('.glsr-dp-details')['' === placeholder ? 'slideUp' : 'slideDown']('fast')
            option.toggleClass('is-selected')
            if (!~[].concat(infoNotice, warnNotice).indexOf(value)) {
                helpNotice.slideUp('fast')
            } else {
                helpNotice.find('.is-info')[!~infoNotice.indexOf(value) ? 'hide' : 'show']()
                helpNotice.find('.is-warning')[!~warnNotice.indexOf(value) ? 'hide' : 'show']()
                helpNotice.slideDown('fast')
            }
        },

        submit: function (ev) {
            ev.preventDefault();
            const data = {
                [Data.ajax.prefix]: _.extend({}, this.model.toJSON(), {
                    _action: 'deactivate',
                    _nonce: Data.ajax.nonce,
                })
            };
            $(ev.currentTarget)
                .addClass('is-busy')
                .prop('disabled', true)
                .text(Data.l10n.processing);
            wp.ajax.post(Data.ajax.action, data).always(() => {
                window.location.href = this.target.attr('href');
            })
        },

        updateModel: function (ev) {
            if (this.isBusy) return;
            this.isBusy = true;
            this.model.set(ev.target.name, ev.target.value, { validate: false })
            this.isBusy = false;
        },
    });

    const Dialog = Backbone.View.extend({
        el: '#the-list',
        collection: null,
        overlay: $('#glsr-dp-overlay'),

        events: {
            'click a[data-deactivate]': 'openOverlay',
        },

        initialize: function () {
            this.collection = new Collection(Data.plugins);
        },

        openOverlay: function (ev) {
            let target = $(ev.target);
            let slug = target.data('deactivate');
            let model = this.collection.findWhere({ slug })
            if (model) {
                let view = new View({ model, target });
                ev.preventDefault()
                $('body').addClass('modal-open')
                view.render()
                this.overlay.html(view.el)
            }
        },
    });

    new Dialog();
})
