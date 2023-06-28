<?php defined('WPINC') || exit; ?>

<div class="hook-instance">
    <h3><?= esc_html__('Reviewers', 'site-reviews'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['reviewer' => 'points'])); ?>">
                    <?= esc_html__('Points', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?= esc_attr($hook->field_id(['reviewer' => 'points'])); ?>"
                    min="0"
                    name="<?= esc_attr($hook->field_name(['reviewer' => 'points'])); ?>"
                    value="<?= esc_attr($hook->core->number($hook->prefs['reviewer']['points'])); ?>"
                />
                <span class="description">
                    <?= esc_html__('For writing a review.', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['reviewer' => 'log'])); ?>">
                    <?= esc_html__('Template', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?= esc_attr($hook->field_id(['reviewer' => 'log'])); ?>"
                    name="<?= esc_attr($hook->field_name(['reviewer' => 'log'])); ?>"
                    placeholder="<?= esc_attr__('required', 'site-reviews'); ?>"
                    value="<?= esc_attr($hook->prefs['reviewer']['log']); ?>"
                />
                <span class="description">
                    <?= wp_kses_post($hook->available_template_tags(['general', 'post'])); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['reviewer' => 'per_post'])); ?>">
                    <?= esc_html__('Limit per post', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control"
                    id="<?= esc_attr($hook->field_id(['reviewer' => 'per_post'])); ?>"
                    min="0"
                    name="<?= esc_attr($hook->field_name(['reviewer' => 'per_post'])); ?>"
                    value="<?= esc_attr($hook->prefs['reviewer']['per_post']); ?>"
                />
                <span class="description">
                    <?= wp_kses_post($hook->core->template_tags_general(__('Number of reviews per post that grants %_plural% to the reviewer. Use zero for unlimited.', 'site-reviews'))); ?>
                </span>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['reviewer' => 'per_day'])); ?>">
                    <?= esc_html__('Limit per day', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control"
                    id="<?= esc_attr($hook->field_id(['reviewer' => 'per_day'])); ?>"
                    min="0"
                    name="<?= esc_attr($hook->field_name(['reviewer' => 'per_day'])); ?>"
                    value="<?= esc_attr($hook->prefs['reviewer']['per_day']); ?>"
                />
                <span class="description">
                    <?= wp_kses_post($hook->core->template_tags_general(__('Number of reviews per day that grants %_plural% to the reviewer. Use zero for unlimited.', 'site-reviews'))); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <div class="checkbox">
                    <label for="<?= esc_attr($hook->field_id(['reviewer' => 'remove_on_trash'])); ?>">
                        <input type="checkbox"
                            id="<?= esc_attr($hook->field_id(['reviewer' => 'remove_on_trash'])); ?>"
                            name="<?= esc_attr($hook->field_name(['reviewer' => 'remove_on_trash'])); ?>"
                            value="1"
                            <?php checked($hook->prefs['reviewer']['remove_on_trash'], 1); ?>
                        /> <?= wp_kses_post($hook->core->template_tags_general(__('Remove %plural% when review is unapproved or trashed.', 'site-reviews'))); ?>
                    </label>
                </div>
                <span class="description"></span>
            </div>
        </div>
    </div>
</div>

<div class="hook-instance">
    <h3><?= esc_html__('Assigned Post Authors', 'site-reviews'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['assigned_author' => 'points'])); ?>">
                    <?= esc_html__('Points', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?= esc_attr($hook->field_id(['assigned_author' => 'points'])); ?>"
                    min="0"
                    name="<?= esc_attr($hook->field_name(['assigned_author' => 'points'])); ?>"
                    value="<?= esc_attr($hook->core->number($hook->prefs['assigned_author']['points'])); ?>"
                />
                <span class="description">
                    <?= esc_html__('For getting a review.', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['assigned_author' => 'log'])); ?>">
                    <?= esc_html__('Template', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?= esc_attr($hook->field_id(['assigned_author' => 'log'])); ?>"
                    name="<?= esc_attr($hook->field_name(['assigned_author' => 'log'])); ?>"
                    placeholder="<?= esc_attr__('required', 'site-reviews'); ?>"
                    value="<?= esc_attr($hook->prefs['assigned_author']['log']); ?>"
                />
                <span class="description">
                    <?= wp_kses_post($hook->available_template_tags(['general', 'post'])); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="hook-instance">
    <h3><?= esc_html__('Assigned Users', 'site-reviews'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['assigned_user' => 'points'])); ?>">
                    <?= esc_html__('Points', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?= esc_attr($hook->field_id(['assigned_user' => 'points'])); ?>"
                    min="0"
                    name="<?= esc_attr($hook->field_name(['assigned_user' => 'points'])); ?>"
                    value="<?= esc_attr($hook->core->number($hook->prefs['assigned_user']['points'])); ?>"
                />
                <span class="description">
                    <?= esc_html__('For getting a review.', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?= esc_attr($hook->field_id(['assigned_user' => 'log'])); ?>">
                    <?= esc_html__('Template', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?= esc_attr($hook->field_id(['assigned_user' => 'log'])); ?>"
                    name="<?= esc_attr($hook->field_name(['assigned_user' => 'log'])); ?>"
                    placeholder="<?= esc_attr__('required', 'site-reviews'); ?>"
                    value="<?= esc_attr($hook->prefs['assigned_user']['log']); ?>"
                />
                <span class="description">
                    <?= wp_kses_post($hook->available_template_tags(['general'])); ?>
                </span>
            </div>
        </div>
    </div>
</div>
