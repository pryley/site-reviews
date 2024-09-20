<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

class DuplicateValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        if ('yes' !== glsr_get_option('forms.prevent_duplicates')) {
            return true;
        }
        $userId = get_current_user_id();
        $args = [
            'assigned_posts' => $this->request->assigned_posts,
            'author_id' => $userId,
            'content' => $this->request->content,
            'per_page' => 1,
            'status' => 'all',
        ];
        if (0 === $userId) {
            $args['email'] = $this->request->email;
        }
        $result = 0 === glsr_get_reviews($args)->total;
        return glsr()->filterBool('validate/duplicate', $result, $this->request);
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            $this->fail(__('Duplicate review detected. It looks like you already said that!', 'site-reviews'));
        }
    }
}
