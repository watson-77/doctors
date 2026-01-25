<?php
/**
 * WordPress Translation Loader for apicoder
 * 
 * Place this file in your theme or plugin folder
 * Add this to your functions.php or main plugin file:
 * require_once __DIR__ . '/load-translations-apicoder.php';
 * 
 * Generated: <?php echo date('Y-m-d H:i:s'); ?>
 */

if (!function_exists('load_apicoder_translations')) {
    /**
     * Load translations for apicoder
     */
    function load_apicoder_translations() {
        $locale = apply_filters('plugin_locale', get_locale(), 'apicoder');
        $mofile = "apicoder-$locale.mo";
        
        // Try to load from theme languages folder
        $theme_path = get_template_directory() . '/languages/' . $mofile;
        if (file_exists($theme_path)) {
            load_textdomain('apicoder', $theme_path);
            return;
        }
        
        // Try to load from plugin languages folder
        $plugin_path = plugin_dir_path(__FILE__) . 'languages/' . $mofile;
        if (file_exists($plugin_path)) {
            load_textdomain('apicoder', $plugin_path);
            return;
        }
        
        // Try to load from default WordPress languages folder
        $wp_path = WP_LANG_DIR . '/plugins/' . $mofile;
        if (file_exists($wp_path)) {
            load_textdomain('apicoder', $wp_path);
            return;
        }
        
        // Load from current directory (for testing)
        $current_path = __DIR__ . '/apicoder-ru_RU.mo';
        if (file_exists($current_path)) {
            load_textdomain('apicoder', $current_path);
        }
    }
    
    // Hook into WordPress
    add_action('init', 'load_apicoder_translations');
    
    /**
     * Get translation function for apicoder
     */
    if (!function_exists('__apicoder')) {
        function __apicoder($text, $domain = 'apicoder') {
            return __($text, $domain);
        }
    }
    
    if (!function_exists('_eapicoder')) {
        function _eapicoder($text, $domain = 'apicoder') {
            _e($text, $domain);
        }
    }
    
    if (!function_exists('_xapicoder')) {
        function _xapicoder($text, $context, $domain = 'apicoder') {
            return _x($text, $context, $domain);
        }
    }
}
