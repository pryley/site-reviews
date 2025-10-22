<?php defined('ABSPATH') || exit; 

$productCollectionsUrl = add_query_arg([
    'canvas' => 'edit',
    'p' => '/wp_template/surecart/surecart//taxonomy-sc_collection',
], admin_url('site-editor.php'));

$productsUrl = add_query_arg([
    'canvas' => 'edit',
    'p' => '/wp_template/surecart/surecart//single-sc_product',
], admin_url('site-editor.php'));

$upsellUrl = add_query_arg([
    'canvas' => 'edit',
    'p' => '/wp_template/surecart/surecart//single-upsell',
], admin_url('site-editor.php'));

?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="integrations-surecart">
            <span class="title has-logo">
                <?php 
                    echo \GeminiLabs\SiteReviews\Helpers\Svg::get('assets/images/icons/integrations/surecart.svg', [
                        'fill' => 'currentColor',
                        'height' => 24,
                        'width' => 24,
                    ]);
                ?>
                SureCart
            </span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="integrations-surecart" class="inside">
        <h3>Enable the SureCart integration</h3>
        <p>Go to the <?php echo glsr_admin_link('settings.integrations.surecart'); ?> page and enable the integration.</p>
        <p>After the integration is enabled, you will be able to add the "Product Rating" and "Product Reviews" blocks to your SureCart template pages.</p>
        <h3>Edit the SureCart Product Collections template</h3>
        <p>The "Product Collections" template is used to display product collections (i.e. your Shop page).</p>
        <ol>
            <li>Edit the <a href="<?php echo esc_url($productCollectionsUrl); ?>">Product Collections template</a>.</li>
            <li>Click the Title of one of the products.</li>
            <li>Click the Options icon and select "Add after" from the dropdown menu.</li>
            <li>Add the "Product Rating" block.</li>
            <li>In the block Settings tab, change the "Text" option to <code>({num})</code>.</li>
            <li>In the block Style tab, change the "Typography size" option to "Small".</li>
            <li>Save the template.</li>
        </ol>
        <h3>Edit the SureCart Products template</h3>
        <p>The "Products" template is used to display individual products.</p>
        <ol>
            <li>Edit the <a href="<?php echo esc_url($productsUrl); ?>">Products template</a>.</li>
            <li>Click the Title of the product.</li>
            <li>Click the Options icon and select "Add after" from the dropdown menu.</li>
            <li>Add the "Product Rating" block.</li>
            <li>In the block Settings tab, enable the "Make text a link" option.</li>
            <li>In the block Style tab, change the "Typography size" option to "Medium".</li>
            <li>Click the Content block of the product.</li>
            <li>Click the Options icon and select "Add after" from the dropdown menu.</li>
            <li>Add the "Product Reviews" block.</li>
            <li>Save the template.</li>
        </ol>
        <h3>Edit the SureCart Upsells template</h3>
        <p>The "Upsell" template is used to display individual upsell products</p>
        <ol>
            <li>Edit the <a href="<?php echo esc_url($upsellUrl); ?>">Upsells template</a>.</li>
            <li>Click the Title of the product.</li>
            <li>Click the Options icon and select "Add after" from the dropdown menu.</li>
            <li>Add the "Product Rating" block.</li>
            <li>In the block Style tab, change the "Typography size" option to "Medium".</li>
            <li>Save the template.</li>
        </ol>
    </div>
</div>
