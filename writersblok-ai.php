<?php

/**
 * @link              https://writersblok.ai
 * @since             1.0.0
 * @package           writers-blok-ai
 *
 * @wordpress-plugin
 * Plugin Name:       WritersBlok AI
 * Plugin URI:        https://wordpress.org/plugins/writersblok-ai/
 * Description:       An AI powered writing assistant to help you quickly create amazing content and defeat your writers block. 
 * Version:           1.4
 * Author:            WritersBlok
 * Author URI:        https://writersblok.ai/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       writersblokai
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WRITERSBLOK_VERSION', '1.4' );
/**
 * Freemius SDK
 * 
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'wpwrtblk_wa_fs' ) ) {
    wpwrtblk_wa_fs()->set_basename( false, __FILE__ );
    require_once plugin_dir_path( __FILE__ ) . '/classes/class-create-settings-routes.php';
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    
    if ( !function_exists( 'wpwrtblk_wa_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wpwrtblk_wa_fs()
        {
            global  $wpwrtblk_wa_fs ;
            
            if ( !isset( $wpwrtblk_wa_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
                $wpwrtblk_wa_fs = fs_dynamic_init( array(
                    'id'              => '11772',
                    'plugin_id'       => '11772',
                    'slug'            => 'writersblok-ai',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_00a2bbf340efc4e3cabb7b43ca836',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Premium',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'trial'           => array(
                    'days'               => 7,
                    'is_require_payment' => true,
                ),
                    'has_affiliation' => 'all',
                    'menu'            => array(
                    'slug'       => 'wpwrtblk',
                    'first-path' => 'admin.php?page=wpwrtblk',
                ),
                    'is_live'         => true,
                ) );
            }
            
            return $wpwrtblk_wa_fs;
        }
        
        // Init Freemius.
        wpwrtblk_wa_fs();
        // Signal that SDK was initiated.
        do_action( 'wpwrtblk_wa_fs_loaded' );
        function wpwrtblk_wa_fs_custom_connect_message_on_update(
            $message,
            $user_first_name,
            $plugin_title,
            $user_login,
            $site_link,
            $freemius_link
        )
        {
            return sprintf(
                __( 'Hey %1$s' ) . ',<br><br>' . __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'writersblok-ai' ),
                $user_first_name,
                '<b>' . $plugin_title . '</b>',
                '<b>' . $user_login . '</b>',
                $site_link,
                $freemius_link
            );
        }
        
        wpwrtblk_wa_fs()->add_filter(
            'connect_message_on_update',
            'wpwrtblk_wa_fs_custom_connect_message_on_update',
            10,
            6
        );
    }
    
    // ... Your plugin's main file logic ...
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-writersblok-activator.php
     */
    function activate_writersblok()
    {
        require_once plugin_dir_path( __FILE__ ) . '/classes/class-writersblok-activator.php';
        Writersblok_Activator::activate();
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-writersblok-deactivator.php
     */
    function deactivate_writersblok()
    {
        require_once plugin_dir_path( __FILE__ ) . '/classes/class-writersblok-deactivator.php';
        Writersblok_Deactivator::deactivate();
    }
    
    register_activation_hook( __FILE__, 'activate_writersblok' );
    register_deactivation_hook( __FILE__, 'deactivate_writersblok' );
    require_once plugin_dir_path( __FILE__ ) . '/classes/class-create-settings-routes.php';
    add_action( 'admin_menu', 'wpwrtblk_init_menu' );
    /**
     * Init Admin Menu.
     *
     * @return void
     */
    function wpwrtblk_init_menu()
    {
        add_menu_page(
            'WritersBlok AI',
            // page_title
            'WritersBlok AI',
            // menu_title
            'manage_options',
            // capability
            'wpwrtblk',
            // menu-slug
            'wpwrtblk_admin_page',
            // callback
            plugin_dir_url( __FILE__ ) . 'assets/images/icon.svg',
            // icon
            '2.1'
        );
    }
    
    /**
     * Init Admin Page.
     *
     * @return void
     */
    function wpwrtblk_admin_page()
    {
        require_once plugin_dir_path( __FILE__ ) . 'templates/app.php';
    }
    
    add_action( 'admin_enqueue_scripts', 'wpwrtblk_admin_enqueue_scripts' );
    /**
     * Enqueue scripts and styles.
     *
     * @return void
     */
    function wpwrtblk_admin_enqueue_scripts()
    {
        $deps = [
            'wp-plugins',
            'wp-element',
            'wp-edit-post',
            'wp-i18n',
            'wp-api-request',
            'wp-data',
            'wp-hooks',
            'wp-plugins',
            'wp-components',
            'wp-blocks',
            'wp-editor',
            'wp-compose'
        ];
        wp_enqueue_style( 'wpwrtblk-style', plugin_dir_url( __FILE__ ) . 'build/index.css' );
        wp_enqueue_script(
            'wpwrtblk-script',
            plugin_dir_url( __FILE__ ) . 'build/index.js',
            $deps,
            null,
            true
        );
        // if using default permalinks, change API url
        
        if ( get_option( 'permalink_structure' ) == "" ) {
            $api_url = home_url( '/?rest_route=' );
        } else {
            $api_url = home_url( '/wp-json' );
        }
        
        wp_add_inline_script( 'wpwrtblk-script', 'const wpwrtblkData = ' . json_encode( array(
            'apiUrl'            => $api_url,
            'adminUrl'          => home_url( '/wp-admin' ),
            'pluginDir'         => plugin_dir_url( __FILE__ ),
            'nonce'             => wp_create_nonce( 'wp_rest' ),
            'canUsePremiumCode' => wpwrtblk_wa_fs()->can_use_premium_code(),
            'currentPlan'       => wpwrtblk_wa_fs()->get_plan_name(),
        ) ), 'before' );
    }
    
    // set global vars
    define( 'WPWRTBLK_PLUGIN_ROOT', plugin_dir_url( __FILE__ ) );
    define( 'WPWRTBLK_CAN_USE_PREMIUM', wpwrtblk_wa_fs()->can_use_premium_code() );
    define( 'WPWRTBLK_CURRENT_PLAN', wpwrtblk_wa_fs()->get_plan_name() );
    // Register Metaboxes.
    function wpwrtblk_register_meta_fields()
    {
        register_meta( 'post', 'wpwrtblk__text_field', [
            'show_in_rest'      => true,
            'type'              => 'string',
            'single'            => true,
            'sanitize_callback' => 'sanitize_text_string',
            'auth_callback'     => function () {
            return current_user_can( 'edit_posts' );
        },
        ] );
    }
    
    add_action( 'init', 'wpwrtblk_register_meta_fields' );
    /* add settings link to plugins page */
    add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpwrtblk_settings_link' );
    function wpwrtblk_settings_link( $links )
    {
        // Build and escape the URL.
        $url = esc_url( add_query_arg( 'page', 'wpwrtblk', get_admin_url() . 'admin.php' ) );
        // Create the link.
        $settings_link = "<a href='{$url}'>" . __( 'Settings' ) . '</a>';
        // Adds the link to the end of the array.
        array_push( $links, $settings_link );
        return $links;
    }
    
    /* redirect to setting page after activation */
    function wpwrtblk_activation_redirect( $plugin )
    {
        if ( $plugin == plugin_basename( __FILE__ ) ) {
            exit( wp_redirect( admin_url( 'admin.php?page=wpwrtblk' ) ) );
        }
    }
    
    add_action( 'activated_plugin', 'wpwrtblk_activation_redirect' );
}

require_once plugin_dir_path( __FILE__ ) . '/classes/class-create-settings-routes.php';