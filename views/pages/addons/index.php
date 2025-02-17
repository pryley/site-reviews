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
                <?php echo \GeminiLabs\SiteReviews\Helper::svg('assets/images/premium.svg'); ?>
            </div>
            <div class="glsr-premium__table">
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
                    $features = [
                        _x('Custom review forms', 'admin-text', 'site-reviews') => true,
                        _x('Custom review notifications', 'admin-text', 'site-reviews') => true,
                        _x('Custom review themes', 'admin-text', 'site-reviews') => true,
                        _x('Delete reviews on the frontend', 'admin-text', 'site-reviews') => true,
                        _x('Display review images in a gallery', 'admin-text', 'site-reviews') => true,
                        _x('Edit reviews on the frontend', 'admin-text', 'site-reviews') => true,
                        _x('Filter reviews by rating, category, and images', 'admin-text', 'site-reviews') => true,
                        _x('Flag inappropriate reviews for moderation', 'admin-text', 'site-reviews') => true,
                        _x('Respond to reviews on the frontend', 'admin-text', 'site-reviews') => true,
                        _x('Review carousels and grids', 'admin-text', 'site-reviews') => true,
                        _x('Review images', 'admin-text', 'site-reviews') => true,
                        _x('Search reviews for keywords', 'admin-text', 'site-reviews') => true,
                        _x('Sort reviews by rating or date', 'admin-text', 'site-reviews') => true,
                        _x('Translate reviews with DeepL AI', 'admin-text', 'site-reviews') => true,
                        _x('Upvote reviews', 'admin-text', 'site-reviews') => true,
                    ];
                    $no = sprintf('<i class="dashicons-before dashicons-no-alt" aria-label="%s"></i>', _x('No', 'admin-text', 'site-reviews'));
                    $yes = sprintf('<i class="dashicons-before dashicons-yes" aria-label="%s"></i>', _x('Yes', 'admin-text', 'site-reviews'));
                    foreach ($features as $feature => $isPremiumOnly) {
                        ?>
                        <tr>
                            <td>
                                <div>
                                    <i class="glsr-tooltip dashicons-before dashicons-editor-help"></i>
                                    <?php echo $feature; ?>
                                </div>
                            </td>
                            <td>
                                <div><?php echo $isPremiumOnly ? $no : $yes; ?></div>
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
                <button type="button" class="components-button is-next-40px-default-size is-pressed" style="font-size: 1rem; justify-content: center; margin-block-start: 1rem; min-width: 100%;">
                    <?php _ex('Compare more features ↓', 'admin-text', 'site-reviews'); ?>
                </button>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="glsr-addons">
        <?php glsr('Modules\Html\Template')->renderMultiple('partials/addons/addon', $addons); ?>
    </div>
<?php } ?>
</div>
