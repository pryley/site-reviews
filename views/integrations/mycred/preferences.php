<?php defined('WPINC') || exit; ?>

<div class="hook-instance">
    <h3><?php echo esc_html_x('Reviewers', 'admin-text', 'site-reviews'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['reviewer' => 'points'])); ?>">
                    <?php echo esc_html_x('Points', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['reviewer' => 'points'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['reviewer' => 'points'])); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    value="<?php echo esc_attr($hook->core->number($hook->prefs['reviewer']['points'])); ?>"
                />
                <span class="description">
                    <?php echo esc_html_x('For writing a review.', 'admin-text', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['reviewer' => 'log'])); ?>">
                    <?php echo esc_html_x('Template', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['reviewer' => 'log'])); ?>"
                    name="<?php echo esc_attr($hook->field_name(['reviewer' => 'log'])); ?>"
                    placeholder="<?php echo esc_attr($hook->defaults['reviewer']['log']); ?>"
                    value="<?php echo esc_attr($hook->prefs['reviewer']['log']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->available_template_tags(['general', 'post'])); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['reviewer' => 'points_deduction'])); ?>">
                    <?php echo esc_html_x('Deduct Points', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['reviewer' => 'points_deduction'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['reviewer' => 'points_deduction'])); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    value="<?php echo esc_attr($hook->core->number($hook->prefs['reviewer']['points_deduction'])); ?>"
                />
                <span class="description">
                    <?php echo esc_html_x('For losing a review.', 'admin-text', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['reviewer' => 'log_deduction'])); ?>">
                    <?php echo esc_html_x('Deduct Template', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['reviewer' => 'log_deduction'])); ?>"
                    name="<?php echo esc_attr($hook->field_name(['reviewer' => 'log_deduction'])); ?>"
                    placeholder="<?php echo esc_attr($hook->defaults['reviewer']['log_deduction']); ?>"
                    value="<?php echo esc_attr($hook->prefs['reviewer']['log_deduction']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->available_template_tags(['general', 'post'])); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['reviewer' => 'per_post'])); ?>">
                    <?php echo esc_html_x('Limit per post', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control"
                    id="<?php echo esc_attr($hook->field_id(['reviewer' => 'per_post'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['reviewer' => 'per_post'])); ?>"
                    value="<?php echo esc_attr($hook->prefs['reviewer']['per_post']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->core->template_tags_general(_x('Number of reviews per post that grants %_plural% to the reviewer. Use zero for unlimited.', 'admin-text', 'site-reviews'))); ?>
                </span>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['reviewer' => 'per_day'])); ?>">
                    <?php echo esc_html_x('Limit per day', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control"
                    id="<?php echo esc_attr($hook->field_id(['reviewer' => 'per_day'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['reviewer' => 'per_day'])); ?>"
                    value="<?php echo esc_attr($hook->prefs['reviewer']['per_day']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->core->template_tags_general(_x('Number of reviews per day that grants %_plural% to the reviewer. Use zero for unlimited.', 'admin-text', 'site-reviews'))); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="hook-instance">
    <h3><?php echo esc_html_x('Assigned Post Authors', 'admin-text', 'site-reviews'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_author' => 'points'])); ?>">
                    <?php echo esc_html_x('Points', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_author' => 'points'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['assigned_author' => 'points'])); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    value="<?php echo esc_attr($hook->core->number($hook->prefs['assigned_author']['points'])); ?>"
                />
                <span class="description">
                    <?php echo esc_html_x('For getting a review.', 'admin-text', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_author' => 'log'])); ?>">
                    <?php echo esc_html_x('Template', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_author' => 'log'])); ?>"
                    name="<?php echo esc_attr($hook->field_name(['assigned_author' => 'log'])); ?>"
                    placeholder="<?php echo esc_attr($hook->defaults['assigned_author']['log']); ?>"
                    value="<?php echo esc_attr($hook->prefs['assigned_author']['log']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->available_template_tags(['general', 'post'])); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_author' => 'points_deduction'])); ?>">
                    <?php echo esc_html_x('Deduct Points', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_author' => 'points_deduction'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['assigned_author' => 'points_deduction'])); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    value="<?php echo esc_attr($hook->core->number($hook->prefs['assigned_author']['points_deduction'])); ?>"
                />
                <span class="description">
                    <?php echo esc_html_x('For losing a review.', 'admin-text', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_author' => 'log_deduction'])); ?>">
                    <?php echo esc_html_x('Deduct Template', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_author' => 'log_deduction'])); ?>"
                    name="<?php echo esc_attr($hook->field_name(['assigned_author' => 'log_deduction'])); ?>"
                    placeholder="<?php echo esc_attr($hook->defaults['assigned_author']['log_deduction']); ?>"
                    value="<?php echo esc_attr($hook->prefs['assigned_author']['log_deduction']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->available_template_tags(['general', 'post'])); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="hook-instance">
    <h3><?php echo esc_html_x('Assigned Users', 'admin-text', 'site-reviews'); ?></h3>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_user' => 'points'])); ?>">
                    <?php echo esc_html_x('Points', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_user' => 'points'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['assigned_user' => 'points'])); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    value="<?php echo esc_attr($hook->core->number($hook->prefs['assigned_user']['points'])); ?>"
                />
                <span class="description">
                    <?php echo esc_html_x('For getting a review.', 'admin-text', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_user' => 'log'])); ?>">
                    <?php echo esc_html_x('Template', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_user' => 'log'])); ?>"
                    name="<?php echo esc_attr($hook->field_name(['assigned_user' => 'log'])); ?>"
                    placeholder="<?php echo esc_attr($hook->defaults['assigned_user']['log']); ?>"
                    value="<?php echo esc_attr($hook->prefs['assigned_user']['log']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->available_template_tags(['general'])); ?>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_user' => 'points_deduction'])); ?>">
                    <?php echo esc_html_x('Deduct Points', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="number" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_user' => 'points_deduction'])); ?>"
                    min="0"
                    name="<?php echo esc_attr($hook->field_name(['assigned_user' => 'points_deduction'])); ?>"
                    step="<?php echo esc_attr($step); ?>"
                    value="<?php echo esc_attr($hook->core->number($hook->prefs['assigned_user']['points_deduction'])); ?>"
                />
                <span class="description">
                    <?php echo esc_html_x('For losing a review.', 'admin-text', 'site-reviews'); ?>
                </span>
            </div>
        </div>
        <div class="col-md-8 col-xs-12">
            <div class="form-group">
                <label for="<?php echo esc_attr($hook->field_id(['assigned_user' => 'log_deduction'])); ?>">
                    <?php echo esc_html_x('Deduct Template', 'admin-text', 'site-reviews'); ?>
                </label>
                <input type="text" class="form-control" 
                    id="<?php echo esc_attr($hook->field_id(['assigned_user' => 'log_deduction'])); ?>"
                    name="<?php echo esc_attr($hook->field_name(['assigned_user' => 'log_deduction'])); ?>"
                    placeholder="<?php echo esc_attr($hook->defaults['assigned_user']['log_deduction']); ?>"
                    value="<?php echo esc_attr($hook->prefs['assigned_user']['log_deduction']); ?>"
                />
                <span class="description">
                    <?php echo wp_kses_post($hook->available_template_tags(['general'])); ?>
                </span>
            </div>
        </div>
    </div>
</div>
