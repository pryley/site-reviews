<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helper;

class ReviewLimitsValidator extends ValidatorAbstract
{
    public function filterSqlClauseOperator(): string
    {
        return 'AND';
    }

    public function isValid(): bool
    {
        $method = Helper::buildMethodName('validateBy', (string) glsr_get_option('forms.limit'));
        return method_exists($this, $method)
            ? call_user_func([$this, $method])
            : true;
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            $this->fail(__('You have already submitted a review.', 'site-reviews'));
        }
    }

    protected function isWhitelisted(string $value, string $whitelist): bool
    {
        if (empty($whitelist)) {
            return false;
        }
        $values = array_filter(array_map('trim', explode("\n", $whitelist)));
        return in_array($value, $values);
    }

    protected function normalizeArgs(array $args): array
    {
        $assignments = glsr_get_option('forms.limit_assignments', ['assigned_posts'], 'array'); // assigned_posts is the default
        $limitToDays = max(0, glsr_get_option('forms.limit_time', 0, 'int'));
        if (in_array('assigned_posts', $assignments)) {
            $args['assigned_posts'] = $this->request->assigned_posts;
        }
        if (in_array('assigned_terms', $assignments)) {
            $args['assigned_terms'] = $this->request->assigned_terms;
        }
        if (in_array('assigned_users', $assignments)) {
            $args['assigned_users'] = $this->request->assigned_users;
        }
        if ($limitToDays > 0) {
            $args['date'] = [
                'after' => wp_date('Y-m-d H:i:s', time() - (DAY_IN_SECONDS * $limitToDays)),
                'inclusive' => true, // all reviews after and on this exact date
            ];
        }
        $args['status'] = 'all';
        return $args;
    }

    protected function validateByEmail(): bool
    {
        glsr_log()->debug("Email is: {$this->request->email}");
        return $this->validateLimit('email', $this->request->email, [
            'email' => $this->request->email,
        ]);
    }

    protected function validateByIpAddress(): bool
    {
        glsr_log()->debug("IP Address is: {$this->request->ip_address}");
        return $this->validateLimit('ip_address', $this->request->ip_address, [
            'ip_address' => $this->request->ip_address,
        ]);
    }

    protected function validateByUsername(): bool
    {
        $user = wp_get_current_user();
        if (!$user->exists()) {
            return true;
        }
        glsr_log()->debug("Username is: {$user->user_login}");
        return $this->validateLimit('username', $user->user_login, [
            'user__in' => $user->ID,
        ]);
    }

    protected function validateLimit(string $key, string $value, array $args): bool
    {
        if (empty($value)) {
            return true;
        }
        if ($this->isWhitelisted($value, glsr_get_option("forms.limit_whitelist.{$key}"))) {
            return true;
        }
        add_filter('query/sql/clause/operator', [$this, 'filterSqlClauseOperator'], 20);
        $reviews = glsr_get_reviews($this->normalizeArgs($args));
        remove_filter('query/sql/clause/operator', [$this, 'filterSqlClauseOperator'], 20);
        $result = 0 === $reviews->total;
        return glsr()->filterBool('validate/review-limits', $result, $reviews, $this->request, $key);
    }
}
