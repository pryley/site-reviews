import compat from '@/compat/blocks.js';

if (window.wp?.hooks) { // the block editor
    compat(window.wp.hooks)
}
