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
                <svg width="24" height="24" viewBox="0 0 400 400" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                   <path fill-rule="evenodd" clip-rule="evenodd" d="M40 0C17.9086 0 0 17.9086 0 40V360C0 382.091 17.9086 400 40 400H360C382.091 400 400 382.091 400 360V40C400 17.9086 382.091 0 360 0H40ZM126.226 110.011C138.654 97.5783 162.977 87.5 180.553 87.5H339.674L283.416 143.776H92.4714L126.226 110.011ZM116.905 256.224H307.85L274.095 289.99C261.667 302.422 237.344 312.5 219.768 312.5H60.6472L116.905 256.224ZM328.766 171.862H64.625L53.3735 183.117C28.5173 207.982 36.8637 228.138 72.0157 228.138H336.156L347.408 216.883C372.264 192.018 363.918 171.862 328.766 171.862Z" fill="currentColor" />
                </svg>
                SureCart
            </span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="integrations-surecart" class="inside">
        <h3>Enable the SureCart integration</h3>
        <p>Go to the <a href="<?php echo esc_url(glsr_admin_url('settings', 'integrations', 'surecart')); ?>">Site Reviews > Settings > Integrations > SureCart</a> page and enable the integration. After the integration is enabled, you will be able to add the "Product Rating" and "Product Reviews" blocks to your SureCart template pages.</p>
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
