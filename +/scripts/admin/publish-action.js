import Ajax from '@/admin/ajax.js';

class PublishAction {
    constructor () {
        this.root = jQuery('.misc-pub-section[data-action]');
        this.root.find('a[data-click]').on('click', (ev) => {
            ev.preventDefault();
            const $el = jQuery(ev.currentTarget);
            const eventFn = $el.data('click');
            if ('function' === typeof this[eventFn]) {
                this[eventFn]($el)
            }
        })
    }

    cancel ($el) {
        const $section = $el.parent();
        const $root = $section.parent();
        const value = $section.find('input[type="hidden"]').val();
        $section.find('select').val(value);
        $section.slideUp('fast');
        $root.find('a[data-click="edit"]').show().focus();
    }

    edit ($el) {
        const $section = $el.parent().find('.misc-pub-select');
        if (!$section.is(':hidden')) return;
        $section.slideDown('fast', () => $section.find('select').focus());
        $el.hide();
    }

    save ($el) {
        const $section = $el.parent();
        const $select = $section.find('select');
        const $selected = $select.children(':selected');
        const $root = $section.parent();
        const text = $selected.data('alt') || $selected.text()
        const data = {
            _action: $root.data('action'),
            post_id: jQuery('#post_ID').val(),
            value: $section.find('select').val(),
        };
        (new Ajax(data)).post(response => {
            $section.find('input[type="hidden"]').val(response?.value)
            $select.val(response?.value)
        });
        $section.slideUp('fast');
        $root.find('.misc-pub-text').text(text)
        $root.find('a[data-click="edit"]').show().focus();
    }
}

export default PublishAction;
