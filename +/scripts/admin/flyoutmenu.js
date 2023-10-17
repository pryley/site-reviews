class Flyoutmenu {
    constructor () {
        const $flyout = jQuery('#glsr-flyout');
        if ($flyout.length === 0) return;
        const $head = $flyout.find('.glsr-flyout-head');
        const $sullie = $head.find('img');
        const menu = {
            state: 'inactive',
            // srcInactive: $sullie.attr('src'),
            srcActive: $sullie.data('active'),
        };

        $head.on('click', function (ev) {
            ev.preventDefault();
            if ('active' === menu.state) {
                $flyout.removeClass('opened');
                // $sullie.attr('src', menu.srcInactive);
                menu.state = 'inactive';
            } else {
                $flyout.addClass('opened');
                // $sullie.attr('src', menu.srcActive);
                menu.state = 'active';
            }
        });
    }
}

export default Flyoutmenu;
