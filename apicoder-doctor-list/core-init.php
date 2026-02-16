<?php 
/*
*	***** Apicoder Doctor list *****
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if
// Define Our Constants
define('ADL_CORE_INC',dirname( __FILE__ ).'/assets/inc/');
define('ADL_CORE_TEMPL',dirname( __FILE__ ).'/template/');
define('ADL_CORE_IMG',plugins_url( 'assets/img/', __FILE__ ));
define('ADL_CORE_CSS',plugins_url( 'assets/css/', __FILE__ ));
define('ADL_CORE_JS',plugins_url( 'assets/js/', __FILE__ ));
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PLUGIN_URL', plugins_url() );

require_once __DIR__ . '/load-translations-apicoder.php';
/*
*  Register CSS
*/
function adl_register_core_css(){
wp_enqueue_style('adl-core', ADL_CORE_CSS . 'adl-core.css',null,'1.0.1','all');
wp_enqueue_style('adl-core-bootstrap', "//cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css");
};
add_action( 'wp_enqueue_scripts', 'adl_register_core_css' );    
/*
*  Register JS/Jquery Ready
*/
function adl_register_core_js(){
// Register Core Plugin JS	
wp_enqueue_script('adl-core', ADL_CORE_JS . 'adl-core.js','jquery','1.0.1',true);
};
add_action( 'wp_enqueue_scripts', 'adl_register_core_js' );   

function load_cdn_scripts() {
    // Регистрация и подключение скрипта из CDN
    wp_register_script( 'jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'jquery' );
    wp_register_script( 'my-cdn-poper', 'https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'my-cdn-poper' );
    wp_register_script( 'my-cdn-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js', array(), '1.0.0', true );
    wp_enqueue_script( 'my-cdn-bootstrap' );
    // Регистрация и подключение стилей из CDN
    wp_enqueue_style( 'my-cdn-style', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css' );
}
add_action( 'wp_enqueue_scripts', 'load_cdn_scripts' );

/*
*  Includes
*/ 
// Load the registration cpt
if ( file_exists( ADL_CORE_INC . 'adl-core-reg.php' ) ) {
	require_once ADL_CORE_INC . 'adl-core-reg.php';
}
// Load the install-demo cpt
if ( file_exists( ADL_CORE_INC . 'install-demo.php' ) ) {
	require_once ADL_CORE_INC . 'install-demo.php';
}
// Load the registration template
if ( file_exists( ADL_CORE_INC . 'adl-page-template.php' ) ) {
	require_once ADL_CORE_INC . 'adl-page-template.php';
}
// Load the Functions
if ( file_exists( ADL_CORE_INC . 'adl-core-functions.php' ) ) {
	require_once ADL_CORE_INC . 'adl-core-functions.php';
}     
// Load the ajax Request
if ( file_exists( ADL_CORE_INC . 'adl-ajax-request.php' ) ) {
	require_once ADL_CORE_INC . 'adl-ajax-request.php';
} 
// Load the Shortcodes
if ( file_exists( ADL_CORE_INC . 'adl-shortcodes.php' ) ) {
	require_once ADL_CORE_INC . 'adl-shortcodes.php';
}
