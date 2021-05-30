<?php defined('ABSPATH') || die; ?>

<form method="get">
    <table style="display:none">
        <tbody id="inlineedit">
            <tr id="inline-edit" style="display:none" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-<?= glsr()->post_type; ?>">
                <td colspan="<?= $columns; ?>" class="colspanchange">
                    <fieldset class="glsr-inline-edit-col-left">
                        <legend class="inline-edit-legend">
                            <?= _x('Respond to the review', 'admin-text', 'site-reviews'); ?>
                        </legend>
                        <div class="inline-edit-col">
                            <label>
                                <span class=""><?= _x('Their Review', 'admin-text', 'site-reviews'); ?></span>
                                <textarea cols="22" rows="1" data-name="post_content" readonly></textarea>
                            </label>
                        </div>
                    </fieldset>
                    <fieldset class="glsr-inline-edit-col-right">
                        <div class="inline-edit-col">
                            <label>
                                <span class=""><?= _x('Your Response', 'admin-text', 'site-reviews'); ?></span>
                                <textarea cols="22" rows="1" name="_response" class="ptitle"></textarea>
                            </label>
                        </div>
                    </fieldset>
                    <div class="submit inline-edit-save">
                        <?php wp_nonce_field('inlineeditnonce', '_inline_edit', false); ?>
                        <input type="hidden" name="screen" value="<?= $screenId; ?>" />
                        <button type="button" class="button cancel alignleft"><?= _x('Cancel', 'admin-text', 'site-reviews'); ?></button>
                        <button type="button" class="button button-primary save alignright"><?= _x('Update', 'admin-text', 'site-reviews'); ?></button>
                        <span class="spinner"></span>
                        <br class="clear" />
                        <div class="notice notice-error notice-alt inline hidden">
                            <p class="error"></p>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</form>
