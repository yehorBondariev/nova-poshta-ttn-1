<?php

/**
 * Fired during plugin activation
 *
 * @link       http://morkva.co.ua/
 * @since      1.0.0
 *
 * @package    morkvanp-plugin
 * @subpackage morkvanp-plugin/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    morkvanp-plugin
 * @subpackage morkvanp-plugin/includes
 * @author     MORKVA <hello@morkva.co.ua>
 */
class MNP_Plugin_Activator {
	/**
	 * The code that runs during plugin activation
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
       flush_rewrite_rules();
    }
}