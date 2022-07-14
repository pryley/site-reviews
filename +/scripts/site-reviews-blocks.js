import "./blocks/block-form";
import "./blocks/block-reviews";
import "./blocks/block-summary";
import Event from './public/event.js';

if (!window.hasOwnProperty('GLSR')) {
    window.GLSR = { Event };
}
