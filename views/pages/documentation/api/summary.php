<?php defined('ABSPATH') || exit;

$schema = (new \GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\SummarySchema())->schema();

?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="api-summary">
            <span class="title">Rating Summary</span>
            <span class="badge code">/site-reviews/v1/summary</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="api-summary" class="inside">
        <h3>Schema</h3>
        <p>The schema defines all the fields that exist within the response. The response can be expected to contain the fields below unless one of the following conditions is true:</p>
        <ol>
            <li>The <code>_fields</code> query parameter is used.</li>
            <li>The <code>_rendered</code> query parameter is used.</li>
        </ol>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Field</strong></th>
                        <th scope="col"><strong>Type</strong></th>
                        <th scope="col"><strong>Context</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($schema['properties'] as $field => $values) { ?>
                    <tr>
                        <td><strong><?php echo esc_html($field); ?></strong></td>
                        <td><?php echo esc_html($values['type'] ?? ''); ?></td>
                        <td><?php echo esc_html(implode(', ', ($values['context'] ?? []))); ?></td>
                        <td><?php echo esc_html($values['description'] ?? ''); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <h3>Global Parameters</h3>
        <p>To instruct Site Reviews to return only a subset of the fields in a response, you may use the <code>_fields</code> query parameter. If for example you only need the average and ranking of the summary, you can restrict the response to only those properties with this fields query:</p>
        <pre><code class="language-bash">/site-reviews/v1/summary?_fields=average,ranking</code></pre>
        <p>To instruct Site Reviews to return the rendered HTML of the summary in the response instead of the summary values, you may use the <code>_rendered</code> query parameter. For example:</p>
        <pre><code class="language-bash">/site-reviews/v1/summary?_rendered=1</code></pre>
        <p>To instruct Site Reviews to hide specific fields when rendering the summary, you may use the <code>_hide</code> query parameter. Allowed values are the same as the [site_reviews_summary] shortcode hide options. For example:</p>
        <pre><code class="language-bash">/site-reviews/v1/summary?_rendered=1&_hide=rating,bars</code></pre>

        <h3>List The Summary</h3>
        <p>Query this endpoint to retrieve the rating summary. The response you receive can be controlled and filtered using the URL query parameters below.</p>
        <h4>Arguments</h4>
        <div class="glsr-responsive-table">
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th scope="col"><strong>Parameter</strong></th>
                        <th scope="col"><strong>Default</strong></th>
                        <th scope="col"><strong>Description</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>after</strong></td>
                        <td></td>
                        <td>Limit summary to reviews published after a given ISO8601 compliant date.</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_posts</strong></td>
                        <td></td>
                        <td>Limit summary to reviews assigned to specific posts of any public post type (IDs or slugs in the format of <code>post_type:slug</code>).</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_terms</strong></td>
                        <td></td>
                        <td>Limit summary to reviews assigned to specific terms in the <code>site-review-category</code> taxonomy (IDs or slugs).</td>
                    </tr>
                    <tr>
                        <td><strong>assigned_users</strong></td>
                        <td></td>
                        <td>Limit summary to reviews assigned to specific users (IDs or usernames).</td>
                    </tr>
                    <tr>
                        <td><strong>before</strong></td>
                        <td></td>
                        <td>Limit summary to reviews published before a given ISO8601 compliant date.</td>
                    </tr>
                    <tr>
                        <td><strong>date</strong></td>
                        <td></td>
                        <td>Limit summary to reviews published on a given ISO8601 compliant date.</td>
                    </tr>
                    <tr>
                        <td><strong>email</strong></td>
                        <td></td>
                        <td>Limit summary to reviews containing a given email address.</td>
                    </tr>
                    <tr>
                        <td><strong>exclude</strong></td>
                        <td></td>
                        <td>Ensure summary excludes specific review IDs.</td>
                    </tr>
                    <tr>
                        <td><strong>include</strong></td>
                        <td></td>
                        <td>Limit summary to specific review IDs.</td>
                    </tr>
                    <tr>
                        <td><strong>ip_address</strong></td>
                        <td></td>
                        <td>Limit summary to reviews submitted from a given IP address.</td>
                    </tr>
                    <tr>
                        <td><strong>rating</strong></td>
                        <td></td>
                        <td>Limit summary to reviews containing a given <em>minimum</em> rating.</td>
                    </tr>
                    <tr>
                        <td><strong>rating_field</strong></td>
                        <td></td>
                        <td>
                            Use rating values of a custom rating field; use the custom rating Field Name as the value.
                            <div class="glsr-notice-inline components-notice is-warning"><?php echo glsr_premium_link('site-reviews-forms'); ?> addon required.</div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>status</strong></td>
                        <td>approved</td>
                        <td>Limit summary to reviews containing a given status. One of: <code>all</code>, <code>approved</code>, <code>unapproved</code></td>
                    </tr>
                    <tr>
                        <td><strong>terms</strong></td>
                        <td></td>
                        <td>Limit summary to reviews submitted with terms accepted. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                    <tr>
                        <td><strong>theme</strong></td>
                        <td></td>
                        <td>
                            Render the summary rating using a specific custom review theme (ID); only works with the <code>rendered</code> parameter.
                            <div class="glsr-notice-inline components-notice is-warning"><?php echo glsr_premium_link('site-reviews-themes'); ?> addon required.</div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>type</strong></td>
                        <td></td>
                        <td>Limit summary to reviews containing a given review type.</td>
                    </tr>
                    <tr>
                        <td><strong>user__in</strong></td>
                        <td></td>
                        <td>Limit summary to reviews authored by specific users (IDs or usernames).</td>
                    </tr>
                    <tr>
                        <td><strong>user__not_in</strong></td>
                        <td></td>
                        <td>Ensure summary excludes reviews authored by specific users (IDs or usernames).</td>
                    </tr>
                    <tr>
                        <td><strong>verified</strong></td>
                        <td></td>
                        <td>Limit summary to reviews that are either verified or not verified. One of: <code>0</code>, <code>1</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h4>Definition</h4>
        <pre><code class="language-bash">GET /site-reviews/v1/summary</code></pre>
        <h4>Example Request</h4>
        <pre><code class="language-bash">curl --user "USERNAME:PASSWORD" https://example.com/wp-json/site-reviews/v1/summary</code></pre>
    </div>
</div>
