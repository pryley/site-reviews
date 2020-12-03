<p class="glsr-heading">schema</p>
<p>Include the "schema" option to enable the aggregate rating schema for your reviews in Google. The difference between this and the schema option in the [site_reviews] shortcode is that this one only generates the aggregate ratings schema, while the other generates both the aggregate ratings schema and the review schema for each individual review that is visible on the page. If you have the choice, enable this option on the [site_reviews] shortcode instead.</p>
<p><span class="required">Important:</span> This option should only be used once on a page to avoid duplicate schemas; keep this in mind if you are using more than one [site_reviews] and/or [site_reviews_summary] shortcodes on the same page.</p>
<p>The default schema value is: <code>"false"</code></p>
<div class="shortcode-example">
    <input type="text" readonly class="code" value='[site_reviews_summary schema="true"]'>
    <pre><code class="syntax-shortcode"><span class="tag">[site_reviews_summary</span> <span class="attr-name">schema</span>=<span class="attr-value">"true"</span><span class="tag">]</span></code></pre>
</div>
