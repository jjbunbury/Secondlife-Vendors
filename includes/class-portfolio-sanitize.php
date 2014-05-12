<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Sanitize
 *
 * Registers post types and taxonomies
 *
 * @class 		Vendor_Sanitize
 * @version		2.1.0
 * @package		Vendor/Classes/Sanitize
 * @category	Class
 * @author 		Dazzle Software
 */
if ( ! class_exists( 'Vendor_Sanitize' ) ) {

	class Vendor_Sanitize
	{
		//sanitize_text_field
		/**
         * Sanitize a string from user input or from the db
         *
         * check for invalid UTF-8,
         * Convert single < characters to entity,
         * strip all tags,
         * remove line breaks, tabs and extra white space,
         * strip octets.
         *
         * @since 2.9.0
         *
         * @param string $str
         * @return string
         */
		function sanitize_text($str) {
			$filtered = wp_check_invalid_utf8( $str );

			if ( strpos($filtered, '<') !== false ) {
				$filtered = wp_pre_kses_less_than( $filtered );
				// This will strip extra whitespace for us.
				$filtered = wp_strip_all_tags( $filtered, true );
			} else {
				$filtered = trim( preg_replace('/[\r\n\t ]+/', ' ', $filtered) );
			}

			$found = false;
			while ( preg_match('/%[a-f0-9]{2}/i', $filtered, $match) ) {
				$filtered = str_replace($match[0], '', $filtered);
				$found = true;
			}

			if ( $found ) {
				// Strip out the whitespace that may now exist after removing the octets.
				$filtered = trim( preg_replace('/ +/', ' ', $filtered) );
			}

			/**
	         * Filter a sanitized text field string.
	         *
	         * @since 2.9.0
	         *
	         * @param string $filtered The sanitized string.
	         * @param string $str      The string prior to being sanitized.
	         */
			return apply_filters( 'sanitize_text_field', $filtered, $str );
		}

		public function strtolower( $str = null, $encoding = 'UTF-8' ) {
			$strtolower               = function_exists( 'mb_strtolower' ) ? mb_strtolower( $str, $encoding ) : strtolower( $str );
			return $strtolower;
		}

		public function strtoupper( $directory = null, $encoding = 'UTF-8' ) {
			$strtoupper               = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $str, $encoding ) : strtoupper( $str );
			return $strtoupper;
		}

		public function sanitize_text_strtoupper( $directory = null, $mode = true) {
			if (seems_utf8($directory)) {
				if($mode) {
					if (function_exists('mb_strtoupper')) {
						$directory = mb_strtoupper($directory, 'UTF-8');
					}
				}
				else
				{
					if (function_exists('mb_strtolower')) {
						$directory = mb_strtolower($directory, 'UTF-8');
					}
				}
				$directory = utf8_uri_encode($directory, 200);
			}
			if($mode) {
				$directory = strtoupper($directory);
			}
			else
			{
				$directory = strtolower($directory);
			}
			$directory = preg_replace('/&.+?;/', '', $directory); // kill entities
			$directory = str_replace('-', '_', $directory);
			return $directory;
		}
		
		public function sanitize_text_strtolower( $directory = null, $mode = true) {
			if (seems_utf8($directory)) {
				if($mode) {
					if (function_exists('mb_strtoupper')) {
						$directory = mb_strtoupper($directory, 'UTF-8');
					}
				}
				else
				{
					if (function_exists('mb_strtolower')) {
						$directory = mb_strtolower($directory, 'UTF-8');
					}
				}
				$directory = utf8_uri_encode($directory, 200);
			}
			if($mode) {
				$directory = strtoupper($directory);
			}
			else
			{
				$directory = strtolower($directory);
			}
			$directory = preg_replace('/&.+?;/', '', $directory); // kill entities
			$directory = str_replace('-', '_', $directory);
			return $directory;
		}
	}
} // class exists