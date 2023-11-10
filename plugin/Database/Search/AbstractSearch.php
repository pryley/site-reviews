<?php

namespace GeminiLabs\SiteReviews\Database\Search;

abstract class AbstractSearch
{
    protected \wpdb $db;
    protected array $results = [];

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function render(): string
    {
        return '';
    }

    public function results(): array
    {
        return $this->results;
    }

    /**
     * @return static
     */
    public function search(string $searchTerm)
    {
        if (empty($searchTerm)) {
            $this->results = [];
        } elseif (is_numeric($searchTerm)) {
            $this->results = $this->searchById((int) $searchTerm);
        } else {
            $this->results = $this->searchByTerm($searchTerm);
        }
        return $this;
    }

    abstract protected function searchById(int $searchId): array;

    abstract protected function searchByTerm(string $searchTerm): array;
}
