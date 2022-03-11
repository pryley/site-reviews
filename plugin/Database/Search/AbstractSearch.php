<?php

namespace GeminiLabs\SiteReviews\Database\Search;

abstract class AbstractSearch
{
    protected $db;
    protected $results;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->results = [];
    }

    /**
     * @return string
     */
    public function render()
    {
        return '';
    }

    /**
     * @return array
     */
    public function results()
    {
        return $this->results;
    }

    /**
     * @param string $searchTerm
     * @return static
     */
    public function search($searchTerm)
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

    /**
     * @param int $searchId
     * @return array
     */
    abstract protected function searchById($searchId);

    /**
     * @param string $searchTerm
     * @return array
     */
    abstract protected function searchByTerm($searchTerm);
}
