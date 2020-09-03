<div id="faq-create-review" class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="">
            <span class="title">How do I create a review with PHP?</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div class="inside">
        <p>Site Reviews provides a <code><a href="<?= admin_url('edit.php?post_type='.glsr()->post_type.'&page=documentation#tab-functions'); ?>" data-expand="#fn-glsr_create_review">glsr_create_review()</a></code> helper function to easily create a review.</p>
        <p>Here is an example:</p>
        <pre><code class="language-php">if (function_exists('glsr_create_review')) {
    $review = glsr_create_review([
        'content' => 'This is my review.',
        'date' => '2018-06-13',
        'email' => 'jane@doe.com',
        'name' => 'Jane Doe',
        'rating' => 5,
        'title' => 'Fantastic plugin!',
    ]);
}
</code></pre>
    </div>
</div>
