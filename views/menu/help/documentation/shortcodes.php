<?php defined( 'WPINC' ) || die; ?>

<div class="glsr-card card">
	<h3>[site_reviews]</h3>
	<p>This shortcode displays your most recently submitted reviews.</p>

	<code>assigned_to="100,101"</code>
	<p>Include the "assigned_to" attribute to limit reviews to those assigned to a specific page/post ID. Accepted values are either one or more post/page ID's (separated by commas), or "post_id" which will use the ID of the current page.</p>

	<code>category="13,test"</code>
	<p>Include the "category" attribute to limit reviews to one or more categories. Accepted values are either a category ID or slug.</p>

	<code>class="my-reviews full-width"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode.</p>

	<code>count=10</code>
	<p>By default, the shortcode displays the latest 10 reviews. Include the "count" attribute to change the number of reviews that are displayed.</p>

	<code>hide=author,content,date,rating,response,title,url</code>
	<p>By default the shortcode displays all review fields. Include the "hide" attribute to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>

	<code>offset=1</code>
	<p>Include the "offset" attribute to displace or pass over a number of reviews. For example, <em>[site_reviews count=5 offset=2]</em> will show 5 reviews, skipping the first two. It is NOT recommended to use this option with pagination enabled.</p>

	<code>pagination=true</code>
	<p>Include the "pagination" attribute to display reviews in multiple pages (i.e. Page 1, Page 2, etc.). The value can be "true", "ajax", or "false". The "ajax" value allows you to load the next page of reviews without loading a whole new page. When using pagination, only one <em>[site_reviews]</em> shortcode can be used on a page at a time.</p>

	<code>rating=4</code>
	<p>By default, the shortcode displays all 1-5 star reviews. Include the "rating" attribute to set the minimum star-rating of reviews to display.</p>

	<code>schema=true</code>
	<p>Include the "schema" attribute to enable rich snippets for your reviews, this is disabled by default. The difference between this and the schema option in the <em>[site_reviews_summary]</em> shortcode is that this one generates both the overall reviews rating schema and the schema for each individual review, while the other only generates the overall reviews rating schema.</p>
	<p><span class="required">Important:</span> This attribute should only be used once on a page to avoid duplicate schemas; keep that in mind if you are using more than one <em>[site_reviews]</em> and/or <em>[site_reviews_summary]</em> shortcodes on the same page.</p>

	<code>id="type some random text here"</code>
	<p>Include the "id" attribute to add a custom ID attribute to the shortcode. This is especially useful when using pagination with the ajax option.</p>

	<code>title="Our Reviews"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
</div>

<div class="glsr-card card">
	<h3>[site_reviews_summary]</h3>
	<p>This shortcode displays a summary of your reviews.</p>

	<code>assigned_to="100,101"</code>
	<p>Include the "assigned_to" attribute to limit the reviews used to calculate the average rating to those assigned to a specific page/post ID. Accepted values are either one or more post/page ID's (separated by commas), or "post_id" which will use the ID of the current page.</p>
	<p><span class="required">Important:</span> If you are using this shortcode together with the <em>[site_reviews]</em> shortcode, make sure you set this attribute value the same for both shortcodes.</p>

	<code>category="13,test"</code>
	<p>Include the "category" attribute to limit the reviews used to calculate the average rating to one or more categories. Accepted values are either a category ID or slug.</p>
	<p><span class="required">Important:</span> If you are using this shortcode together with the <em>[site_reviews]</em> shortcode, make sure you set this attribute value the same for both shortcodes.</p>

	<code>class="my-reviews-summary full-width"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode.</p>

	<code>count=20</code>
	<p>By default, the shortcode calculates the average rating for all reviews found in your set criteria. Include the "count" attribute to change the number of reviews that are used.</p>

	<code>hide=bars,rating,stars,summary</code>
	<p>By default the shortcode displays all fields. Include the "hide" attribute to hide any specific fields you don't want to show. If all fields are hidden, the shortcode will not be displayed.</p>

	<code>labels="5 star,4 star,3 star,2 star,1 star"</code>
	<p>The "labels" attribute allows you to enter custom labels for the percentage bar levels (from high to low), each level should be separated with a comma. The defaults labels are: "Excellent,Very good,Average,Poor,Terrible"</p>

	<code>rating=1</code>
	<p>By default, the shortcode uses all 1-5 star reviews to calculate the average rating. Include the "rating" attribute to set the minimum star-rating of reviews to use.</p>
	<p><span class="required">Important:</span> If you are using this shortcode together with the <em>[site_reviews]</em> shortcode, make sure you set this attribute value the same for both shortcodes.</p>

	<code>schema=true</code>
	<p>Include the "schema" attribute to enable rich snippets for your reviews, this is disabled by default. The difference between this and the schema option in the <em>[site_reviews]</em> shortcode is that this one only generates the overall reviews rating schema, while the other generates both the overall reviews rating schema and the schema for each individual review. If you have the choice, better to enable this attribute on the <em>[site_reviews]</em> shortcode instead.</p>
	<p><span class="required">Important:</span> This attribute should only be used once on a page to avoid duplicate schemas; keep that in mind if you are using more than one <em>[site_reviews]</em> and/or <em>[site_reviews_summary]</em> shortcodes on the same page.</p>

	<code>show_if_empty=false</code>
	<p>Include the "show_if_empty" attribute to specify whether or not to show the shortcode when there are no reviews to summarise.</p>

	<code>text="{rating} out of {max} stars"</code>
	<p>The "text" attribute allows you to change the summary text. Available template tags to use are, "{rating}" which represents the calculated average rating, "{max}" which represents the maximum star rating available, and "%d" which represents the total number of reviews. However, rather than using this attribute to change the summary text, it's recommended to instead create a custom translation for it in the "Site Reviews -> Settings -> Translation page". That way, you will be able to customize both the singular (1 review) and plural (2 reviews) summary texts.</p>
	<p>The default summary text is: "{rating} out of {max} stars (based on %d reviews)".</p>

	<code>title="Overall Rating"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
</div>

<div class="glsr-card card">
	<h3>[site_reviews_form]</h3>
	<p>This shortcode displays the review submission form.</p>

	<code>assign_to="101"</code>
	<p>The "assign_to" attribute allows you to automatically assign submitted reviews to a post or page. Accepted values are either one or more post/page ID's (separated by commas), or "post_id" which will assign reviews to the ID of the current page.</p>

	<code>category="13,test"</code>
	<p>Include the "category" attribute to automatically assign one or more categories to the submitted review. Accepted values are either a category ID or slug.</p>

	<code>class="my-reviews-form"</code>
	<p>Include the "class" attribute to add custom CSS classes to the shortcode form.</p>

	<code>description="Required fields are marked &lt;span&gt;*&lt;/span&gt;"</code>
	<p>By default, the shortcode displays no description. Include the "description" attribute to display a custom shortcode description.</p>

	<code>hide=email,name,terms,title</code>
	<p>Add the "hide" attribute to exclude certain fields in the form. The rating and review fields cannot be excluded.</p>

	<code>id="type some random text here"</code>
	<p>This shortcode should only be used on a page once. However, if for any reason you need to include more than one on a page, add the "id" attribute to each with some random text to make it a unique shortcode form.</p>

	<code>title="Submit a Review"</code>
	<p>By default, the shortcode displays no heading. Include the "title" attribute to display a custom shortcode heading.</p>
</div>
