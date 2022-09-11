<?php defined('ABSPATH') || exit; ?>

<div id="misc-pub-verified" class="misc-pub-section misc-pub-verified">
    <label for="verified-status"><?= _x('Verified', 'admin-text', 'site-reviews'); ?>:</label>
    <span id="verified-status-text" class="verified-status-text"><?= $verified ? $context['yes'] : $context['no']; ?></span>
    <a href="#verified-status" class="edit-verified-status hide-if-no-js">
        <span aria-hidden="true"><?= _x('Edit', 'admin-text', 'site-reviews'); ?></span>
        <span class="screen-reader-text"><?= _x('Edit verified status', 'admin-text', 'site-reviews'); ?></span>
    </a>
    <div id="verified-status-select" class="verified-status-select hide-if-js">
        <input type="hidden" name="<?= glsr()->id; ?>[is_verified]" id="hidden-verified-status" value="<?= intval($verified); ?>">
        <select id="verified-status">
            <option value="1"<?php selected($verified, false); ?>><?= _x('Verify review', 'admin-text', 'site-reviews'); ?></option>
            <option value="0"<?php selected($verified, true); ?>><?= _x('Unverify review', 'admin-text', 'site-reviews'); ?></option>
        </select>
        <a href="#verified-status" class="save-verified-status hide-if-no-js button" data-no="{{ no }}" data-yes="{{ yes }}"><?= _x('OK', 'admin-text', 'site-reviews'); ?></a>
        <a href="#verified-status" class="cancel-verified-status hide-if-no-js button-cancel"><?= _x('Cancel', 'admin-text', 'site-reviews'); ?></a>
    </div>
</div>
