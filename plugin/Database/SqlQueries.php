<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\QueryBuilder;

class SqlQueries
{
	protected $db;
	protected $postType;

	public function __construct()
	{
		global $wpdb;
		$this->db = $wpdb;
		$this->postType = Application::POST_TYPE;
	}

	/**
	 * @param string $sessionCookiePrefix
	 * @return int|false
	 */
	public function deleteAllSessions( $sessionCookiePrefix )
	{
		return $this->db->query("
			DELETE
			FROM {$this->db->options}
			WHERE option_name LIKE '{$sessionCookiePrefix}_%'
		");
	}

	/**
	 * @param string $expiredSessions
	 * @return int|false
	 */
	public function deleteExpiredSessions( $expiredSessions )
	{
		return $this->db->query("
			DELETE
			FROM {$this->db->options}
			WHERE option_name IN ('{$expiredSessions}')
		");
	}

	/**
	 * @param string $metaKey
	 * @return array
	 */
	public function getReviewCountsFor( $metaKey )
	{
		return (array) $this->db->get_results("
			SELECT m.meta_value AS name, COUNT(*) num_posts
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
			WHERE p.post_type = '{$this->postType}'
			AND m.meta_key = '{$metaKey}'
			GROUP BY name
		");
	}

	/**
	 * @param string $sessionCookiePrefix
	 * @param int $limit
	 * @return array
	 */
	public function getExpiredSessions( $sessionCookiePrefix, $limit )
	{
		return $this->db->get_results("
			SELECT option_name AS name, option_value AS expiration
			FROM {$this->db->options}
			WHERE option_name LIKE '{$sessionCookiePrefix}_expires_%'
			ORDER BY option_value ASC
			LIMIT 0, {$limit}
		");
	}

	/**
	 * @param int $lastPostId
	 * @param int $limit
	 * @return array
	 */
	public function getReviewCounts( $lastPostId = 0, $limit = 500 )
	{
		return $this->db->get_results("
			SELECT p.ID, m1.meta_value AS rating, m2.meta_value AS type
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
			INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
			WHERE p.ID > {$lastPostId}
			AND p.post_status = 'publish'
			AND p.post_type = '{$this->postType}'
			AND m1.meta_key = 'rating'
			AND m2.meta_key = 'review_type'
			ORDER By p.ID
			ASC LIMIT {$limit}
		", OBJECT );
	}

	/**
	 * @param string $metaReviewId
	 * @return int
	 */
	public function getReviewPostId( $metaReviewId )
	{
		$postId = $this->db->get_var("
			SELECT p.ID
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
			WHERE p.post_type = '{$this->postType}'
			AND m.meta_key = 'review_id'
			AND m.meta_value = '{$metaReviewId}'
		");
		return intval( $postId );
	}

	/**
	 * @param string $reviewType
	 * @return array
	 */
	public function getReviewIdsByType( $reviewType )
	{
		$query = $this->db->get_col("
			SELECT m1.meta_value AS review_id
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
			INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
			WHERE p.post_type = '{$this->db->postType}'
			AND m1.meta_key = 'review_id'
			AND m2.meta_key = 'review_type'
			AND m2.meta_value = '{$reviewType}'
		");
		return array_keys( array_flip( $query ));
	}

	/**
	 * @param int $postId
	 * @param int $lastPostId
	 * @param int $limit
	 * @return array
	 */
	public function getReviewPostCounts( $postId, $lastPostId = 0, $limit = 500 )
	{
		return $this->db->get_results("
			SELECT p.ID, m1.meta_value AS rating, m2.meta_value AS type
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
			INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
			INNER JOIN {$this->db->postmeta} AS m3 ON p.ID = m3.post_id
			WHERE p.ID > {$lastPostId}
			AND p.post_status = 'publish'
			AND p.post_type = '{$this->postType}'
			AND m1.meta_key = 'rating'
			AND m2.meta_key = 'review_type'
			AND m3.meta_key = 'assigned_to'
			AND m3.meta_value = {$postId}
			ORDER By p.ID
			ASC LIMIT {$limit}
		", OBJECT );
	}

	/**
	 * @param int $greaterThanId
	 * @param int $limit
	 * @return array
	 */
	public function getReviewRatingsFromIds( array $postIds, $greaterThanId = 0, $limit = 100 )
	{
		sort( $postIds );
		$postIds = array_slice( $postIds, intval( array_search( $greaterThanId, $postIds )), $limit );
		$postIds = implode( ',', $postIds );
		return $this->db->get_results("
			SELECT p.ID, m.meta_value AS rating
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
			WHERE p.ID > {$greaterThanId}
			AND p.ID IN ('{$postIds}')
			AND p.post_status = 'publish'
			AND p.post_type = '{$this->postType}'
			AND m.meta_key = 'rating'
			ORDER By p.ID
			ASC LIMIT {$limit}
		", OBJECT );
	}

	/**
	 * @param string $key
	 * @param string $status
	 * @return array
	 */
	public function getReviewsMeta( $key, $status = 'publish' )
	{
		$queryBuilder = glsr( QueryBuilder::class );
		$key = $queryBuilder->buildSqlOr( $key, "m.meta_key = '%s'" );
		$status = $queryBuilder->buildSqlOr( $status, "p.post_status = '%s'" );
		return $this->db->get_col("
			SELECT DISTINCT m.meta_value
			FROM {$this->db->postmeta} m
			LEFT JOIN {$this->db->posts} p ON p.ID = m.post_id
			WHERE p.post_type = '{$this->postType}'
			AND ({$key})
			AND ({$status})
			ORDER BY m.meta_value
		");
	}

	/**
	 * @param int $termTaxonomyId
	 * @param int $lastPostId
	 * @param int $limit
	 * @return array
	 */
	public function getReviewTermCounts( $termTaxonomyId, $lastPostId = 0, $limit = 500 )
	{
		return $this->db->get_results("
			SELECT p.ID, m1.meta_value AS rating, m2.meta_value AS type
			FROM {$this->db->posts} AS p
			INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
			INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
			INNER JOIN {$this->db->term_relationships} AS tr ON p.ID = tr.object_id
			WHERE p.ID > {$lastPostId}
			AND p.post_status = 'publish'
			AND p.post_type = '{$this->postType}'
			AND m1.meta_key = 'rating'
			AND m2.meta_key = 'review_type'
			AND tr.term_taxonomy_id = {$termTaxonomyId}
			ORDER By p.ID
			ASC LIMIT {$limit}
		", OBJECT );
	}
}
