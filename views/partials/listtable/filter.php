<?php defined('ABSPATH') || exit; ?>

<div id="<?= $id; ?>" class="glsr-filter <?= $class; ?>" role="combobox" aria-haspopup="true" aria-expanded="false" data-action="<?= $action; ?>">
    <input class="glsr-filter__value" type="hidden" name="<?= $name; ?>" value="<?= $value; ?>">
    <span class="glsr-filter__selected" role="textbox" aria-readonly="true" tabindex="0" title="<?= $selected; ?>"><?= $selected; ?></span>
    <div class="glsr-filter__dropdown">
        <input class="glsr-filter__search" type="search" role="searchbox"
            aria-autocomplete="list" aria-controls="<?= $id; ?>-listbox" aria-label="<?= _x('Search', 'admin-text', 'site-reviews'); ?>"
            autocapitalize="none" autocomplete="off" autocorrect="off" spellcheck="false"
            placeholder="<?= _x('Search...', 'admin-text', 'site-reviews'); ?>"
            tabindex="0"
        >
        <div id="<?= $id; ?>-listbox" class="glsr-filter__results" role="listbox" aria-expanded="false" aria-hidden="true"></div>
    </div>
</div>
