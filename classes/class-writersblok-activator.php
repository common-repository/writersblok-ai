<?php

/**
 * Fired during plugin activation
 *
 * @link       https://writersblok.ai
 * @since      1.0.0
 *
 * @package    Writersblok
 * @subpackage Writersblok/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Writersblok
 * @subpackage Writersblok/includes
 * @author     WritersBlok <john@writersblok.ai>
 */
class Writersblok_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        self::bootstrap();
	}
    
    /**
	 * Set the default api key
	 *	 
	 *
	 * @since    1.0.0
	 */
    public static function bootstrap(){ 
		if(get_option('wpwrtblk_api_key') === FALSE){
			add_option('wpwrtblk_api_key', 'enter api key');
		}
		
		if(get_option('wpwrtblk_api_settings') === FALSE){
			$api_options = array(
				'0' => 'gpt-3.5-turbo',		# model
				'1' => 3000, 				# tokens
				'2' => 1, 					# temp
				'3' => 1, 					# top_p
				'4' => 0, 					# presence_penalty
				'5' => 0, 					# frequency_penalty
				'6' => 1, 					# n
				'7' => 1 					# best_of
			);
			add_option('wpwrtblk_api_settings', $api_options);
		}   
    }

	
}
