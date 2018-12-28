<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\ChangeStatus as Command;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ChangeStatus
{
	/**
	 * @return array
	 */
	public function handle( Command $command )
	{
		$postId = wp_update_post([
			'ID' => $command->id,
			'post_status' => $command->status,
		]);
		if( is_wp_error( $postId )) {
			glsr_log()->error( $postId->get_error_message() );
			return [];
		}
		return [
			'class' => 'status-'.$command->status,
			'counts' => $this->getStatusLinks(),
			'link' => $this->getPostLink( $postId ).$this->getPostState( $postId ),
		];
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function getPostLink( $postId )
	{
		$title = _draft_or_post_title( $postId );
		return glsr( Builder::class )->a( $title, [
			'aria-label' => '&#8220;'.esc_attr( $title ).'&#8221; ('.__( 'Edit', 'site-reviews' ).')',
			'class' => 'row-title',
			'href' => get_edit_post_link( $postId ),
		]);
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	protected function getPostState( $postId )
	{
		ob_start();
		_post_states( get_post( $postId ));
		return ob_get_clean();
	}

	/**
	 * @return void|string
	 */
	protected function getStatusLinks()
	{
		global $avail_post_stati;
		require_once( ABSPATH.'wp-admin/includes/class-wp-posts-list-table.php' );
		$hookName = 'edit-'.Application::POST_TYPE;
		set_current_screen( $hookName );
		$avail_post_stati = get_available_post_statuses( Application::POST_TYPE );
		$table = new \WP_Posts_List_Table( ['screen' => $hookName] );
		$views = apply_filters( 'views_'.$hookName, $table->get_views() ); // uses compat get_views()
		if( empty( $views ))return;
		foreach( $views as $class => $view ) {
			$views[$class] = "\t<li class='$class'>$view";
		}
		return implode( ' |</li>', $views ).'</li>';
	}
}
