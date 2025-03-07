<?php
/**
 * @package WordPress
 * @subpackage Traveler
 * @since 1.0
 *
 * ST Required Plugins
 *
 * Created by ShineTheme
 *
 */

if(!class_exists('STRequiredPlugins'))
{
    class STRequiredPlugins{


        function __construct()
        {
            add_action('tgmpa_register',array($this,'st_register_required_plugins'));
        }

        function st_register_required_plugins()
        {
            
            $plugins = array(
                
                array(
                    'name'      => 'Contact Form 7',
                    'slug'      => 'contact-form-7',
                    'required'  => true,
                ),
                array(
                    'name'      => 'Advanced Editor Tools',
                    'slug'      => 'tinymce-advanced',
                    'required'  => false,
                ),
                array(
                    'name'      => 'Traveler code',
                    'slug'      => 'traveler-code',
                    'required'  => true,
                    'source'     => 'http://shinetheme.com/demosd/plugins/traveler/traveler-code.zip',
                    'version'     => "3.0.8",
                ),
                array(
                    'name'               => 'Option Tree', // The plugin name.
                    'slug'               => 'st-option-tree', // The plugin slug (typically the folder name).
                    'source'                => 'http://shinetheme.com/demosd/plugins/traveler/st-option-tree.zip', // The plugin source.
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required.
                    'version'            => "2.7.4.2",
                ),
                array(
                    'name'      =>'MailChimp for WordPress',
                    'slug'      =>'mailchimp-for-wp',
                    'required'  =>true
                ),
                array(
                    'name'      => 'Woocommerce',
                    'slug'      => 'woocommerce',
                    'required'  => false,
                ),
                array(
                    'name'      => 'FOX – Currency Switcher Professional for WooCommerce',
                    'slug'      => 'woocommerce-currency-switcher',
                    'required'  => false,
                ),
            );
            array_push($plugins,
                array(
                    'name'               => 'Elementor Website Builder', // The plugin name.
                    'slug'               => 'elementor', // The plugin slug (typically the folder name).
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required.
                )
            );
            array_push($plugins,
                array(
                    'name'               => 'WPBakery Visual Composer', // The plugin name.
                    'slug'               => 'js_composer', // The plugin slug (typically the folder name).
                    'source'                => 'http://shinetheme.com/demosd/plugins/traveler/js_composer.zip', // The plugin source.
                    'required'           => true, // If false, the plugin is only 'recommended' instead of required.
                    'version'            => "6.13.0",
                )
            );
          

            /**
             * Array of configuration settings. Amend each line as needed.
             * If you want the default strings to be available under your own theme domain,
             * leave the strings uncommented.
             * Some of the strings are added into a sprintf, so see the comments at the
             * end of each line for what each argument will be.
             */
            $config = array(
                'default_path' => '',                      // Default absolute path to pre-packaged plugins.
                'menu'         => 'tgmpa-install-plugins', // Menu slug.
                'has_notices'  => true,                    // Show admin notices or not.
                'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
                'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
                'is_automatic' => false,                   // Automatically activate plugins after installation or not.
                'message'      => '',                      // Message to output right before the plugins table.
                'strings'      => array(
                    'page_title'                      => __( 'Install Required Plugins', 'traveler' ),
                    'menu_title'                      => __( 'Install Plugins', 'traveler' ),
                    'installing'                      => __( 'Installing Plugin: %s', 'traveler' ), // %s = plugin name.
                    'oops'                            => __( 'Something went wrong with the plugin API.', 'traveler' ),
                    'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'traveler' ), // %1$s = plugin name(s).
                    'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'traveler' ), // %1$s = plugin name(s).
                    'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'traveler' ),
                    'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins', 'traveler' ),
                    'return'                          => __( 'Return to Required Plugins Installer', 'traveler' ),
                    'plugin_activated'                => __( 'Plugin activated successfully.', 'traveler' ),
                    'complete'                        => __( 'All plugins installed and activated successfully. %s', 'traveler' ), // %s = dashboard link.
                    'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
                )
            );
            tgmpa( $plugins, $config );
        }
    }
}

new STRequiredPlugins();