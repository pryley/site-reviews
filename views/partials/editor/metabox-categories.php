<?php defined('ABSPATH') || exit; ?>

<div id="taxonomy-<?php echo $taxonomy->name; ?>" class="categorydiv">
    <ul id="<?php echo $taxonomy->name; ?>-tabs" class="category-tabs">
        <li class="tabs">
            <a href="#<?php echo $taxonomy->name; ?>-all"><?php echo esc_html($taxonomy->labels->all_items); ?></a>
        </li>
        <li class="hide-if-no-js">
            <a href="#<?php echo $taxonomy->name; ?>-pop"><?php echo esc_html_x('Most Used', 'admin-text', 'site-reviews'); ?></a>
        </li>
    </ul>
    <div id="<?php echo $taxonomy->name; ?>-pop" class="tabs-panel" style="display: none;">
        <ul id="<?php echo $taxonomy->name; ?>checklist-pop" class="categorychecklist form-no-clear" >
            <?php $popular_ids = wp_popular_terms_checklist($taxonomy->name); ?>
        </ul>
    </div>
    <div id="<?php echo $taxonomy->name; ?>-all" class="tabs-panel">
        <input type="hidden" name="tax_input[<?php echo $taxonomy->name; ?>][]" value='0' />
        <ul id="<?php echo $taxonomy->name; ?>checklist" data-wp-lists="list:<?php echo $taxonomy->name; ?>" class="categorychecklist form-no-clear">
            <?php
                wp_terms_checklist($post->ID, [
                    'taxonomy' => $taxonomy->name,
                    'popular_cats' => $popular_ids,
                ]);
            ?>
        </ul>
    </div>
    <?php if (current_user_can($taxonomy->cap->edit_terms)) { ?>
        <div id="<?php echo $taxonomy->name; ?>-adder" class="wp-hidden-children">
            <a id="<?php echo $taxonomy->name; ?>-add-toggle" href="#<?php echo $taxonomy->name; ?>-add" class="hide-if-no-js taxonomy-add-new">
                <?php echo sprintf('+ %s', esc_html($taxonomy->labels->add_new_item)); ?>
            </a>
            <div id="<?php echo $taxonomy->name; ?>-add" class="category-add wp-hidden-child">
                <?php wp_nonce_field("add-{$taxonomy->name}", "_ajax_nonce-add-{$taxonomy->name}", false); ?>
                <label class="screen-reader-text" for="new<?php echo $taxonomy->name; ?>">
                    <?php echo esc_html($taxonomy->labels->add_new_item); ?>
                </label>
                <input type="text" id="new<?php echo $taxonomy->name; ?>" class="form-required form-input-tip" 
                    aria-required="true"
                    name="new<?php echo $taxonomy->name; ?>" 
                    value="<?php echo esc_attr($taxonomy->labels->new_item_name); ?>" 
                />
                <input type="button" id="<?php echo $taxonomy->name; ?>-add-submit" class="button category-add-submit" 
                    data-wp-lists="add:<?php echo $taxonomy->name; ?>checklist:<?php echo $taxonomy->name; ?>-add" 
                    value="<?php echo esc_attr($taxonomy->labels->add_new_item); ?>"
                />
                <span id="<?php echo $taxonomy->name; ?>-ajax-response"></span>
            </div>
        </div>
    <?php } ?>
</div>
