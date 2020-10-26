/** global: GLSR */

import Ajax from './public/ajax.js';
import Excerpts from './public/excerpts.js';
import Forms from './public/forms.js';
import Pagination from './public/pagination.js';

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = {};
}
window.GLSR.ajax = new Ajax();
window.GLSR.forms = [];

document.addEventListener('DOMContentLoaded', function () {
    // set text direction class
    const widgets = document.querySelectorAll('.glsr');
    for (let i = 0; i < widgets.length; i++) {
        let direction = window.getComputedStyle(widgets[i], null).getPropertyValue('direction');
        widgets[i].classList.add('glsr-' + direction);
    }
    window.GLSR.Forms = Forms;
    new Forms();
    new Pagination();
    new Excerpts();
});
