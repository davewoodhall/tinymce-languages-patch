<?php

/*
Plugin Name: Tiny MCE language patches
Plugin URI:
Description: Correct missing translated locales for TinyMCE
Version: 1.0
Author: Duo Energie Graphique
Author URI: http://duoeg.com
License: MIT
*/



/* ====================================================================================================
 * NOTES
 * 		download i18n files (locales)
 * 			http://archive.tinymce.com/i18n/
 * ==================================================================================================== */

if(!defined('ABSPATH'))
	die('Direct file access denied!');



if(!class_exists('dw_patchTinyMCELanguages')) {
	class dw_patchTinyMCELanguages {
		private $path_to_plugin;
		private $path_to_languages;

		function __construct(){
			$this->path_to_plugin			= plugin_dir_path(__FILE__);
			$this->path_to_languages		= $this->path_to_plugin.'langs/';

			$this->getLanguageList();
		}

		/* ====================================================================================================
		 * Loop languages we want to include
		 * ==================================================================================================== */
		private function checkIfLanguagesExist($langs) {
			$includes = preg_replace('/wp-content$/', 'wp-includes', WP_CONTENT_DIR);
			$tinymce_dir = trailingslashit($includes).'js/tinymce/langs/';
			$path = $this->path_to_languages;

			$missing_languages = array();

			// Get list of languages included
			$tinymce_languages = array();
			foreach (scandir($tinymce_dir) as $supportedLanguage) {
				if( $this->fileHasExtension($supportedLanguage, '.js') ) {
					$shortname = substr($supportedLanguage,0,2);
					if($shortname != 'en' and !$this->fileHasExtension($supportedLanguage, 'en.js')) {
						$tinymce_languages[] = $shortname;
					}
				}
			}

			// Copy languages to included languages
			foreach($langs as $lang) {
				if(!in_array($lang,$tinymce_languages)) {
					$missing_languages[] = $lang;
				}
			}

			if(!empty($missing_languages)) {
				foreach($missing_languages as $ml) {
					$locale 	= substr($ml,0,2);
					$new_name	= $locale.'.js';

				//	wp_die( $path.$ml );				// /Applications/MAMP/htdocs/dw_tests/wordpress/wp-content/plugins/tinymce-french-patch/langs/fr_FR.js
				//	wp_die( $tinymce_dir.$new_name );	// /Applications/MAMP/htdocs/dw_tests/wordpress/wp-includes/js/tinymce/langs/fr.js

					copy( $path.$ml, $tinymce_dir.$new_name );
				}
			}
		}

		/* ====================================================================================================
		 * Loop languages we want to include
		 * ==================================================================================================== */
		private function getLanguageList(){
			$path = $this->path_to_languages;

			$files = array();
			foreach (scandir($path) as $file) {
				if( $this->fileHasExtension($file, '.js') ) {
					$files[] = $file;
				}
			}

			if(!empty($files)) {
				$this->checkIfLanguagesExist( $files );
			}
		}

		/* ====================================================================================================
		 * Check if a string ends with ".js" (or any defined)
		 * ==================================================================================================== */
		private function fileHasExtension($haystack, $needle='.js') {
			$length = strlen($needle);
			return $length === 0 || (substr($haystack, -$length) === $needle);
		}
	}
	$patch_languages = new dw_patchTinyMCELanguages();
}