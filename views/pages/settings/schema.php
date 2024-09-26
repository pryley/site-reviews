<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?php echo _x('JSON-LD Schema Settings', 'admin-text', 'site-reviews'); ?></h2>

<div class="components-notice is-info" style="margin-bottom: 12px;margin-left:0;">
    <p class="components-notice__content">
        <?php echo _x('<strong>The schema is disabled by default.</strong> Use the schema option in your blocks or shortcodes to enable it.', 'admin-text', 'site-reviews'); ?>
    </p>
</div>

<div class="components-notice is-warning" style="background-color:#fff;margin-left:0;">
    <p class="components-notice__content">
        <?php echo sprintf(_x('Google limits the schema types that can trigger review rich results in search. To learn more, please %sread this%s.', 'admin-text', 'site-reviews'),
            '<a href="https://developers.google.com/search/blog/2019/09/making-review-rich-results-more-helpful" target="_blank">',
            '</a>'
        ); ?>
    </p>
</div>

<p>
    <?php echo sprintf(_x('The schema is used to display rich review snippets in Google\'s search results. If the schema has been enabled, you can use the %s tool to test your pages for valid schema.', 'admin-text', 'site-reviews'),
        sprintf('<a href="https://search.google.com/test/rich-results" target="_blank">%s</a>', _x('Google Rich Results', 'admin-text', 'site-reviews'))
    ); ?>
</p>
<p>
    <?php echo sprintf(_x('In some cases it may be useful to link the Site Reviews schema with other schema on your page, this is done by adding the %s unique identifier property to each schema that you wish to link. If you are using Woocommerce and have set the Schema Type to "Product", Site Reviews will automatically do this for you. In all other cases, either use the <code>schema_identifier</code> Custom Field name in the %s, or use the %s hook.', 'admin-text', 'site-reviews'),
        '<code><a href="https://rich-snippets.io/how-to-build-complex-structured-data/#b-reference-by-id" target="_blank">@id</a></code>',
        sprintf('<a href="https://wordpress.org/support/article/custom-fields/" target="_blank">%s</a>', _x('Custom Fields metabox', 'admin-text', 'site-reviews')),
        sprintf('<a data-expand="#hooks-filter-schema" href="%s">site-reviews/schema/&lt;schema_type&gt;</a>', glsr_admin_url('documentation', 'hooks'))
    ); ?>
</p>

<table class="form-table">
    <tbody>
        {{ rows }}
    </tbody>
</table>
