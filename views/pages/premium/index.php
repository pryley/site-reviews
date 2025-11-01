<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <hr class="wp-header-end" />
<?php echo $notices; ?>
<?php if (!$is_premium) { ?>
    <div class="glsr-premium about__container">
        <h2>
            <?php _ex('Level up with Site Reviews Premium', 'admin-text', 'site-reviews'); ?>
        </h2>
        <p class="is-subheading">
            <?php _ex('Get the most out of your reviews with a bunch of additional features and dedicated 24/7 support from Paul, the developer behind Site Reviews.', 'admin-text', 'site-reviews'); ?>
        </p>
        <div class="glsr-premium__features">
            <div class="glsr-premium__banner">
                <?php echo \GeminiLabs\SiteReviews\Helpers\Svg::get('assets/images/premium.svg'); ?>
            </div>
            <div class="glsr-premium__table">
                <?php if (!empty($features)) { ?>
                    <table>
                        <thead>
                            <th scope="col">
                                <div>
                                    <?php _ex('Compare Features', 'admin-text', 'site-reviews'); ?>
                                </div>
                            </th>
                            <th scope="col">
                                <div>
                                    <?php _ex('Free', 'admin-text', 'site-reviews'); ?>
                                </div>
                            </th>
                            <th scope="col">
                                <div>
                                    <?php _ex('Premium', 'admin-text', 'site-reviews'); ?>
                                </div>
                            </th>
                        </thead>
                        <tbody>
                        <?php
                        $no = sprintf('<i class="dashicons-before dashicons-no-alt" aria-label="%s"></i>', _x('No', 'admin-text', 'site-reviews'));
                        $yes = sprintf('<i class="dashicons-before dashicons-yes" aria-label="%s"></i>', _x('Yes', 'admin-text', 'site-reviews'));
                        foreach ($features as $args) {
                            $row = glsr()->args($args);
                            ?>
                            <tr style="<?php echo !$row->premium ? 'display:none;' : ''; ?>">
                                <td>
                                    <div>
                                        <?php if (!empty($row->tooltip)) { ?>
                                            <i class="glsr-tooltip dashicons-before dashicons-editor-help" data-tippy-content="<?php echo esc_attr($row->tooltip); ?>"></i>
                                        <?php } ?>
                                        <?php echo $row->feature; ?>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo $row->premium ? $no : $yes; ?></div>
                                </td>
                                <td>
                                    <div><?php echo $yes; ?></div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                <?php } ?>
                <?php if (count(array_filter($features, fn($row) => !$row['premium']))) { ?>
                    <button type="button" class="components-button is-next-40px-default-size is-pressed" style="font-size: 1rem; justify-content: center; min-width: 100%;">
                        <?php _ex('Compare more features ↓', 'admin-text', 'site-reviews'); ?>
                    </button>
                <?php } else { ?>
                    <a href="<?php echo glsr_premium_url('site-reviews-premium'); ?>" target="_blank" class="components-button is-next-40px-default-size is-pressed" style="font-size: 1rem; justify-content: center; min-width: 100%;">
                        <?php _ex('View all premium features &rarr;', 'admin-text', 'site-reviews'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="glsr-addons">
        <?php glsr('Modules\Html\Template')->renderMultiple('partials/addons/addon', $addons); ?>
    </div>
<?php } ?>
</div>
