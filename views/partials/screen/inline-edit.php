<?php defined('ABSPATH') || exit; ?>

<form method="get">
    <table style="display:none">
        <tbody id="inlineedit">
            <tr id="inline-edit" style="display:none" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-<?php echo glsr()->post_type; ?>">
                <td colspan="<?php echo esc_attr($columns); ?>" class="colspanchange">
                    <div class="inline-edit-wrapper" role="region" aria-labelledby="quick-edit-legend">
                        <fieldset class="glsr-inline-edit-col-left">
                            <legend id="quick-edit-legend"  class="inline-edit-legend">
                                <?php echo _x('Respond to the review', 'admin-text', 'site-reviews'); ?>
                            </legend>
                            <div class="inline-edit-col">
                                <label>
                                    <span class=""><?php echo _x('Their Review', 'admin-text', 'site-reviews'); ?></span>
                                    <textarea cols="22" rows="1" data-name="post_content" readonly></textarea>
                                </label>
                            </div>
                        </fieldset>
                        <fieldset class="glsr-inline-edit-col-right">
                            <div class="inline-edit-col">
                                <label>
                                    <span class=""><?php echo _x('Your Response', 'admin-text', 'site-reviews'); ?></span>
                                    <textarea cols="22" rows="1" name="_response" class="ptitle"></textarea>
                                </label>
                            </div>
                        </fieldset>
                        <div class="submit inline-edit-save">
                            <?php wp_nonce_field('inlineeditnonce', '_inline_edit', false); ?>
                            <input type="hidden" name="screen" value="<?php echo esc_attr($screen_id); ?>" />
                            <button type="button" class="button cancel alignleft"><?php echo _x('Cancel', 'admin-text', 'site-reviews'); ?></button>
                            <button type="button" class="button button-primary save alignright"><?php echo _x('Update', 'admin-text', 'site-reviews'); ?></button>
                            <span class="spinner"></span>
                            <br class="clear" />
                            <div class="notice notice-error notice-alt inline hidden">
                                <p class="error"></p>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr id="bulk-edit" style="display:none" class="inline-edit-row inline-edit-row-post bulk-edit-row bulk-edit-row-post bulk-edit-<?php echo glsr()->post_type; ?>">
                <td colspan="<?php echo esc_attr($columns); ?>" class="colspanchange">
                    <div class="inline-edit-wrapper" role="region" aria-labelledby="bulk-edit-legend" tabindex="-1">
                        <fieldset class="inline-edit-col-left">
                            <legend id="bulk-edit-legend" class="inline-edit-legend"><?php echo _x('Bulk Edit', 'admin-text', 'site-reviews'); ?></legend>
                            <div class="inline-edit-col">
                                <div id="bulk-title-div">
                                    <div id="bulk-titles" style="font-size:13px;margin-bottom:0;"></div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="inline-edit-col-center inline-edit-categories">
                            <div class="inline-edit-col">
                                <span class="title inline-edit-categories-label"><?php echo esc_html($taxonomy->labels->name); ?></span>
                                <input type="hidden" name="tax_input[<?php echo esc_attr($taxonomy->name); ?>][]" value="0" />
                                <ul class="cat-checklist <?php echo esc_attr($taxonomy->name); ?>-checklist">
                                    <?php wp_terms_checklist(0, ['taxonomy' => $taxonomy->name]); ?>
                                </ul>
                            </div>
                        </fieldset>
                        <fieldset class="inline-edit-col-right">
                            <div class="inline-edit-col">
                                <div class="inline-edit-group wp-clearfix">
                                    <label class="inline-edit-status alignleft">
                                        <span class="title" style="margin:.1em 0;width: 100%;"><?php echo _x('Status', 'admin-text', 'site-reviews'); ?></span>
                                        <select name="_status">
                                            <option value="-1">&mdash; <?php echo _x('No Change', 'admin-text', 'site-reviews'); ?> &mdash;</option>
                                            <?php if (glsr()->can('publish_posts')) : ?>
                                                <option value="publish"><?php echo _x('Approved', 'admin-text', 'site-reviews'); ?></option>
                                            <?php endif; ?>
                                            <option value="pending"><?php echo _x('Unapproved', 'admin-text', 'site-reviews'); ?></option>
                                        </select>
                                    </label>
                                    <label class="inline-edit-author alignleft">
                                        <span class="title" style="margin:.1em 0;width: 100%;"><?php echo _x('Author', 'admin-text', 'site-reviews'); ?></span>
                                        <?php echo $author_dropdown; ?>
                                    </label>
                                </div>
                                <?php if (post_type_supports(glsr()->post_type, 'comments') || post_type_supports(glsr()->post_type, 'trackbacks')) : ?>
                                    <div class="inline-edit-group wp-clearfix">
                                        <?php if (post_type_supports(glsr()->post_type, 'comments')) : ?>
                                            <label class="alignleft">
                                                <span class="title"><?php echo _x('Comments', 'admin-text', 'site-reviews'); ?></span>
                                                <select name="comment_status">
                                                    <option value="">&mdash; <?php echo _x('No Change', 'admin-text', 'site-reviews'); ?> &mdash;</option>
                                                    <option value="open"><?php echo _x('Allow', 'admin-text', 'site-reviews'); ?></option>
                                                    <option value="closed"><?php echo _x('Do not allow', 'admin-text', 'site-reviews'); ?></option>
                                                </select>
                                            </label>
                                        <?php endif; ?>
                                        <?php if (post_type_supports(glsr()->post_type, 'trackbacks')) : ?>
                                            <label class="alignright">
                                                <span class="title"><?php echo _x('Pings', 'admin-text', 'site-reviews'); ?></span>
                                                <select name="ping_status">
                                                    <option value="">&mdash; <?php echo _x('No Change', 'admin-text', 'site-reviews'); ?> &mdash;</option>
                                                    <option value="open"><?php echo _x('Allow', 'admin-text', 'site-reviews'); ?></option>
                                                    <option value="closed"><?php echo _x('Do not allow', 'admin-text', 'site-reviews'); ?></option>
                                                </select>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="inline-edit-group wp-clearfix">
                                    <span class="title"><?php echo _x('Assigned Posts', 'admin-text', 'site-reviews'); ?></span>
                                    <div class="glsr-search-multibox" id="glsr-search-posts">
                                        <div class="glsr-spinner"><span class="spinner"></span></div>
                                        <div class="glsr-search-multibox-entries">
                                            <div class="glsr-selected-entries"></div>
                                            <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?php echo esc_attr_x('Search by ID or title...', 'admin-text', 'site-reviews'); ?>">
                                        </div>
                                        <div class="glsr-search-results"></div>
                                    </div>
                                </div>
                                <div class="inline-edit-group wp-clearfix">
                                    <span class="title"><?php echo _x('Assigned Users', 'admin-text', 'site-reviews'); ?></span>
                                    <div class="glsr-search-multibox" id="glsr-search-users">
                                        <div class="glsr-spinner"><span class="spinner"></span></div>
                                        <div class="glsr-search-multibox-entries">
                                            <div class="glsr-selected-entries"></div>
                                            <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?php echo esc_attr_x('Search by ID or name...', 'admin-text', 'site-reviews'); ?>">
                                        </div>
                                        <div class="glsr-search-results"></div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <?php echo $additional_fieldsets; ?>
                        <div class="submit inline-edit-save">
                            <button type="button" class="button cancel alignleft"><?php echo _x('Cancel', 'admin-text', 'site-reviews'); ?></button>
                            <input type="submit" name="bulk_edit" id="bulk_edit" class="button button-primary alignright" value="<?php echo esc_attr_x('Update', 'admin-text', 'site-reviews'); ?>">
                            <input type="hidden" name="post_view" value="<?php echo esc_attr($mode); ?>" />
                            <input type="hidden" name="screen" value="<?php echo esc_attr($screen_id); ?>" />
                            <br class="clear">
                            <div class="notice notice-error notice-alt inline hidden">
                                <p class="error"></p>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</form>

<script type="text/html" id="tmpl-glsr-assigned-posts">
<?php include glsr()->path('views/partials/editor/assigned-entry.php'); ?>
</script>
<script type="text/html" id="tmpl-glsr-assigned-users">
<?php include glsr()->path('views/partials/editor/assigned-entry.php'); ?>
</script>
