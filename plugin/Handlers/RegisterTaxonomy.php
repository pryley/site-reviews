<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\RegisterTaxonomy as Command;

class RegisterTaxonomy
{
	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		register_taxonomy( Application::TAXONOMY, Application::POST_TYPE, $command->args );
		register_taxonomy_for_object_type( Application::TAXONOMY, Application::POST_TYPE );

		add_action( Application::TAXONOMY.'_term_edit_form_top', [$this, 'disableParents'] );
		add_action( Application::TAXONOMY.'_term_new_form_tag',  [$this, 'disableParents'] );
		add_action( Application::TAXONOMY.'_add_form_fields',    [$this, 'enableParents'] );
		add_action( Application::TAXONOMY.'_edit_form',          [$this, 'enableParents'] );
		add_action( 'restrict_manage_posts',                     [$this, 'renderTaxonomyFilter'], 9 );
	}

	/**
	 * @return void
	 * @action Application::TAXONOMY._add_form_fields
	 * @action Application::TAXONOMY._edit_form
	 */
	public function disableParents()
	{
		global $wp_taxonomies;
		$wp_taxonomies[Application::TAXONOMY]->hierarchical = false;
	}

	/**
	 * @return void
	 * @action Application::TAXONOMY._term_edit_form_top
	 * @action Application::TAXONOMY._term_new_form_tag
	 */
	public function enableParents()
	{
		global $wp_taxonomies;
		$wp_taxonomies[Application::TAXONOMY]->hierarchical = true;
	}

	/**
	 * @return void
	 * @action restrict_manage_posts
	 */
	public function renderTaxonomyFilter()
	{
		global $wp_query;
		if( !is_object_in_taxonomy( get_current_screen()->post_type, Application::TAXONOMY )
			|| apply_filters( 'site-reviews/disable/filter/category', false )
		)return;
		printf( '<label class="screen-reader-text" for="%s">%s</label>', Application::TAXONOMY, __( 'Filter by category', 'site-reviews' ));
		$selected = isset( $wp_query->query[Application::TAXONOMY] )
			? $wp_query->query[Application::TAXONOMY]
			: '';
		$taxonomy = get_taxonomy( Application::TAXONOMY );
		$showOptionAll = $taxonomy
			? ucfirst( strtolower( $taxonomy->labels->all_items ))
			: '';
		wp_dropdown_categories([
			'depth' => 3,
			'hide_empty' => true,
			'hide_if_empty' => true,
			'hierarchical' => true,
			'name' => Application::TAXONOMY,
			'orderby' => 'name',
			'selected' => $selected,
			'show_count' => false,
			'show_option_all' => $showOptionAll,
			'taxonomy' => Application::TAXONOMY,
			'value_field' => 'slug',
		]);
	}
}
