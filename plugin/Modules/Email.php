<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\EmailContract;
use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Contracts\TemplateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Defaults\EmailDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Email implements EmailContract
{
    /** @var array */
    public $attachments;

    /** @var array */
    public $data;

    /** @var array */
    public $email;

    /** @var array */
    public $headers;

    /** @var string */
    public $message;

    /** @var string */
    public $subject;

    /** @var string|array */
    public $to;

    public function app(): PluginContract
    {
        return glsr();
    }

    public function compose(array $email, array $data = []): EmailContract
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

    public function data(): Arguments
    {
        return glsr()->args($this->data);
    }

    public function defaults(): DefaultsAbstract
    {
        return glsr(EmailDefaults::class);
    }

    public function logMailError(\WP_Error $error): void
    {
        glsr_log()
            ->error('[wp_mail] Email was not sent: '.$error->get_error_message())
            ->debug([
                'headers' => $this->headers,
                'attachments' => $this->attachments,
                'email' => $this->email,
            ]);
    }

    public function read(string $format = ''): string
    {
        if ('plaintext' === $format) {
            $message = $this->stripHtmlTags($this->message);
            return $this->app()->filterString('email/message', $message, 'text', $this);
        }
        return $this->message;
    }

    public function send(): bool
    {
        $required = [
            'message' => !empty($this->message),
            'recipient' => !empty($this->to),
            'subject' => !empty($this->subject),
        ];
        $missing = array_keys(array_diff($required, array_filter($required)));
        if (!empty($missing)) {
            glsr_log()->warning(sprintf('The email is missing the %s', Str::naturalJoin($missing)));
            return false;
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

    public function template(): TemplateContract
    {
        return glsr(Template::class);
    }

    /**
     * @action phpmailer_init
     */
    public function buildPlainTextMessage($phpmailer): void
    {
        if (empty($this->email)) {
            return;
        }
        if ('text/plain' === $phpmailer->ContentType || !empty($phpmailer->AltBody)) {
            return;
        }
        $message = $this->stripHtmlTags($phpmailer->Body);
        $phpmailer->AltBody = $this->app()->filterString('email/message', $message, 'text', $this);
    }

    protected function buildHeaders(): array
    {
        $allowed = [
            'bcc', 'cc', 'from', 'reply-to',
        ];
        $headers = array_intersect_key($this->email, array_flip($allowed));
        $headers = array_filter($headers);
        foreach ($headers as $key => $value) {
            unset($headers[$key]);
            $headers[] = "{$key}: {$value}";
        }
        $headers[] = 'Content-Type: text/html';
        return $this->app()->filterArray('email/headers', $headers, $this);
    }

    protected function buildHtmlMessage(): string
    {
        $message = $this->buildMessage();
        $message = $this->email['before'].$message.$this->email['after'];
        $message = strip_shortcodes($message);
        $message = wptexturize($message);
        $message = wpautop($message);
        $message = str_replace('&lt;&gt; ', '', $message);
        $message = str_replace(']]>', ']]&gt;', $message);
        $context = wp_parse_args(['message' => $message], $this->email['template-tags']);
        $template = $this->email['template'];
        $message = $this->template()->build("templates/emails/{$template}", [
            'context' => $context,
        ]);
        return $this->app()->filterString('email/message', stripslashes($message), 'html', $this);
    }

    protected function buildMessage(): string
    {
        if (!empty($this->email['message'])) {
            return $this->email['message'];
        }
        $template = trim(glsr(OptionManager::class)->get('settings.general.notification_message'));
        if (!empty($template)) {
            $context = ['context' => $this->email['template-tags']];
            $templatePathForHook = 'notification_message';
            return $this->template()->interpolate($template, $templatePathForHook, $context);
        }
        return '';
    }

    protected function normalize(array $email = []): void
    {
        $email = $this->defaults()->restrict($email);
        $this->email = $this->app()->filterArray('email/compose', $email, $this);
    }

    protected function reset(): void
    {
        $this->attachments = [];
        $this->data = [];
        $this->email = [];
        $this->headers = [];
        $this->message = '';
        $this->subject = '';
        $this->to = '';
    }

    protected function stripHtmlTags($string): string
    {
        // remove invisible elements
        $string = preg_replace('@<(embed|head|noembed|noscript|object|script|style)[^>]*?>.*?</\\1>@siu', '', $string);
        // replace link elements
        $string = preg_replace_callback('@<a[^>]*href=("|\')(.*?)\1[^>]*>(.*?)<\/a>@iu', function ($matches) {
            $matches = array_map('trim', $matches);
            return ($matches[2] !== $matches[3])
                ? sprintf('%s (%s)', $matches[3], $matches[2])
                : $matches[2];
        }, $string);
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
