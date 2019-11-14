<?php
/**
 *  @copyright 2017  Cloudways  https://www.cloudways.com
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Retrieve site options accounting for settings inheritance.
 *
 * @param string $option_name
 * @param bool   $is_local
 * @return array
 */
function breeze_get_option( $option_name, $is_local = false ) {
	$inherit = true;

	global $breeze_network_subsite_settings;

	if ( is_network_admin() && ! $breeze_network_subsite_settings ) {
		$is_local = false;
	} elseif ( ! breeze_does_inherit_settings() ) {
		$inherit = false;
	}

	if ( ! is_multisite() || $is_local || ! $inherit ) {
		$option = get_option( 'breeze_' . $option_name );
	} else {
		$option = get_site_option( 'breeze_' . $option_name );
	}

	if ( empty( $option ) || ! is_array( $option ) ) {
		$option = array();
	}

	return $option;
}

/**
 * Update site options accounting for multisite.
 *
 * @param string $option_name
 * @param mixed  $value
 * @param bool   $is_local
 */
function breeze_update_option( $option_name, $value, $is_local = false ) {
	if ( is_network_admin() ) {
		$is_local = false;
	}

	if ( ! is_multisite() || $is_local ) {
		update_option( 'breeze_' . $option_name, $value );
	} else {
		update_site_option( 'breeze_' . $option_name, $value );
	}
}

/**
 * Check whether current site should inherit network-level settings.
 *
 * @return bool
 */
function breeze_does_inherit_settings() {
	global $breeze_network_subsite_settings;

	if ( ! is_multisite() || ( ! $breeze_network_subsite_settings && is_network_admin() ) ) {
		return false;
	}

	$inherit_option = get_option( 'breeze_inherit_settings' );

	return '0' !== $inherit_option;
}

/**
 * Check if plugin is activated network-wide in a multisite environment.
 *
 * @return bool
 */
function breeze_is_active_for_network() {
	return is_multisite() && is_plugin_active_for_network( 'breeze/breeze.php' );
}

function breeze_is_supported( $check ) {
	switch ( $check ) {
		case 'conditional_htaccess':
			$return = isset( $_SERVER['SERVER_SOFTWARE'] ) && stripos( $_SERVER['SERVER_SOFTWARE'], 'Apache/2.4' ) !== false;
			break;
	}

	return $return;
}
