/** global: GLSR */

import Excerpts from './public/excerpts.js';
import Forms from './public/forms.js';
import Pagination from './public/pagination.js';

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = {};
}
window.GLSR.forms = [];

document.addEventListener('DOMContentLoaded', function () {
    // set text direction class
    var widgets = document.querySelectorAll('.glsr');
    for (var i = 0; i < widgets.length; i++) {
        var direction = window.getComputedStyle(widgets[i], null).getPropertyValue('direction');
        widgets[i].classList.add('glsr-' + direction);
    }
    // Check for unsupported browser versions (<=IE9)
    if (!(document.all && !window.atob)) {
        new Forms();
        new Pagination();
        new Excerpts();
    }
});
