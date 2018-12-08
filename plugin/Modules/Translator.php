<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Translation;

class Translator
{
	/**
	 * @param string $translation
	 * @param string $text
	 * @param string $domain
	 * @return string
	 */
	public function filterGettext( $translation, $text, $domain )
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
	public function filterGettextWithContext( $translation, $text, $context, $domain )
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
	public function filterNgettext( $translation, $single, $plural, $number, $domain )
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
	public function filterNgettextWithContext( $translation, $single, $plural, $number, $context, $domain )
	{
		return $this->translate( $translation, $domain, [
			'context' => $context,
			'number' => $number,
			'plural' => $plural,
			'single' => $single,
		]);
	}

	/**
	 * @param string $original
	 * @param string $domain
	 * @return string
	 */
	public function translate( $original, $domain, array $args )
	{
		$domains = apply_filters( 'site-reviews/translator/domains', [Application::ID] );
		if( !in_array( $domain, $domains )) {
			return $original;
		}
		$args = $this->normalizeTranslationArgs( $args );
		$strings = $this->getTranslationStrings( $args['single'], $args['plural'] );
		if( empty( $strings )) {
			return $original;
		}
		$string = current( $strings );
		return $string['type'] == 'plural'
			? $this->translatePlural( $domain, $string, $args )
			: $this->translateSingle( $domain, $string, $args );
	}

	/**
	 * @param string $single
	 * @param string $plural
	 * @return array
	 */
	protected function getTranslationStrings( $single, $plural )
	{
		return array_filter( glsr( Translation::class )->translations(), function( $string ) use( $single, $plural ) {
			return $string['s1'] == html_entity_decode( $single, ENT_COMPAT, 'UTF-8' )
				&& $string['p1'] == html_entity_decode( $plural, ENT_COMPAT, 'UTF-8' );
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

	/**
	 * @param string $domain
	 * @return string
	 */
	protected function translatePlural( $domain, array $string, array $args )
	{
		if( !empty( $string['s2'] )) {
			$args['single'] = $string['s2'];
		}
		if( !empty( $string['p2'] )) {
			$args['plural'] = $string['p2'];
		}
		return get_translations_for_domain( $domain )->translate_plural(
			$args['single'],
			$args['plural'],
			$args['number'],
			$args['context']
		);
	}

	/**
	 * @param string $domain
	 * @return string
	 */
	protected function translateSingle( $domain, array $string, array $args )
	{
		if( !empty( $string['s2'] )) {
			$args['single'] = $string['s2'];
		}
		return get_translations_for_domain( $domain )->translate(
			$args['single'],
			$args['context']
		);
	}
}
