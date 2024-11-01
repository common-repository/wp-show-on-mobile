<?php
/*
 * Plugin Name: WP Show On Mobile
 * Plugin URI: www.dogan-ucar.de/wp-show-on-mobile
 * Description: This plugin shows content on mobile/desktop user agents only. The 'show_on_mobile' and 'show_on_desktop' shortcodes are added with this plugin. This plugin shows a shortcode or a post/page content on mobile/desktop user agent.
 * Version: 1.0.0
 * Author: Dogan Ucar
 * Author URI: www.dogan-ucar.de
 * License: GNU General Public License v3.0
 *
 * Copyright (C) <2016> <Dogan Ucar>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For further questions please visit www.dogan-ucar.de/wp-sc-mobile
 *
 */

// Adds a hook for a shortcode tag.
// More details: https://codex.wordpress.org/Function_Reference/add_shortcode
add_shortcode ( 'show_on_mobile', 'show_content_on_mobile' );
add_shortcode ( 'show_on_desktop', 'show_content_on_desktop' );

/**
 * This function executes a shortcode if the user agent is a mobile device.
 * Requesting the user agent is done by the wp_is_mobile() function.
 * The function passes the text to showContent() function if the user agent
 * is mobile. If the showContent() returns a text, the text is returned to WordPress.
 * Otherwise it returns an empty string.
 *
 * @param unknown $atts        	
 * @param unknown $text        	
 * @return boolean/string
 */
function show_content_on_mobile($atts, $text) {
	$mobile = isMobile ();
	logDebug ( __FUNCTION__, "isMobile() = " . $mobile );
	if (! $mobile) {
		return false;
	}
	logDebug ( __FUNCTION__, "text = " . $text );
	$content = showContent ( $text );
	if ($content) {
		return $content;
	} else {
		return "";
	}
}
/**
 * This function executes a shortcode if the user agent is a desktop device (not mobile).
 * Requesting the user agent is done by the wp_is_mobile() function.
 * The function passes the text to showContent() function if the user agent
 * is a desktop agent (mobile). If the showContent() returns a text, the text is returned to WordPress.
 * Otherwise it returns an empty string.
 *
 * @param unknown $atts        	
 * @param unknown $shortcode        	
 * @return boolean/string
 */
function show_content_on_desktop($atts, $text) {
	$mobile = isMobile ();
	logDebug ( __FUNCTION__, "isMobile() = " . $mobile );
	if ($mobile) {
		return false;
	}
	logDebug ( __FUNCTION__, "text = " . $text );
	$content = showContent ( $text );
	return $content;
}
/**
 * This function executes a shortcode, if $text contains a shortcode.
 * If $text does not contain a shortcode, the text will be returned.
 * The shortcode has to be registered with 'add_shortcode()' before.
 * If this is not the case, the function returns false. If $text is
 * empty, the function returns false. If any of the conditions does
 * not match, the function returns also false.
 *
 * @param unknown $text        	
 * @return boolean/string
 */
function showContent($text) {
	$trimmedText = trim ( $text );
	logDebug ( __FUNCTION__, "trimmedText = " . $trimmedText );
	if ($trimmedText == "") {
		return false;
	}
	$pattern = get_shortcode_regex ();
	logDebug ( __FUNCTION__, "pattern = " . $pattern );
	if (preg_match_all ( '/' . $pattern . '/s', $text, $matches ) && array_key_exists ( 2, $matches )) {
		logDebug ( __FUNCTION__, "$text contains a shortcode" );
		$shortcode = $matches [2] [0];
		if (isShortcode ( $shortcode )) {
			logDebug ( __FUNCTION__, "$shortcode is a registered shortcode" );
			return do_shortcode ( $text );
		} else {
			logDebug ( __FUNCTION__, "$shortcode is not a registered shortcode" );
			return false;
		}
	} else {
		logDebug ( __FUNCTION__, "$text does not contain a shortcode. It will be echo'ed" );
		return $text;
	}
	return false;
}

/**
 * returns a boolean whether the user agent is mobile or not.
 * Inside the function there is the wp_is_mobile() used.
 */
function isMobile() {
	return wp_is_mobile ();
}
/**
 * returns a boolean whether $shortCode is a shortcode or not.
 * Inside the function there is the shortcode_exists() used.
 *
 * @param unknown $shortCode        	
 */
function isShortcode($shortCode) {
	return shortcode_exists ( $shortCode );
}

/**
 * logs an text if WP_DEBUG and WP_DEBUG_LOG is enabled.
 * The logfiles name is wp_show_on_mobile_log.log.
 *
 * This function is for debugging purposes only.
 *
 * @param unknown $caller        	
 * @param unknown $text        	
 */
function logDebug($caller, $text) {
	if (WP_DEBUG && WP_DEBUG_LOG) {
		$logDir = dirname ( __FILE__ ) . "/";
		$logFile = "wp_show_on_mobile_log.log";
		$dateTime = date ( "Y-m-d H:i:s" );
		$logText = "$dateTime|$caller|$text\n";
		$logFileAbsPath = $logDir . $logFile;
		file_put_contents ( $logFileAbsPath, $logText, FILE_APPEND );
	}
}
?>
