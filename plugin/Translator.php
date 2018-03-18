<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     2.3.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\App;
use GeminiLabs\SiteReviews\Database;
use Sepia\PoParser\Parser;

class Translator
{
	/**
	 * @var App
	 */
	protected $app;

	/**
	 * @var Database
	 */
	protected $db;

	/**
	 * @var array
	 */
	protected $entries;

	/**
	 * @var array
	 */
	protected $results;

	public function __construct( App $app, Database $db )
	{
		$this->app = $app;
		$this->db = $db;
	}

	/**
	 * Returns all saved custom translations with translation context
	 * @return array
	 */
	public function all()
	{
		$translations = $this->getSettings();
		$entries = $this->filter( $translations, $this->entries() )->results();
		array_walk( $translations, function( &$entry ) use( $entries ) {
			$entry['desc'] = array_key_exists( $entry['id'], $entries )
				? $this->getEntryString( $entries[$entry['id']], 'msgctxt' )
				: '';
		});
		return $translations;
	}

	/**
	 * @return array
	 */
	public function entries()
	{
		if( !is_array( $this->entries )) {
			$entries = $this->normalize(
				Parser::parseFile( $this->app->path . 'languages/site-reviews.pot' )->getEntries()
			);
			foreach( $entries as $key => $entry ) {
				$this->entries[html_entity_decode( $key, ENT_COMPAT, 'UTF-8' )] = $entry;
			}
		}
		return $this->entries;
	}

	/**
	 * @param null|array $entriesToExclude
	 * @param null|array $entries
	 * @return self
	 */
	public function exclude( $entriesToExclude = null, $entries = null )
	{
		return $this->filter( $entriesToExclude, $entries, false );
	}

	/**
	 * @param null|array $filterWith
	 * @param null|array $entries
	 * @param bool $intersect
	 * @return self
	 */
	public function filter( $filterWith = null, $entries = null, $intersect = true )
	{
		if( !is_array( $entries )) {
			$entries = $this->results;
		}
		if( !is_array( $filterWith )) {
			$filterWith = $this->getSettings();
		}
		$keys = array_flip( array_column( $filterWith, 'id' ));
		$this->results = $intersect
			? array_intersect_key( $entries, $keys )
			: array_diff_key( $entries, $keys );
		return $this;
	}

	/**
	 * @param string $template
	 * @return string
	 */
	public function render( $template, array $entry )
	{
		ob_start();
		$this->app->make( 'Controllers\MainController' )->renderTemplate( 'strings/'.$template, $entry );
		return ob_get_clean();
	}

	/**
	 * Returns a rendered string of all saved custom translations with translation context
	 * @return string
	 */
	public function renderAll()
	{
		$rendered = '';
		foreach( $this->all() as $index => $entry ) {
			$entry['index'] = $index;
			$entry['prefix'] = $this->db->getOptionName();
			$rendered .= $this->render( $entry['type'], $entry );
		}
		return $rendered;
	}

	/**
	 * @param bool $resetAfterRender
	 * @return string
	 */
	public function renderResults( $resetAfterRender = true )
	{
		$rendered = '';
		foreach( $this->results as $id => $entry ) {
			$data = [
				'desc' => $this->getEntryString( $entry, 'msgctxt' ),
				'id' => $id,
				'p1' => $this->getEntryString( $entry, 'msgid_plural' ),
				's1' => $this->getEntryString( $entry, 'msgid' ),
			];
			$text = !empty( $data['p1'] )
				? sprintf( '%s | %s', $data['s1'], $data['p1'] )
				: $data['s1'];
			$rendered .= $this->render( 'result', [
				'entry' => wp_json_encode( $data ),
				'text' => wp_strip_all_tags( $text ),
			]);
		}
		if( $resetAfterRender ) {
			$this->reset();
		}
		return $rendered;
	}

	/**
	 * @return void
	 */
	public function reset()
	{
		$this->results = [];
	}

	/**
	 * @return array
	 */
	public function results()
	{
		$results = $this->results;
		$this->reset();
		return $results;
	}

	/**
	 * @param string $needle
	 * @param int $threshold
	 * @param bool $caseSensitive
	 * @return self
	 */
	public function search( $needle = '', $threshold = 3, $caseSensitive = false )
	{
		$this->reset();
		$needle = trim( $needle );
		foreach( $this->entries() as $key => $entry ) {
			$single = $this->getEntryString( $entry, 'msgid' );
			$plural = $this->getEntryString( $entry, 'msgid_plural' );
			if( !$caseSensitive ) {
				$needle = strtolower( $needle );
				$single = strtolower( $single );
				$plural = strtolower( $plural );
			}
			if( strlen( $needle ) < $threshold ) {
				if( in_array( $needle, [$single, $plural] )) {
					$this->results[$key] = $entry;
				}
			}
			else if( strpos( sprintf( '%s %s', $single, $plural ), $needle ) !== false ) {
				$this->results[$key] = $entry;
			}
		}
		return $this;
	}

	/**
	 * @param string $original
	 * @param string $domain
	 * @return string
	 */
	public function translate( $original, $domain, array $args )
	{
		if( $domain != 'site-reviews' ) {
			return $original;
		}
		$args = $this->normalizeTranslationArgs( $args );
		extract( $args );
		$strings = $this->getSettings();
		$strings = array_filter( $strings, function( $string ) use( $single, $plural ) {
			return $string['s1'] == html_entity_decode( $single, ENT_COMPAT, 'UTF-8' )
				&& $string['p1'] == html_entity_decode( $plural, ENT_COMPAT, 'UTF-8' );
		});
		if( empty( $strings )) {
			return $original;
		}
		$string = current( $strings );
		if( !empty( $string['s2'] )) {
			$single = $string['s2'];
		}
		if( !empty( $string['p2'] )) {
			$plural = $string['p2'];
		}
		$translations = get_translations_for_domain( $domain );
		return $string['type'] == 'plural'
			? $translations->translate_plural( $single, $plural, $number, $context )
			: $translations->translate( $single, $context );
	}

	/**
	 * @param string $translation
	 * @param string $text
	 * @param string $domain
	 * @return string
	 */
	public function translateGettext( $translation, $text, $domain )
	{
		return $this->translate( $translation, $domain, [
			'single' => $text,
		]);
	}

	/**
	 * @param string $translation
	 * @param string $text
	 * @param string $context
	 * @param string $domain
	 * @return string
	 */
	public function translateGettextWithContext( $translation, $text, $context, $domain )
	{
		return $this->translate( $translation, $domain, [
			'context' => $context,
			'single' => $text,
		]);
	}

	/**
	 * @param string $translation
	 * @param string $single
	 * @param string $plural
	 * @param int $number
	 * @param string $domain
	 * @return string
	 */
	public function translateNgettext( $translation, $single, $plural, $number, $domain )
	{
		return $this->translate( $translation, $domain, [
			'number' => $number,
			'plural' => $plural,
			'single' => $single,
		]);
	}

	/**
	 * @param string $translation
	 * @param string $single
	 * @param string $plural
	 * @param int $number
	 * @param string $context
	 * @param string $domain
	 * @return string
	 */
	public function translateNgettextWithContext( $translation, $single, $plural, $number, $context, $domain )
	{
		return $this->translate( $translation, $domain, [
			'context' => $context,
			'number' => $number,
			'plural' => $plural,
			'single' => $single,
		]);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function getEntryString( array $entry, $key )
	{
		return isset( $entry[$key] )
			? implode( '', (array) $entry[$key] )
			: '';
	}

	/**
	 * @return array
	 */
	protected function getSettings()
	{
		$settings = $this->db->getOptions( 'settings' );
		return isset( $settings['strings'] )
			? $this->normalizeSettings( (array) $settings['strings'] )
			: [];
	}

	/**
	 * @return array
	 */
	protected function normalize( array $entries )
	{
		$keys = [
			'msgctxt', 'msgid', 'msgid_plural', 'msgstr', 'msgstr[0]', 'msgstr[1]',
		];
		array_walk( $entries, function( &$entry ) use( $keys ) {
			foreach( $keys as $key ) {
				$entry = $this->normalizeEntryString( $entry, $key );
			}
		});
		return $entries;
	}

	/**
	 * @param string $key
	 * @return array
	 */
	protected function normalizeEntryString( array $entry, $key )
	{
		if( isset( $entry[$key] )) {
			$entry[$key] = $this->getEntryString( $entry, $key );
		}
		return $entry;
	}

	/**
	 * @return array
	 */
	protected function normalizeSettings( array $strings )
	{
		$defaultString = array_fill_keys( ['id', 's1', 's2', 'p1', 'p2'], '' );
		$strings = array_filter( $strings, 'is_array' );
		foreach( $strings as &$string ) {
			$string['type'] = isset( $string['p1'] ) ? 'plural' : 'single';
			$string = wp_parse_args( $string, $defaultString );
		}
		return array_filter( $strings, function( $string ) {
			return !empty( $string['id'] );
		});
	}

	/**
	 * @return array
	 */
	protected function normalizeTranslationArgs( array $args )
	{
		$defaults = [
			'context' => '',
			'number' => 1,
			'plural' => '',
			'single' => '',
		];
		return shortcode_atts( $defaults, $args );
	}
}
