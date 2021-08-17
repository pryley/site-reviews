<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\EmailDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Email
{
    /**
     * @var array
     */
    public $attachments;

    /**
     * @var array
     */
    public $data;

    /**
     * @var array
     */
    public $email;

    /**
     * @var array
     */
    public $headers;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string|array
     */
    public $to;

    /**
     * @return Email
     */
    public function compose(array $email, array $data = [])
    {
        $this->data = $data;
        $this->normalize($email);
        $this->attachments = $this->email['attachments'];
        $this->headers = $this->buildHeaders();
        $this->message = $this->buildHtmlMessage();
        $this->subject = $this->email['subject'];
        $this->to = $this->email['to'];
        add_action('phpmailer_init', [$this, 'buildPlainTextMessage']);
        return $this;
    }

    /**
     * @param \WP_Error $error
     * @return void
     */
    public function logMailError($error)
    {
        glsr_log()->error('Email was not sent (wp_mail failed)')
            ->debug($this)
            ->debug($error);
    }

    /**
     * @param string $format
     * @return string
     */
    public function read($format = '')
    {
        if ('plaintext' == $format) {
            $message = $this->stripHtmlTags($this->message);
            return glsr()->filterString('email/message', $message, 'text', $this);
        }
        return $this->message;
    }

    /**
     * @return void|bool
     */
    public function send()
    {
        if (!$this->message || !$this->subject || !$this->to) {
            glsr_log()->warning('The email was not sent because it is missing either the email address, subject, or message.');
            return;
        }
        add_action('wp_mail_failed', [$this, 'logMailError']);
        $sent = wp_mail(
            $this->to,
            $this->subject,
            $this->message,
            $this->headers,
            $this->attachments
        );
        remove_action('wp_mail_failed', [$this, 'logMailError']);
        $this->reset();
        return $sent;
    }

    /**
     * @return void
     * @action phpmailer_init
     */
    public function buildPlainTextMessage($phpmailer)
    {
        if (empty($this->email)) {
            return;
        }
        if ('text/plain' === $phpmailer->ContentType || !empty($phpmailer->AltBody)) {
            return;
        }
        $message = $this->stripHtmlTags($phpmailer->Body);
        $phpmailer->AltBody = glsr()->filterString('email/message', $message, 'text', $this);
    }

    /**
     * @return array
     */
    protected function buildHeaders()
    {
        $allowed = [
            'bcc', 'cc', 'from', 'reply-to',
        ];
        $headers = array_intersect_key($this->email, array_flip($allowed));
        $headers = array_filter($headers);
        foreach ($headers as $key => $value) {
            unset($headers[$key]);
            $headers[] = $key.': '.$value;
        }
        $headers[] = 'Content-Type: text/html';
        return glsr()->filterArray('email/headers', $headers, $this);
    }

    /**
     * @return string
     */
    protected function buildHtmlMessage()
    {
        $message = $this->buildMessage();
        $message = $this->email['before'].$message.$this->email['after'];
        $message = strip_shortcodes($message);
        $message = wptexturize($message);
        $message = wpautop($message);
        $message = str_replace('&lt;&gt; ', '', $message);
        $message = str_replace(']]>', ']]&gt;', $message);
        $message = glsr(Template::class)->build('partials/email/index', [
            'context' => ['message' => $message],
        ]);
        return glsr()->filterString('email/message', stripslashes($message), 'html', $this);
    }

    /**
     * @return string
     */
    protected function buildMessage()
    {
        if (!empty($this->email['message'])) {
            return $this->email['message'];
        }
        $context = ['context' => $this->email['template-tags']];
        $template = trim(glsr(OptionManager::class)->get('settings.general.notification_message'));
        if ('email-notification' === $this->email['template'] && !empty($template)) {
            return glsr(Template::class)->interpolate($template, $this->email['template'], $context);
        } elseif (!empty($this->email['template'])) {
            return glsr(Template::class)->build('templates/'.$this->email['template'], $context);
        }
        return '';
    }

    /**
     * @return void
     */
    protected function normalize(array $email = [])
    {
        $email = glsr(EmailDefaults::class)->restrict($email);
        $this->email = glsr()->filterArray('email/compose', $email, $this);
    }

    /**
     * @return void
     */
    protected function reset()
    {
        $this->attachments = [];
        $this->data = [];
        $this->email = [];
        $this->headers = [];
        $this->message = '';
        $this->subject = '';
        $this->to = '';
    }

    /**
     * @return string
     */
    protected function stripHtmlTags($string)
    {
        // remove invisible elements
        $string = preg_replace('@<(embed|head|noembed|noscript|object|script|style)[^>]*?>.*?</\\1>@siu', '', $string);
        // replace certain elements with a line-break
        $string = preg_replace('@</(div|h[1-9]|p|pre|tr)@iu', "\r\n\$0", $string);
        // replace other elements with a space
        $string = preg_replace('@</(td|th)@iu', ' $0', $string);
        // add a placeholder for plain-text bullets to list elements
        $string = preg_replace('@<(li)[^>]*?>@siu', '$0-o-^-o-', $string);
        // strip all remaining HTML tags
        $string = wp_strip_all_tags($string);
        $string = wp_specialchars_decode($string, ENT_QUOTES);
        $string = preg_replace('/\v(?:[\v\h]+){2,}/u', "\r\n\r\n", $string);
        $string = str_replace('-o-^-o-', ' - ', $string);
        return html_entity_decode($string, ENT_QUOTES, 'UTF-8');
    }
}
