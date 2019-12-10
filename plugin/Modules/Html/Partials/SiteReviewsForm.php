<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\StyleValidationDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Form;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class SiteReviewsForm
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array
     */
    protected $required;

    /**
     * @var array
     */
    protected $values;

    /**
     * @return void|string
     */
    public function build(array $args = [])
    {
        $this->args = $args;
        if (!is_user_logged_in() && glsr(OptionManager::class)->getBool('settings.general.require.login')) {
            return $this->buildLoginRegister();
        }
        $this->errors = glsr()->sessionGet($args['id'].'errors', []);
        $this->message = glsr()->sessionGet($args['id'].'message', '');
        $this->required = glsr(OptionManager::class)->get('settings.submissions.required', []);
        $this->values = glsr()->sessionGet($args['id'].'values', []);
        $fields = array_reduce($this->getFields(), function ($carry, $field) {
            return $carry.$field;
        });
        return glsr(Template::class)->build('templates/reviews-form', [
            'args' => $args,
            'context' => [
                'class' => $this->getClass(),
                'fields' => $fields,
                'id' => $this->args['id'],
                'response' => $this->buildResponse(),
                'submit_button' => $this->buildSubmitButton().$this->buildRecaptcha(),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function buildLoginRegister()
    {
        return glsr(Template::class)->build('templates/login-register', [
            'context' => [
                'text' => trim($this->getLoginText().' '.$this->getRegisterText()),
            ],
        ]);
    }

    /**
     * @return void|string
     */
    protected function buildRecaptcha()
    {
        if (!glsr(OptionManager::class)->isRecaptchaEnabled()) {
            return;
        }
        return glsr(Builder::class)->div([
            'class' => 'glsr-recaptcha-holder',
            'data-badge' => glsr(OptionManager::class)->get('settings.submissions.recaptcha.position'),
            'data-sitekey' => sanitize_text_field(glsr(OptionManager::class)->get('settings.submissions.recaptcha.key')),
            'data-size' => 'invisible',
        ]);
    }

    /**
     * @return string
     */
    protected function buildResponse()
    {
        $classes = !empty($this->errors)
            ? glsr(StyleValidationDefaults::class)->defaults()['message_error_class']
            : '';
        return glsr(Template::class)->build('templates/form/response', [
            'context' => [
                'class' => $classes,
                'message' => wpautop($this->message),
            ],
            'has_errors' => !empty($this->errors),
        ]);
    }

    /**
     * @return string
     */
    protected function buildSubmitButton()
    {
        return glsr(Template::class)->build('templates/form/submit-button', [
            'context' => [
                'text' => __('Submit your review', 'site-reviews'),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return trim('glsr-form glsr-'.glsr(Style::class)->get().' '.$this->args['class']);
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        $hiddenFields = $this->getHiddenFields();
        $hiddenFields[] = $this->getHoneypotField();
        $fields = $this->normalizeFields(glsr(Form::class)->getFields('submission-form'));
        $paths = array_map(function ($obj) {
            return $obj->field['path'];
        }, $hiddenFields);
        foreach ($fields as $field) {
            $index = array_search($field->field['path'], $paths);
            if (false === $index) {
                continue;
            }
            unset($hiddenFields[$index]);
        }
        return array_merge($hiddenFields, $fields);
    }

    /**
     * @return string
     */
    protected function getLoginText()
    {
        $loginLink = glsr(Builder::class)->a([
            'href' => wp_login_url(strval(get_permalink())),
            'text' => __('logged in', 'site-reviews'),
        ]);
        return sprintf(__('You must be %s to submit a review.', 'site-reviews'), $loginLink);
    }

    /**
     * @return void|string
     */
    protected function getRegisterText()
    {
        if (!get_option('users_can_register') || !glsr(OptionManager::class)->getBool('settings.general.require.login')) {
            return;
        }
        $registerLink = glsr(Builder::class)->a([
            'href' => wp_registration_url(),
            'text' => __('register', 'site-reviews'),
        ]);
        return sprintf(__('You may also %s for an account.', 'site-reviews'), $registerLink);
    }

    /**
     * @return array
     */
    protected function getHiddenFields()
    {
        $fields = [[
            'name' => '_action',
            'value' => 'submit-review',
        ], [
            'name' => '_counter',
        ], [
            'name' => '_nonce',
            'value' => wp_create_nonce('submit-review'),
        ], [
            'name' => '_post_id',
            'value' => get_the_ID(),
        ], [
            'name' => '_referer',
            'value' => wp_unslash(filter_input(INPUT_SERVER, 'REQUEST_URI')),
        ], [
            'name' => 'assign_to',
            'value' => $this->args['assign_to'],
        ], [
            'name' => 'category',
            'value' => $this->args['category'],
        ], [
            'name' => 'excluded',
            'value' => $this->args['hide'],
        ], [
            'name' => 'form_id',
            'value' => $this->args['id'],
        ]];
        return array_map(function ($field) {
            return new Field(wp_parse_args($field, ['type' => 'hidden']));
        }, $fields);
    }

    /**
     * @return Field
     */
    protected function getHoneypotField()
    {
        return new Field([
            'name' => 'gotcha',
            'type' => 'honeypot',
        ]);
    }

    /**
     * @return void
     */
    protected function normalizeFieldId(Field &$field)
    {
        if (empty($this->args['id']) || empty($field->field['id'])) {
            return;
        }
        $field->field['id'].= '-'.$this->args['id'];
    }

    /**
     * @return void
     */
    protected function normalizeFieldClass(Field &$field)
    {
        if (!isset($field->field['class'])) {
            $field->field['class'] = '';
        }
        $field->field['class'] = trim($field->field['class'].' glsr-field-control');
    }

    /**
     * @return void
     */
    protected function normalizeFieldErrors(Field &$field)
    {
        if (!array_key_exists($field->field['path'], $this->errors)) {
            return;
        }
        $field->field['errors'] = $this->errors[$field->field['path']];
    }

    /**
     * @return void
     */
    protected function normalizeFieldRequired(Field &$field)
    {
        if (!in_array($field->field['path'], $this->required)) {
            return;
        }
        $field->field['required'] = true;
    }

    /**
     * @return array
     */
    protected function normalizeFields($fields)
    {
        $normalizedFields = [];
        foreach ($fields as $field) {
            if (in_array($field->field['path'], $this->args['hide'])) {
                continue;
            }
            $field->field['is_public'] = true;
            $this->normalizeFieldClass($field);
            $this->normalizeFieldErrors($field);
            $this->normalizeFieldRequired($field);
            $this->normalizeFieldValue($field);
            $this->normalizeFieldId($field);
            $normalizedFields[] = $field;
        }
        return $normalizedFields;
    }

    /**
     * @return void
     */
    protected function normalizeFieldValue(Field &$field)
    {
        if (!array_key_exists($field->field['path'], $this->values)) {
            return;
        }
        if (in_array($field->field['type'], ['radio', 'checkbox'])) {
            $field->field['checked'] = $field->field['value'] == $this->values[$field->field['path']];
        } else {
            $field->field['value'] = $this->values[$field->field['path']];
        }
    }
}
