<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Contracts\FieldContract;
use GeminiLabs\SiteReviews\Modules\Html\SettingField;

/**
 * Test case for the Plugin.
 * @group plugin
 */
class SettingFieldTest extends \WP_UnitTestCase
{
    public function test_build_general_delete_data_on_uninstall(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.delete_data_on_uninstall'),
            '<tr class="glsr-setting-field" data-field="settings.general.delete_data_on_uninstall">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-delete_data_on_uninstall">Delete data on uninstall</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-general-delete_data_on_uninstall" data-glsr-track="" name="site_reviews_v7[settings][general][delete_data_on_uninstall]">'.
                        '<option value="">Do not delete anything</option>'.
                        '<option value="minimal">Delete all plugin settings, widgets settings, and caches</option>'.
                        '<option value="all">Delete everything (including all reviews and categories)</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_style(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.style'),
            '<tr class="glsr-setting-field" data-field="settings.general.style">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-style">Plugin Style</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-general-style" name="site_reviews_v7[settings][general][style]">'.
                        '<optgroup label="Styles">'.
                            '<option value="default">Site Reviews (default)</option>'.
                            '<option value="minimal">Site Reviews (minimal)</option>'.
                        '</optgroup>'.
                        '<optgroup label="Plugins">'.
                            '<option value="contact_form_7">Contact Form 7 (v5)</option>'.
                            '<option value="elementor">Elementor Pro (v3)</option>'.
                            '<option value="ninja_forms">Ninja Forms (v3)</option>'.
                            '<option value="wpforms">WPForms (v1)</option>'.
                        '</optgroup>'.
                        '<optgroup label="Themes">'.
                            '<option value="bootstrap">Bootstrap (v5)</option>'.
                            '<option value="divi">Divi (v4)</option>'.
                            '<option value="twentyfifteen">Twenty Fifteen</option>'.
                            '<option value="twentysixteen">Twenty Sixteen</option>'.
                            '<option value="twentyseventeen">Twenty Seventeen</option>'.
                            '<option value="twentynineteen">Twenty Nineteen</option>'.
                            '<option value="twentytwenty">Twenty Twenty</option>'.
                            '<option value="twentytwentyone">Twenty Twenty-One</option>'.
                            '<option value="twentytwentytwo">Twenty Twenty-Two</option>'.
                        '</optgroup>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_request_verification(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.request_verification'),
            '<tr class="glsr-setting-field" data-field="settings.general.request_verification">'.
                '<th scope="row">'.
                    '<label>Request Verification</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Request Verification</span>'.
                        '</legend>'.
                        '<div class="regular-text inline">'.
                            '<label for="site_reviews_v7-settings-general-request_verification-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-request_verification-1" name="site_reviews_v7[settings][general][request_verification]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-request_verification-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-request_verification-2" name="site_reviews_v7[settings][general][request_verification]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_request_verification_message(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.request_verification_message'),
            '<tr class="glsr-setting-field" data-field="settings.general.request_verification_message">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-request_verification_message">'.
                        'Verification Template'.
                    '</label>'.
                '</th>'.
                '<td>'.
                    '<div class="glsr-template-editor">'.
                        '<textarea '.
                            'class="large-text code" '.
                            'id="site_reviews_v7-settings-general-request_verification_message" '.
                            'name="site_reviews_v7[settings][general][request_verification_message]" '.
                            'rows="8"'.
                        '></textarea>'.
                        '<div class="quicktags-toolbar">'.
                            '<input type="button" class="button button-small" data-tag="review_assigned_links" value="assigned links" />'.
                            '<input type="button" class="button button-small" data-tag="review_assigned_posts" value="assigned posts" />'.
                            '<input type="button" class="button button-small" data-tag="review_assigned_users" value="assigned users" />'.
                            '<input type="button" class="button button-small" data-tag="review_author" value="name" />'.
                            '<input type="button" class="button button-small" data-tag="review_categories" value="categories" />'.
                            '<input type="button" class="button button-small" data-tag="review_content" value="content" />'.
                            '<input type="button" class="button button-small" data-tag="review_email" value="email" />'.
                            '<input type="button" class="button button-small" data-tag="review_id" value="review id" />'.
                            '<input type="button" class="button button-small" data-tag="review_ip" value="ip address" />'.
                            '<input type="button" class="button button-small" data-tag="review_rating" value="rating" />'.
                            '<input type="button" class="button button-small" data-tag="review_response" value="response" />'.
                            '<input type="button" class="button button-small" data-tag="review_stars" value="stars" />'.
                            '<input type="button" class="button button-small" data-tag="review_title" value="title" />'.
                            '<input type="button" class="button button-small" data-tag="site_title" value="site title" />'.
                            '<input type="button" class="button button-small" data-tag="site_url" value="site url" />'.
                            '<input type="button" class="button button-small" data-tag="verify_url" value="verify url" />'.
                        '</div>'.
                    '</div>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_require_approval(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.require.approval'),
            '<tr class="glsr-setting-field" data-field="settings.general.require.approval">'.
                '<th scope="row">'.
                    '<label>'.
                        'Require Approval'.
                    '</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Require Approval</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-general-require-approval-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-require-approval-1" name="site_reviews_v7[settings][general][require][approval]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-require-approval-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-require-approval-2" name="site_reviews_v7[settings][general][require][approval]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_require_approval_for(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.require.approval_for'),
            '<tr class="glsr-setting-field" data-field="settings.general.require.approval_for">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-require-approval_for">'.
                        'Require Approval For'.
                    '</label>'.
                '</th>'.
                '<td>'.
                    '<select '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-require-approval_for" '.
                        'name="site_reviews_v7[settings][general][require][approval_for]"'.
                    '>'.
                        '<option value="5">5 stars or less</option>'.
                        '<option value="4">4 stars or less</option>'.
                        '<option value="3">3 stars or less</option>'.
                        '<option value="2">2 stars or less</option>'.
                        '<option value="1">1 star or less</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_require_login(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.require.login'),
            '<tr class="glsr-setting-field" data-field="settings.general.require.login">'.
                '<th scope="row">'.
                    '<label>Require Login</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Require Login</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-general-require-login-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-require-login-1" name="site_reviews_v7[settings][general][require][login]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-require-login-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-require-login-2" name="site_reviews_v7[settings][general][require][login]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_require_login_url(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.require.login_url'),
            '<tr class="glsr-setting-field" data-field="settings.general.require.login_url">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-require-login_url">Custom Login URL</label>'.
                '</th>'.
                '<td>'.
                    '<input '.
                        'type="text" '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-require-login_url" '.
                        'name="site_reviews_v7[settings][general][require][login_url]" '.
                        'placeholder="Placeholder" '.
                        'value="" '.
                    '/>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_require_register(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.require.register'),
            '<tr class="glsr-setting-field" data-field="settings.general.require.register">'.
                '<th scope="row">'.
                    '<label>Show Registration Link</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Show Registration Link</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-general-require-register-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-require-register-1" name="site_reviews_v7[settings][general][require][register]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-require-register-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-general-require-register-2" name="site_reviews_v7[settings][general][require][register]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_require_register_url(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.require.register_url'),
            '<tr class="glsr-setting-field" data-field="settings.general.require.register_url">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-require-register_url">Custom Registration URL</label>'.
                '</th>'.
                '<td>'.
                    '<input '.
                        'type="text" '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-require-register_url" '.
                        'name="site_reviews_v7[settings][general][require][register_url]" '.
                        'placeholder="Placeholder" '.
                        'value="" '.
                    '/>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_multilingual(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.multilingual'),
            '<tr class="glsr-setting-field" data-field="settings.general.multilingual">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-multilingual">Multilingual</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-general-multilingual" name="site_reviews_v7[settings][general][multilingual]">'.
                        '<option value="">No Integration</option>'.
                        '<option value="polylang">Integrate with Polylang</option>'.
                        '<option value="wpml">Integrate with WPML</option>'.
                    '</select>'.
                    '<p class="description">Description</p>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_notifications(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.notifications'),
            '<tr class="glsr-setting-field" data-field="settings.general.notifications">'.
                '<th scope="row">'.
                    '<label>Notifications</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Notifications</span>'.
                        '</legend>'.
                        '<div>'.
                            '<label for="site_reviews_v7-settings-general-notifications-1">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-general-notifications-1" name="site_reviews_v7[settings][general][notifications][]" value="admin" /> Send to administrator <code>admin@example.org</code>'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-notifications-2">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-general-notifications-2" name="site_reviews_v7[settings][general][notifications][]" value="author" /> Send to author of the page that the review is assigned to'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-notifications-3">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-general-notifications-3" name="site_reviews_v7[settings][general][notifications][]" value="custom" /> Send to one or more email addresses'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-notifications-4">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-general-notifications-4" name="site_reviews_v7[settings][general][notifications][]" value="discord" /> Send to <a href="https://discord.com/" target="_blank">Discord</a> channel'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-general-notifications-5">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-general-notifications-5" name="site_reviews_v7[settings][general][notifications][]" value="slack" /> Send to <a href="https://slack.com/" target="_blank">Slack</a>'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_notification_discord(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.notification_discord'),
            '<tr class="glsr-setting-field" data-field="settings.general.notification_discord">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-notification_discord">'.
                        'Discord Webhook URL'.
                    '</label>'.
                '</th>'.
                '<td>'.
                    '<input '.
                        'type="text" '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-notification_discord" '.
                        'name="site_reviews_v7[settings][general][notification_discord]" '.
                        'value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_notification_slack(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.notification_slack'),
            '<tr class="glsr-setting-field" data-field="settings.general.notification_slack">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-notification_slack">'.
                        'Slack Webhook URL'.
                    '</label>'.
                '</th>'.
                '<td>'.
                    '<input '.
                        'type="text" '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-notification_slack" '.
                        'name="site_reviews_v7[settings][general][notification_slack]" '.
                        'value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_notification_from(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.notification_from'),
            '<tr class="glsr-setting-field" data-field="settings.general.notification_from">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-notification_from">Send Emails From</label>'.
                '</th>'.
                '<td>'.
                    '<input '.
                        'type="text" '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-notification_from" '.
                        'name="site_reviews_v7[settings][general][notification_from]" '.
                        'placeholder="Placeholder" '.
                        'value="" '.
                    '/>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_notification_email(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.notification_email'),
            '<tr class="glsr-setting-field" data-field="settings.general.notification_email">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-notification_email">Send Emails To</label>'.
                '</th>'.
                '<td>'.
                    '<input '.
                        'type="text" '.
                        'class="regular-text" '.
                        'id="site_reviews_v7-settings-general-notification_email" '.
                        'name="site_reviews_v7[settings][general][notification_email]" '.
                        'placeholder="Placeholder" '.
                        'value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_general_notification_message(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.general.notification_message'),
            '<tr class="glsr-setting-field" data-field="settings.general.notification_message">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-general-notification_message">'.
                        'Notification Template'.
                    '</label>'.
                '</th>'.
                '<td>'.
                    '<div class="glsr-template-editor">'.
                        '<textarea '.
                            'class="large-text '.
                            'code" '.
                            'id="site_reviews_v7-settings-general-notification_message" '.
                            'name="site_reviews_v7[settings][general][notification_message]" '.
                            'rows="10">'.
                        '</textarea>'.
                        '<div class="quicktags-toolbar">'.
                            '<input type="button" class="button button-small" data-tag="approve_url" value="approve url" />'.
                            '<input type="button" class="button button-small" data-tag="edit_url" value="edit url" />'.
                            '<input type="button" class="button button-small" data-tag="review_assigned_links" value="assigned links" />'.
                            '<input type="button" class="button button-small" data-tag="review_assigned_posts" value="assigned posts" />'.
                            '<input type="button" class="button button-small" data-tag="review_assigned_users" value="assigned users" />'.
                            '<input type="button" class="button button-small" data-tag="review_author" value="name" />'.
                            '<input type="button" class="button button-small" data-tag="review_categories" value="categories" />'.
                            '<input type="button" class="button button-small" data-tag="review_content" value="content" />'.
                            '<input type="button" class="button button-small" data-tag="review_email" value="email" />'.
                            '<input type="button" class="button button-small" data-tag="review_id" value="review id" />'.
                            '<input type="button" class="button button-small" data-tag="review_ip" value="ip address" />'.
                            '<input type="button" class="button button-small" data-tag="review_rating" value="rating" />'.
                            '<input type="button" class="button button-small" data-tag="review_response" value="response" />'.
                            '<input type="button" class="button button-small" data-tag="review_stars" value="stars" />'.
                            '<input type="button" class="button button-small" data-tag="review_title" value="title" />'.
                            '<input type="button" class="button button-small" data-tag="site_title" value="site title" />'.
                            '<input type="button" class="button button-small" data-tag="site_url" value="site url" />'.
                        '</div>'.
                    '</div>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_date_format(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.date.format'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.date.format">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-date-format">Date Format</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-reviews-date-format" name="site_reviews_v7[settings][reviews][date][format]">'.
                        '<option value="">Use the default date format</option>'.
                        '<option value="relative">Use a relative date format</option>'.
                        '<option value="custom">Use a custom date format</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_date_custom(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.date.custom'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.date.custom">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-date-custom">Custom Date Format</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-reviews-date-custom" name="site_reviews_v7[settings][reviews][date][custom]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_name_format(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.name.format'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.name.format">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-name-format">Name Format</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-reviews-name-format" name="site_reviews_v7[settings][reviews][name][format]">'.
                        '<option value="">Use the name as given</option>'.
                        '<option value="first">Use the first name only</option>'.
                        '<option value="first_initial">Convert first name to an initial</option>'.
                        '<option value="last_initial">Convert last name to an initial</option>'.
                        '<option value="initials">Convert to all initials</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_name_initial(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.name.initial'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.name.initial">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-name-initial">Initial Format</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-reviews-name-initial" name="site_reviews_v7[settings][reviews][name][initial]">'.
                        '<option value="">Initial with a space</option>'.
                        '<option value="period">Initial with a period</option>'.
                        '<option value="period_space">Initial with a period and a space</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_assignment(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.assignment'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.assignment">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-assignment">Review Assignment</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-reviews-assignment" name="site_reviews_v7[settings][reviews][assignment]">'.
                        '<option value="loose">Loose Assignment (slower database queries)</option>'.
                        '<option value="strict">Strict Assignment (faster database queries)</option>'.
                    '</select>'.
                    '<p class="description">Description</p>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_assigned_links(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.assigned_links'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.assigned_links">'.
                '<th scope="row">'.
                    '<label>Enable Assigned Links</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Enable Assigned Links</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-reviews-assigned_links-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-assigned_links-1" name="site_reviews_v7[settings][reviews][assigned_links]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-reviews-assigned_links-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-assigned_links-2" name="site_reviews_v7[settings][reviews][assigned_links]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_avatars(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.avatars'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.avatars">'.
                '<th scope="row">'.
                    '<label>Enable Avatars</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Enable Avatars</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-reviews-avatars-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-avatars-1" name="site_reviews_v7[settings][reviews][avatars]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-reviews-avatars-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-avatars-2" name="site_reviews_v7[settings][reviews][avatars]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_avatars_fallback(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.avatars_fallback'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.avatars_fallback">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-avatars_fallback">Fallback Avatar</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-reviews-avatars_fallback" name="site_reviews_v7[settings][reviews][avatars_fallback]">'.
                        '<option value="custom">Custom Image URL</option>'.
                        '<option value="identicon">Identicon (geometric patterns)</option>'.
                        '<option value="initials">Initials (initials of reviewer\'s name)</option>'.
                        '<option value="monsterid">Monster (monsters with generated faces)</option>'.
                        '<option value="mystery">Mystery (silhouetted outline of a person)</option>'.
                        '<option value="none">None (select this if you want an avatar plugin to manage the fallback avatar)</option>'.
                        '<option value="pixels">Pixel Avatars (locally generated)</option>'.
                        '<option value="retro">Retro (8-bit arcade-style pixelated faces)</option>'.
                        '<option value="robohash">Robohash (robots with generated faces)</option>'.
                        '<option value="wavatar">Wavatar (faces with generated features)</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_avatars_fallback_url(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.avatars_fallback_url'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.avatars_fallback_url">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-avatars_fallback_url">Fallback Avatar URL</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-reviews-avatars_fallback_url" name="site_reviews_v7[settings][reviews][avatars_fallback_url]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_avatars_regenerate(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.avatars_regenerate'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.avatars_regenerate">'.
                '<th scope="row">'.
                    '<label>Regenerate Avatars</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Regenerate Avatars</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-reviews-avatars_regenerate-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-avatars_regenerate-1" name="site_reviews_v7[settings][reviews][avatars_regenerate]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-reviews-avatars_regenerate-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-avatars_regenerate-2" name="site_reviews_v7[settings][reviews][avatars_regenerate]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_avatars_size(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.avatars_size'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.avatars_size">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-avatars_size">Avatar Size</label>'.
                '</th>'.
                '<td>'.
                    '<input type="number" class="small-text" id="site_reviews_v7-settings-reviews-avatars_size" name="site_reviews_v7[settings][reviews][avatars_size]" min="16" value="" /> pixels'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_excerpts(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.excerpts'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.excerpts">'.
                '<th scope="row">'.
                    '<label>Enable Excerpts</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Enable Excerpts</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-reviews-excerpts-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-excerpts-1" name="site_reviews_v7[settings][reviews][excerpts]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-reviews-excerpts-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-excerpts-2" name="site_reviews_v7[settings][reviews][excerpts]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_excerpts_action(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.excerpts_action'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.excerpts_action">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-excerpts_action">Excerpt Action</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-reviews-excerpts_action" name="site_reviews_v7[settings][reviews][excerpts_action]">'.
                        '<option value="">Collapse/Expand the review</option>'.
                        '<option value="modal">Display the review in a modal</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_excerpts_length(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.excerpts_length'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.excerpts_length">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-reviews-excerpts_length">Excerpt Length</label>'.
                '</th>'.
                '<td>'.
                    '<input type="number" class="small-text" id="site_reviews_v7-settings-reviews-excerpts_length" name="site_reviews_v7[settings][reviews][excerpts_length]" value="" /> words'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_fallback(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.fallback'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.fallback">'.
                '<th scope="row">'.
                    '<label>Enable Fallback Text</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Enable Fallback Text</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-reviews-fallback-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-fallback-1" name="site_reviews_v7[settings][reviews][fallback]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-reviews-fallback-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-fallback-2" name="site_reviews_v7[settings][reviews][fallback]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                        '<p class="description">Description</p>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_reviews_pagination_url_parameter(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.reviews.pagination.url_parameter'),
            '<tr class="glsr-setting-field" data-field="settings.reviews.pagination.url_parameter">'.
                '<th scope="row">'.
                    '<label>Enable Paginated URLs</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Enable Paginated URLs</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-reviews-pagination-url_parameter-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-pagination-url_parameter-1" name="site_reviews_v7[settings][reviews][pagination][url_parameter]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-reviews-pagination-url_parameter-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-reviews-pagination-url_parameter-2" name="site_reviews_v7[settings][reviews][pagination][url_parameter]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                        '<p class="description">Description</p>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_integration_plugin(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.integration.plugin'),
            '<tr class="glsr-setting-field" data-field="settings.schema.integration.plugin">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-integration-plugin">Integrate with plugin</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-integration-plugin" name="site_reviews_v7[settings][schema][integration][plugin]">'.
                        '<option value="">No Integration</option>'.
                        '<option value="rankmath">RankMath Pro</option>'.
                        '<option value="saswp">Schema & Structured Data for WP & AMP</option>'.
                        '<option value="schema_pro">Schema Pro</option>'.
                        '<option value="seopress">SEOPress</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_type_default(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.type.default'),
            '<tr class="glsr-setting-field" data-field="settings.schema.type.default">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-type-default">Default Schema Type</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-type-default" name="site_reviews_v7[settings][schema][type][default]">'.
                        '<option value="LocalBusiness">Local Business</option>'.
                        '<option value="Product">Product</option>'.
                        '<option value="custom">Custom</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_type_custom(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.type.custom'),
            '<tr class="glsr-setting-field" data-field="settings.schema.type.custom">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-type-custom">Custom Schema Type</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-type-custom" name="site_reviews_v7[settings][schema][type][custom]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_name_default(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.name.default'),
            '<tr class="glsr-setting-field" data-field="settings.schema.name.default">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-name-default">Default Name</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-name-default" name="site_reviews_v7[settings][schema][name][default]">'.
                        '<option value="post">Use the assigned or current page title</option>'.
                        '<option value="custom">Enter a custom title</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_name_custom(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.name.custom'),
            '<tr class="glsr-setting-field" data-field="settings.schema.name.custom">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-name-custom">Custom Name</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-name-custom" name="site_reviews_v7[settings][schema][name][custom]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_description_default(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.description.default'),
            '<tr class="glsr-setting-field" data-field="settings.schema.description.default">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-description-default">Default Description</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-description-default" name="site_reviews_v7[settings][schema][description][default]">'.
                        '<option value="post">Use the assigned or current page excerpt</option>'.
                        '<option value="custom">Enter a custom description</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_description_custom(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.description.custom'),
            '<tr class="glsr-setting-field" data-field="settings.schema.description.custom">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-description-custom">Custom Description</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-description-custom" name="site_reviews_v7[settings][schema][description][custom]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_url_default(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.url.default'),
            '<tr class="glsr-setting-field" data-field="settings.schema.url.default">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-url-default">Default URL</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-url-default" name="site_reviews_v7[settings][schema][url][default]">'.
                        '<option value="post">Use the assigned or current page URL</option>'.
                        '<option value="custom">Enter a custom URL</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_url_custom(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.url.custom'),
            '<tr class="glsr-setting-field" data-field="settings.schema.url.custom">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-url-custom">Custom URL</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-url-custom" name="site_reviews_v7[settings][schema][url][custom]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_image_default(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.image.default'),
            '<tr class="glsr-setting-field" data-field="settings.schema.image.default">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-image-default">Default Image</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-image-default" name="site_reviews_v7[settings][schema][image][default]">'.
                        '<option value="post">Use the featured image of the assigned or current page</option>'.
                        '<option value="custom">Enter a custom image URL</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_image_custom(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.image.custom'),
            '<tr class="glsr-setting-field" data-field="settings.schema.image.custom">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-image-custom">Custom Image URL</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-image-custom" name="site_reviews_v7[settings][schema][image][custom]" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_address(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.address'),
            '<tr class="glsr-setting-field" data-field="settings.schema.address">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-address">Address</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-address" name="site_reviews_v7[settings][schema][address]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_telephone(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.telephone'),
            '<tr class="glsr-setting-field" data-field="settings.schema.telephone">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-telephone">Telephone Number</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-telephone" name="site_reviews_v7[settings][schema][telephone]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_pricerange(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.pricerange'),
            '<tr class="glsr-setting-field" data-field="settings.schema.pricerange">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-pricerange">Price Range</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-pricerange" name="site_reviews_v7[settings][schema][pricerange]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_offertype(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.offertype'),
            '<tr class="glsr-setting-field" data-field="settings.schema.offertype">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-offertype">Offer Type</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-schema-offertype" name="site_reviews_v7[settings][schema][offertype]">'.
                        '<option value="AggregateOffer">AggregateOffer</option>'.
                        '<option value="Offer">Offer</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_price(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.price'),
            '<tr class="glsr-setting-field" data-field="settings.schema.price">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-price">Price</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-price" name="site_reviews_v7[settings][schema][price]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_lowprice(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.lowprice'),
            '<tr class="glsr-setting-field" data-field="settings.schema.lowprice">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-lowprice">Low Price</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-lowprice" name="site_reviews_v7[settings][schema][lowprice]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_highprice(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.highprice'),
            '<tr class="glsr-setting-field" data-field="settings.schema.highprice">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-highprice">High Price</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-highprice" name="site_reviews_v7[settings][schema][highprice]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_schema_pricecurrency(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.schema.pricecurrency'),
            '<tr class="glsr-setting-field" data-field="settings.schema.pricecurrency">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-schema-pricecurrency">Price Currency</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-schema-pricecurrency" name="site_reviews_v7[settings][schema][pricecurrency]" placeholder="Placeholder" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_required(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.required'),
            '<tr class="glsr-setting-field" data-field="settings.forms.required">'.
                '<th scope="row">'.
                    '<label>Required Fields</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Required Fields</span>'.
                        '</legend>'.
                        '<div>'.
                            '<label for="site_reviews_v7-settings-forms-required-1">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-required-1" name="site_reviews_v7[settings][forms][required][]" value="rating" /> Rating'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-required-2">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-required-2" name="site_reviews_v7[settings][forms][required][]" value="title" /> Title'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-required-3">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-required-3" name="site_reviews_v7[settings][forms][required][]" value="content" /> Review'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-required-4">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-required-4" name="site_reviews_v7[settings][forms][required][]" value="name" /> Name'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-required-5">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-required-5" name="site_reviews_v7[settings][forms][required][]" value="email" /> Email'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-required-6">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-required-6" name="site_reviews_v7[settings][forms][required][]" value="terms" /> Terms'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_limit(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.limit'),
            '<tr class="glsr-setting-field" data-field="settings.forms.limit">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-limit">Limit Reviews</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-limit" name="site_reviews_v7[settings][forms][limit]">'.
                        '<option value="">No Limit</option>'.
                        '<option value="email">By Email Address</option>'.
                        '<option value="ip_address">By IP Address</option>'.
                        '<option value="username">By Username (will only work for registered users)</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_limit_time(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.limit_time'),
            '<tr class="glsr-setting-field" data-field="settings.forms.limit_time">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-limit_time">Limit Reviews For</label>'.
                '</th>'.
                '<td>'.
                    '<input type="number" class="small-text" id="site_reviews_v7-settings-forms-limit_time" name="site_reviews_v7[settings][forms][limit_time]" min="0" value="" /> days'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_limit_assignments(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.limit_assignments'),
            '<tr class="glsr-setting-field" data-field="settings.forms.limit_assignments">'.
                '<th scope="row">'.
                    '<label>Restrict Limits To</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Restrict Limits To</span>'.
                        '</legend>'.
                        '<div>'.
                            '<label for="site_reviews_v7-settings-forms-limit_assignments-1">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-limit_assignments-1" name="site_reviews_v7[settings][forms][limit_assignments][]" value="assigned_posts" /> Assigned Posts'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-limit_assignments-2">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-limit_assignments-2" name="site_reviews_v7[settings][forms][limit_assignments][]" value="assigned_terms" /> Assigned Terms'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-limit_assignments-3">'.
                                '<input type="checkbox" id="site_reviews_v7-settings-forms-limit_assignments-3" name="site_reviews_v7[settings][forms][limit_assignments][]" value="assigned_users" /> Assigned Users'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_limit_whitelist_email(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.limit_whitelist.email'),
            '<tr class="glsr-setting-field" data-field="settings.forms.limit_whitelist.email">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-limit_whitelist-email">Email Whitelist</label>'.
                '</th>'.
                '<td>'.
                    '<textarea class="large-text code" id="site_reviews_v7-settings-forms-limit_whitelist-email" name="site_reviews_v7[settings][forms][limit_whitelist][email]" rows="5"></textarea>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_limit_whitelist_ip_address(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.limit_whitelist.ip_address'),
            '<tr class="glsr-setting-field" data-field="settings.forms.limit_whitelist.ip_address">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-limit_whitelist-ip_address">IP Address Whitelist</label>'.
                '</th>'.
                '<td>'.
                    '<textarea class="large-text code" id="site_reviews_v7-settings-forms-limit_whitelist-ip_address" name="site_reviews_v7[settings][forms][limit_whitelist][ip_address]" rows="5"></textarea>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_limit_whitelist_username(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.limit_whitelist.username'),
            '<tr class="glsr-setting-field" data-field="settings.forms.limit_whitelist.username">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-limit_whitelist-username">Username Whitelist</label>'.
                '</th>'.
                '<td>'.
                    '<textarea class="large-text code" id="site_reviews_v7-settings-forms-limit_whitelist-username" name="site_reviews_v7[settings][forms][limit_whitelist][username]" rows="5"></textarea>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_captcha_integration(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.captcha.integration'),
            '<tr class="glsr-setting-field" data-field="settings.forms.captcha.integration">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-captcha-integration">CAPTCHA</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-captcha-integration" name="site_reviews_v7[settings][forms][captcha][integration]">'.
                        '<option value="">Do not use</option>'.
                        '<option value="turnstile">Use Cloudflare Turnstile</option>'.
                        '<option value="friendlycaptcha">Use Friendly Captcha</option>'.
                        '<option value="hcaptcha">Use hCaptcha</option>'.
                        '<option value="recaptcha_v2_invisible">Use reCAPTCHA v2 Invisible</option>'.
                        '<option value="recaptcha_v3">Use reCAPTCHA v3</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_friendlycaptcha_key(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.friendlycaptcha.key'),
            '<tr class="glsr-setting-field" data-field="settings.forms.friendlycaptcha.key">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-friendlycaptcha-key">Site Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-friendlycaptcha-key" name="site_reviews_v7[settings][forms][friendlycaptcha][key]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_friendlycaptcha_secret(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.friendlycaptcha.secret'),
            '<tr class="glsr-setting-field" data-field="settings.forms.friendlycaptcha.secret">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-friendlycaptcha-secret">API Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-friendlycaptcha-secret" name="site_reviews_v7[settings][forms][friendlycaptcha][secret]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_hcaptcha_key(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.hcaptcha.key'),
            '<tr class="glsr-setting-field" data-field="settings.forms.hcaptcha.key">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-hcaptcha-key">Site Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-hcaptcha-key" name="site_reviews_v7[settings][forms][hcaptcha][key]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_hcaptcha_secret(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.hcaptcha.secret'),
            '<tr class="glsr-setting-field" data-field="settings.forms.hcaptcha.secret">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-hcaptcha-secret">Secret Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-hcaptcha-secret" name="site_reviews_v7[settings][forms][hcaptcha][secret]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_recaptcha_key(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.recaptcha.key'),
            '<tr class="glsr-setting-field" data-field="settings.forms.recaptcha.key">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-recaptcha-key">Site Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-recaptcha-key" name="site_reviews_v7[settings][forms][recaptcha][key]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_recaptcha_secret(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.recaptcha.secret'),
            '<tr class="glsr-setting-field" data-field="settings.forms.recaptcha.secret">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-recaptcha-secret">Secret Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-recaptcha-secret" name="site_reviews_v7[settings][forms][recaptcha][secret]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_recaptcha_v3_key(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.recaptcha_v3.key'),
            '<tr class="glsr-setting-field" data-field="settings.forms.recaptcha_v3.key">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-recaptcha_v3-key">Site Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-recaptcha_v3-key" name="site_reviews_v7[settings][forms][recaptcha_v3][key]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_recaptcha_v3_secret(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.recaptcha_v3.secret'),
            '<tr class="glsr-setting-field" data-field="settings.forms.recaptcha_v3.secret">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-recaptcha_v3-secret">Secret Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-recaptcha_v3-secret" name="site_reviews_v7[settings][forms][recaptcha_v3][secret]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_recaptcha_v3_threshold(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.recaptcha_v3.threshold'),
            '<tr class="glsr-setting-field" data-field="settings.forms.recaptcha_v3.threshold">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-recaptcha_v3-threshold">Score Threshold</label>'.
                '</th>'.
                '<td>'.
                    '<input type="number" class="small-text" id="site_reviews_v7-settings-forms-recaptcha_v3-threshold" name="site_reviews_v7[settings][forms][recaptcha_v3][threshold]" min="0" max="1" step="0.1" value="" />'.
                    '<p class="description">Description</p>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_turnstile_key(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.turnstile.key'),
            '<tr class="glsr-setting-field" data-field="settings.forms.turnstile.key">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-turnstile-key">Site Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-turnstile-key" name="site_reviews_v7[settings][forms][turnstile][key]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_turnstile_secret(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.turnstile.secret'),
            '<tr class="glsr-setting-field" data-field="settings.forms.turnstile.secret">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-turnstile-secret">Secret Key</label>'.
                '</th>'.
                '<td>'.
                    '<input type="text" class="regular-text" id="site_reviews_v7-settings-forms-turnstile-secret" name="site_reviews_v7[settings][forms][turnstile][secret]" autocomplete="off" value="" />'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_captcha_position(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.captcha.position'),
            '<tr class="glsr-setting-field" data-field="settings.forms.captcha.position">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-captcha-position">CAPTCHA Badge</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-captcha-position" name="site_reviews_v7[settings][forms][captcha][position]">'.
                        '<option value="bottomleft">Bottom Left</option>'.
                        '<option value="bottomright">Bottom Right</option>'.
                        '<option value="inline">Inline</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_captcha_theme(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.captcha.theme'),
            '<tr class="glsr-setting-field" data-field="settings.forms.captcha.theme">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-captcha-theme">CAPTCHA Theme</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-captcha-theme" name="site_reviews_v7[settings][forms][captcha][theme]">'.
                        '<option value="light">Light</option>'.
                        '<option value="dark">Dark</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_captcha_usage(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.captcha.usage'),
            '<tr class="glsr-setting-field" data-field="settings.forms.captcha.usage">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-captcha-usage">CAPTCHA Usage</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-captcha-usage" name="site_reviews_v7[settings][forms][captcha][usage]">'.
                        '<option value="all">Use for everyone</option>'.
                        '<option value="guest">Use only for guest users</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_akismet(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.akismet'),
            '<tr class="glsr-setting-field" data-field="settings.forms.akismet">'.
                '<th scope="row">'.
                    '<label>Enable Akismet</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Enable Akismet</span>'.
                        '</legend>'.
                        '<div class="inline">'.
                            '<label for="site_reviews_v7-settings-forms-akismet-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-forms-akismet-1" name="site_reviews_v7[settings][forms][akismet]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-akismet-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-forms-akismet-2" name="site_reviews_v7[settings][forms][akismet]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_prevent_duplicates(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.prevent_duplicates'),
            '<tr class="glsr-setting-field" data-field="settings.forms.prevent_duplicates">'.
                '<th scope="row">'.
                    '<label>Prevent Duplicates</label>'.
                '</th>'.
                '<td>'.
                    '<fieldset data-depends="">'.
                        '<legend class="screen-reader-text">'.
                            '<span>Prevent Duplicates</span>'.
                        '</legend>'.
                        '<div class="regular-text inline">'.
                            '<label for="site_reviews_v7-settings-forms-prevent_duplicates-1">'.
                                '<input type="radio" id="site_reviews_v7-settings-forms-prevent_duplicates-1" name="site_reviews_v7[settings][forms][prevent_duplicates]" value="no" /> No'.
                            '</label>'.
                            '<br>'.
                            '<label for="site_reviews_v7-settings-forms-prevent_duplicates-2">'.
                                '<input type="radio" id="site_reviews_v7-settings-forms-prevent_duplicates-2" name="site_reviews_v7[settings][forms][prevent_duplicates]" value="yes" /> Yes'.
                            '</label>'.
                        '</div>'.
                    '</fieldset>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_blacklist_integration(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.blacklist.integration'),
            '<tr class="glsr-setting-field" data-field="settings.forms.blacklist.integration">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-blacklist-integration">Blacklist</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-blacklist-integration" name="site_reviews_v7[settings][forms][blacklist][integration]">'.
                        '<option value="">Use the Site Reviews Blacklist</option>'.
                        '<option value="comments">Use the WordPress Disallowed Comment Keys</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_blacklist_entries(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.blacklist.entries'),
            '<tr class="glsr-setting-field" data-field="settings.forms.blacklist.entries">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-blacklist-entries">Review Blacklist</label>'.
                '</th>'.
                '<td>'.
                    '<textarea class="large-text code" id="site_reviews_v7-settings-forms-blacklist-entries" name="site_reviews_v7[settings][forms][blacklist][entries]" rows="10"></textarea>'.
                '</td>'.
            '</tr>'
        );
    }

    public function test_build_forms_blacklist_action(): void
    {
        $this->assertEquals(
            $this->buildSetting('settings.forms.blacklist.action'),
            '<tr class="glsr-setting-field" data-field="settings.forms.blacklist.action">'.
                '<th scope="row">'.
                    '<label for="site_reviews_v7-settings-forms-blacklist-action">Blacklist Action</label>'.
                '</th>'.
                '<td>'.
                    '<select class="regular-text" id="site_reviews_v7-settings-forms-blacklist-action" name="site_reviews_v7[settings][forms][blacklist][action]">'.
                        '<option value="unapprove">Require approval</option>'.
                        '<option value="reject">Reject submission</option>'.
                    '</select>'.
                '</td>'.
            '</tr>'
        );
    }

    protected function build(array $args = []): string
    {
        $html = $this->field($args)->build();
        $html = html_entity_decode($html, ENT_COMPAT); // decode double quotes in data-depends attributes
        $parts = preg_split('/\R/', $html);
        $parts = array_map('trim', $parts);
        return implode('', $parts);
    }

    protected function buildSetting(string $name): string
    {
        $args = glsr()->settings()[$name];
        $args = wp_parse_args($args, compact('name'));
        return $this->build($args);
    }

    protected function field(array $args = []): FieldContract
    {
        $field = new SettingField($args);
        if ($field->description) {
            $field->description = 'Description';
        }
        if ($field->placeholder) {
            $field->placeholder = 'Placeholder';
        }
        $field->tooltip = ''; // remove the tooltip as it's unecessary to test it.
        return $field;
    }
}
