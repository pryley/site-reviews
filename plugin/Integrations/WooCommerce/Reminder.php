<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce;

class Reminder extends \WC_Email
{
    const ID = 'WC_Email_Customer_Review_Reminder';

    public function __construct()
    {
        $this->customer_email = true;
        $this->description = '';
        $this->id = glsr()->prefix.'reminder';
        $this->placeholders = [
            '{order_date}' => '',
            '{order_number}' => '',
        ];
        $this->template_base = glsr()->path().'templates/woocommerce/';
        $this->template_html = 'emails/review-reminder.php';
        $this->template_plain = 'emails/plain/review-reminder.php';
        $this->title = _x('Site Reviews: Review Reminder', 'admin-text', 'site-reviews');
        parent::__construct();
        // this goes last
        $this->enabled = 'yes';
        $this->manual = false;
        // Triggers for this email.
        add_action('site-reviews/woocommerce/reminder/trigger', [$this, 'trigger']);
    }

    /**
     * @return string
     */
    public function get_content_html()
    {
        return wc_get_template_html($this->template_html, [
            'additional_content' => $this->get_additional_content(),
            'email' => $this,
            'email_heading' => $this->get_heading(),
            'order' => $this->object,
            'plain_text' => false,
            'sent_to_admin' => false,
        ]);
    }

    /**
     * @return string
     */
    public function get_content_plain()
    {
        return wc_get_template_html($this->template_plain, [
            'additional_content' => $this->get_additional_content(),
            'email' => $this,
            'email_heading' => $this->get_heading(),
            'order' => $this->object,
            'plain_text' => true,
            'sent_to_admin' => false,
        ]);
    }

    /**
     * @return string
     */
    public function get_default_additional_content()
    {
        return __('We would love if you could help us and other customers by reviewing products that you recently purchased in order #{order_id}. It only takes a minute and it would really help others. Click the button below and leave your review!', 'site-reviews');
    }

    /**
     * @return string
     */
    public function get_default_heading()
    {
        return __('Thanks for shopping with us!', 'site-reviews');
    }

    /**
     * @return string
     */
    public function get_default_subject()
    {
        return __('{site_title}: Review Your Experience with Us', 'site-reviews');
    }

    /**
     * @param int $orderId.
     * @return void
     */
    public function trigger($orderId)
    {
        $this->setup_locale();
        $order = wc_get_order($orderId);
        if (is_a($order, 'WC_Order')) {
            $this->object = $order;
            $this->recipient = $this->object->get_billing_email();
            $this->placeholders['{order_date}'] = wc_format_datetime($this->object->get_date_created());
            $this->placeholders['{order_number}'] = $this->object->get_order_number();
        }
        if ($this->is_enabled() && $this->get_recipient()) {
            $this->send(
                $this->get_recipient(),
                $this->get_subject(),
                $this->get_content(),
                $this->get_headers(),
                $this->get_attachments()
            );
        }
        $this->restore_locale();
    }
}
