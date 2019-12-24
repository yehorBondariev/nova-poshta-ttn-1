<?php

/**
 * Plugin Name: Woo Nova Poshta
 * Plugin URI: https://www.morkva.co.ua/woocommerce-plugins/avtomatychna-heneratsiia-nakladnykh-nova-poshta?utm_source=nova-poshta-ttn-pro
 * Description: Плагін 2-в-1: спосіб доставки Нова Пошта та генерація накладних Нова Пошта.
 * Version: 1.4.1
 * Author: MORKVA
 * Text Domain: morkvanp-plugin
 * Domain Path: /i18n/
 */

use plugins\NovaPoshta\classes\AjaxRoute;
use plugins\NovaPoshta\classes\base\ArrayHelper;
use plugins\NovaPoshta\classes\Calculator;
use plugins\NovaPoshta\classes\Checkout;
use plugins\NovaPoshta\classes\DatabaseScheduler;
use plugins\NovaPoshta\classes\Log;
use plugins\NovaPoshta\classes\base\Base;
use plugins\NovaPoshta\classes\base\Options;
use plugins\NovaPoshta\classes\Database;
use plugins\NovaPoshta\classes\DatabaseSync;
use plugins\NovaPoshta\classes\NovaPoshtaApi;

if ( ! defined( 'WPINC' ) ) {
    die;
}

function check_woo_shipping() {
    if ( is_plugin_active ( 'woo-shipping-for-nova-poshta/woo-shipping-for-nova-poshta.php' ) ) {
            $plugins = 'woo-shipping-for-nova-poshta/woo-shipping-for-nova-poshta.php';
            deactivate_plugins( $plugins, $silent = false, $network_wide = null );
    }
}
add_action( 'init', 'check_woo_shipping' );

if (!get_option('np_address_shpping_notuse')){
  require_once 'address_shipping_method.php';
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugData = get_plugin_data(__FILE__);
if ($plugData['Name'] == 'Woo Nova Poshta'){
  if(file_exists('freemius/freemiusimport.php') ){
    require_once 'freemius/freemiusimport.php';
  }

}
define( 'MNP_PLUGIN_VERSION', $plugData['Version'] );
define( 'MNP_PLUGIN_NAME', $plugData['Name'] );

define('NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR', trailingslashit(dirname(__FILE__)));
define('NOVA_POSHTA_TTN_SHIPPING_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('NOVA_POSHTA_TTN_SHIPPING_TEMPLATES_DIR', trailingslashit(NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR . 'templates'));
define('NOVA_POSHTA_TTN_SHIPPING_CLASSES_DIR', trailingslashit(NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR . 'classes'));
define('NOVA_POSHTA_TTN_DOMAIN', untrailingslashit(basename(dirname(__FILE__))));
define('NOVA_POSHTA_TTN_SHIPPING_METHOD', 'nova_poshta_shipping_method');


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/autoload.php';

add_action('wp_ajax_my_actionfogetnpshippngcost', 'my_actionfogetnpshippngcost_callback');
add_action('wp_ajax_nopriv_my_actionfogetnpshippngcost', 'my_actionfogetnpshippngcost_callback');

function my_actionfogetnpshippngcost_callback() {
    global $woocommerce;

    $weight_total = $woocommerce->cart->cart_contents_weight;
    $weight_unit  =  get_option('woocommerce_weight_unit');
    $weightarray = array(
        'g' => 0.001,
        'kg' => 1,
        'lbs' => 0.45359,
        'oz' => 0.02834
    );

    foreach ( $weightarray as $unit => $value ) {
      if( $unit == $weight_unit){
            $weight_total = $weight_total * $value;
        }
    }
    if($weight_total < 0.5){
        $weight_total = 0.5;
    }
    $total = intval($woocommerce->cart->total);
    $shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
    $sender_city = $shipping_settings["city"];//old settings
    if(!empty( get_option( 'woocommerce_nova_poshta_shipping_method_city' )) )
    {
      $sender_city = get_option( 'woocommerce_nova_poshta_shipping_method_city' );
    }

    $c2 = $_POST['c2'] ;
    $cod = $_POST['cod'];

    $codarray = array("CargoType" => "Money",   "Amount" => $total);

    $methodProperties = array("CitySender" => $sender_city,"CityRecipient" => $c2,"Weight" => $weight_total,"ServiceType" => "WarehouseWarehouse","Cost" => $total,"SeatsAmount" => "1" );

    if($cod == 'checked'){
        $methodProperties['RedeliveryCalculate'] = $codarray;
    }

    $costs = array("modelName" => "InternetDocument","calledMethod" => "getDocumentPrice","methodProperties" => $methodProperties,"apiKey" => get_option('text_example'));

    $curl = curl_init();

    $url = "https://api.novaposhta.ua/v2.0/json/";

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => True,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($costs),

        CURLOPT_HTTPHEADER => array("content-type: application/json",),
    ));

    $response = curl_exec( $curl );
    $obj = json_decode( $response, true );

    $err = curl_error( $curl );
    curl_close( $curl );
    //
     if ( ($err) || ( !get_option('show_calc')) ) {
        echo '01234';//signal to stop calculating injection
     }
        else {
        $obj = json_decode( $response, true );
        $echovar = 0;
        $echovar += $obj["data"][0]["Cost"];
        $echovar += $obj["data"][0]["CostRedelivery"];
            echo $echovar;
     }
 wp_die();
}
add_action('wp_ajax_my_actionfogetnpshippngcities', 'my_actionfogetnpshippngcities_callback');
add_action('wp_ajax_nopriv_my_actionfogetnpshippngcities', 'my_actionfogetnpshippngcities_callback');

function my_actionfogetnpshippngcities_callback() {
  global $wpdb;
  $likea = $_GET['q'];
  $obl = $_GET['d'];
  $rulocale = false;
  if( get_locale()== NovattnPoshta::LOCALE_RU){
    $rulocale=true;
  }
  if(!$rulocale){
    $stringsql = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."nova_poshta_city` Where description like '".$likea."%'");
    if( isset($obl) && !empty($obl) ){
        $stringsql = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."nova_poshta_city` Where description like '".$likea."%' AND parent_ref='".$obl."'");
    }
  }
  else{
    $stringsql = $wpdb->prepare("SELECT description_ru as description,ref FROM `".$wpdb->prefix."nova_poshta_city` Where description_ru like '".$likea."%'");
    if( isset($obl) && !empty($obl) ){
        $stringsql = $wpdb->prepare("SELECT description_ru as description,ref FROM `".$wpdb->prefix."nova_poshta_city` Where description_ru like '".$likea."%' AND parent_ref='".$obl."'");
    }
  }

  $a=$wpdb->get_results( $stringsql , ARRAY_A );
  //$a = array('1' => "2" );
  wp_send_json( $a );
 //wp_die();
}



if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

function request_a_shipping_quote_init() {
if ( ! class_exists( 'WC_NovaPoshta_Shipping_Method' ) ) {


}
}
add_action( 'woocommerce_shipping_init', 'request_a_shipping_quote_init' );

function request_shipping_quote_shipping_method( $methods ) {
$methods['nova_poshta_shipping_method'] = 'WC_NovaPoshta_Shipping_Method';

return $methods;
}
add_filter( 'woocommerce_shipping_methods', 'request_shipping_quote_shipping_method' );

}

///////start class



class NovattnPoshta extends Base
{
    const LOCALE_RU = 'ru_RU';

    /**
     * Register main plugin hooks
     */

    public function init()
    {
        register_activation_hook(__FILE__, array($this, 'activatePlugin'));
        register_deactivation_hook(__FILE__, array($this, 'deactivatePlugin'));

        if ($this->isWoocommerce()) {
            //general plugin actions
            add_action('init', array(AjaxRoute::getClass(), 'init'));
            add_action('admin_init', array(new DatabaseScheduler(), 'ensureSchedule'));
            add_action('plugins_loaded', array($this, 'checkDatabaseVersion'));
            add_action('plugins_loaded', array($this, 'loadPluginDomain'));
            add_action('wp_enqueue_scripts', array($this, 'scripts'));
            add_action('wp_enqueue_scripts', array($this, 'styles'));
            add_action('admin_enqueue_scripts', array($this, 'adminScripts'));
            add_action('admin_enqueue_scripts', array($this, 'adminStyles'));

            //register new shipping method
            add_action('woocommerce_shipping_init', array($this, 'initNovaPoshtaShippingMethod'));
            add_filter('woocommerce_shipping_methods', array($this, 'addNovaPoshtaShippingMethod'));

            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'pluginActionLinks'));

            Checkout::instance()->init();
            Calculator::instance()->init();
        }
    }

    /**
     * @return bool
     */
    public function isWoocommerce()
    {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
    }

    /**
     * @return bool
     */
    public function isCheckout()
    {
        return Checkout::instance()->isCheckout;
    }

    /**
     * This method can be used safely only after woocommerce_after_calculate_totals hook
     * when $_SERVER['REQUEST_METHOD'] == 'GET'
     *
     * @return bool
     */
    public function isNPttn()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $sessionMethods = WC()->session->chosen_shipping_methods;

        $chosenMethods = array();
        if ($this->isPost() && ($postMethods = (array)ArrayHelper::getValue($_POST, 'shipping_method', array()))) {
            $chosenMethods = $postMethods;
        } elseif (isset($sessionMethods) && count($sessionMethods) > 0) {
            $chosenMethods = $sessionMethods;
        }
        return in_array(NOVA_POSHTA_TTN_SHIPPING_METHOD, $chosenMethods);
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return !$this->isPost();
    }

    /**
     * Enqueue all required scripts
     */



    public function scripts()
 {
     $suffix = '.min.js';
     $fileName = 'assets/js/nova-poshta' . $suffix;
     wp_register_script(
         'nova-poshta-js',
         NOVA_POSHTA_TTN_SHIPPING_PLUGIN_URL . $fileName,
         ['jquery-ui-autocomplete'],
         filemtime(NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR . $fileName)
     );
// wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css' );
// wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js');
// wp_enqueue_script( 'select2' );


     $this->localizeHelper('nova-poshta-js');

     wp_enqueue_script('nova-poshta-js');
 }

    /**
     * Enqueue all required styles
     */
    public function styles()
    {
        global $wp_scripts;
        $jquery_version = isset($wp_scripts->registered['jquery-ui-core']->ver) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
        wp_register_style('jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version);
        wp_enqueue_style('jquery-ui-style');

                wp_register_style('np-frontend-style',  NOVA_POSHTA_TTN_SHIPPING_PLUGIN_URL.'/assets/css/frontend.css', array(), $jquery_version);
        wp_enqueue_style('np-frontend-style');

    }

    /**
     * Enqueue all required styles for admin panel
     */
    public function adminStyles()
    {
        $suffix = $this->isDebug() ? '.css' : '.min.css';
        $fileName = 'assets/css/style' . $suffix;
        wp_register_style('nova-poshta-style',
            NOVA_POSHTA_TTN_SHIPPING_PLUGIN_URL . $fileName,
            ['jquery-ui-style'],
            filemtime(NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR . $fileName)
        );
        wp_enqueue_style('nova-poshta-style');
    }

    /**
     * Enqueue all required scripts for admin panel
     */
    public function adminScripts()
    {
        $suffix = $this->isDebug() ? '.js' : '.min.js';
        $fileName = 'assets/js/nova-poshta-admin' . $suffix;
        wp_register_script(
            'nova-poshta-admin-js',
            NOVA_POSHTA_TTN_SHIPPING_PLUGIN_URL . $fileName,
            ['jquery-ui-autocomplete'],
            filemtime(NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR . $fileName)
        );

        $this->localizeHelper('nova-poshta-admin-js');

        wp_enqueue_script('nova-poshta-admin-js');
    }

    /**
     * @param string $handle
     */
    public function localizeHelper($handle)
    {
        wp_localize_script($handle, 'NovaPoshtaHelper', [
            'ajaxUrl' => admin_url('admin-ajax.php', 'relative'),
                        'textforcostcalc' => 'Розрахунок вартості доставки',
                        'textforcostcalcru' => 'Расчет стомости доставки',
                        'textforcostcalcen' => 'Delivery cost calculation',
            'chooseAnOptionText' => __('Choose an option', NOVA_POSHTA_TTN_DOMAIN),
            'getRegionsByNameSuggestionAction' => AjaxRoute::GET_REGIONS_BY_NAME_SUGGESTION,
            'getCitiesByNameSuggestionAction' => AjaxRoute::GET_CITIES_BY_NAME_SUGGESTION,
            'getWarehousesBySuggestionAction' => AjaxRoute::GET_WAREHOUSES_BY_NAME_SUGGESTION,
            'getCitiesAction' => AjaxRoute::GET_CITIES_ROUTE,
            'getWarehousesAction' => AjaxRoute::GET_WAREHOUSES_ROUTE,
            'markPluginsAsRated' => AjaxRoute::MARK_PLUGIN_AS_RATED,
        ]);
    }

    /**
     * @param string $template
     * @param string $templateName
     * @param string $templatePath
     * @return string
     */
    public function locateTemplate($template, $templateName, $templatePath)
    {
        global $woocommerce;
        $_template = $template;
        if (!$templatePath)
            $templatePath = $woocommerce->template_url;

        $pluginPath = NOVA_POSHTA_TTN_SHIPPING_TEMPLATES_DIR . 'woocommerce/';

        // Look within passed path within the theme - this is priority
        $template = locate_template(array(
            $templatePath . $templateName,
            $templateName
        ));

        if (!$template && file_exists($pluginPath . $templateName)) {
            $template = $pluginPath . $templateName;
        }

        return $template ?: $_template;
    }

    /**
     * @param array $methods
     * @return array
     */
    public function addNovaPoshtaShippingMethod($methods)
    {
        $methods[] = 'WC_NovaPoshta_Shipping_Method';
        //$methods[] = 'WC_NovaPoshtaAddress_Shipping_Method';
        return $methods;
    }

    /**
     * Init NovaPoshta shipping method class
     */
    public function initNovaPoshtaShippingMethod()
    {
        if (!class_exists('WC_NovaPoshta_Shipping_Method')) {
            /** @noinspection PhpIncludeInspection */
            require_once NOVA_POSHTA_TTN_SHIPPING_PLUGIN_DIR . 'classes/WC_NovaPoshta_Shipping_Method.php';
        }
    }

    /**
     * Activation hook handler
     */
    public function activatePlugin()
    {
        Database::instance()->upgrade();
        DatabaseSync::instance()->synchroniseLocations();
    }

    /**
     * Deactivation hook handler
     */
    public function deactivatePlugin()
    {
        Database::instance()->downgrade();
        Options::instance()->clearOptions();
    }

    public function checkDatabaseVersion()
    {
        if (version_compare($this->pluginVersion, get_site_option('nova_poshta_db_version'), '>')) {
            Database::instance()->upgrade();
            DatabaseSync::instance()->synchroniseLocations();
            update_site_option('nova_poshta_db_version', $this->pluginVersion);
        }
    }

    /**
     * Register translations directory
     * Register text domain
     */
    public function loadPluginDomain()
    {
        $path = sprintf('./%s/i18n', NOVA_POSHTA_TTN_DOMAIN);
        load_plugin_textdomain(NOVA_POSHTA_TTN_DOMAIN, false, $path);
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->options->isDebug();
    }

    /**
     * @param array $links
     * @return array
     */
    public function pluginActionLinks($links)
    {
        $href = admin_url('admin.php?page=wc-settings&tab=shipping&section=' . NOVA_POSHTA_TTN_SHIPPING_METHOD);
        $settingsLink = sprintf('<a href="' . $href . '" title="%s">%s</a>', esc_attr(__('View Plugin Settings', NOVA_POSHTA_TTN_DOMAIN)), __('Settings', NOVA_POSHTA_TTN_DOMAIN));
        array_unshift($links, $settingsLink);
        return $links;
    }

    /**
     * @return Options
     */
    protected function getOptions()
    {
        return Options::instance();
    }

    /**
     * @return Log
     */
    protected function getLog()
    {
        return Log::instance();
    }

    /**
     * @return wpdb
     */
    protected function getDb()
    {
        global $wpdb;
        return $wpdb;
    }

    /**
     * @return NovaPoshtaApi
     */
    protected function getApi()
    {
        return NovaPoshtaApi::instance();
    }

    /**
     * @return string
     */
    protected function getPluginVersion()
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $pluginData = get_plugin_data(__FILE__);
        return $pluginData['Version'];
    }

    /**
     * @var NovattnPoshta
     */
    private static $_instance;

    /**
     * @return NovaPoshta
     */
    public static function instance()
    {
        if (static::$_instance == null) {
            static::$_instance = new static();
        }
        return static::$_instance;
    }

    /**
     * NovaPoshta constructor.
     *
     * @access private
     */
    private function __construct()
    {
    }

    /**
     * @access private
     */
    private function __clone()
    {
    }
}



///////finish

NovattnPoshta::instance()->init();


/**
 * @return NovattnPoshta
 */
function NPttn()
{
    return NovattnPoshta::instance();
}


define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-morkvanp-plugin-activator.php
 */
function activate_morkvanp_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-morkvanp-plugin-activator.php';
    MNP_Plugin_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-morkvanp-plugin-deactivator.php
 */
function deactivate_morkvanp_plugin() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-morkvanp-plugin-deactivator.php';
    MNP_Plugin_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_morkvanp_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_morkvanp_plugin' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-morkvanp-plugin.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_morkvanp_plugin() {
    $plugin = new MNP_Plugin();
    $plugin->run();
}
run_morkvanp_plugin();


function np_wc_add_my_account_orders_column( $columns ) {

    $new_columns = array();

    foreach ( $columns as $key => $name ) {

        $new_columns[ $key ] = $name;

        // add ship-to after order status column
        if ( 'order-status' === $key ) {
            $new_columns['order-ship-to'] = __( 'TTN', 'textdomain' );
        }
    }

    return $new_columns;
}

add_filter( 'woocommerce_my_account_my_orders_columns', 'np_wc_add_my_account_orders_column' );

function np_wc_my_orders_ship_to_column( $order ) {

        $outputdataid = get_post_meta( $order->get_id(), 'novaposhta_ttn', true );
        $link = '<a target="_blank" href=https://novaposhta.ua/tracking/?cargo_number='.$outputdataid.'>ТТН</a>';
        echo ! empty( $outputdataid ) ? $link : '–';

}
add_action( 'woocommerce_my_account_my_orders_column_order-ship-to', 'np_wc_my_orders_ship_to_column' );

add_action('woocommerce_thankyou', 'enroll_order', 10, 1);
function enroll_order( $order_id ) {
    if ( ! $order_id )
        return;

    // Allow code execution only once
        $meta_key = 'novaposhta_ttn';
    $meta_values = get_post_meta( $order_id,  $meta_key , true );

    if( (empty($meta_values)) && ( get_option('autoinvoice') ) ) {
        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );
                $message_note = 'Невдача';
                $path = PLUGIN_PATH . 'public/partials/invoice_auto.php';
                if(file_exists($path)){
                    require $path;
                    require PLUGIN_PATH . 'public/partials/functions.php';
                    $number = Invoice_auto::invoiceauto($order_id);
                    if($number > 0){
                        $message_note = $number;
                        $note = "ТТН: ".$message_note;
                        echo $note;
                        echo '<style>#nnnid{display:none;}</style>';
                    }
                }
              //$note = "Створення накладної під час замовлення: ".$message_note;
            //$order->add_order_note( $note );
            $order->save();
        }
    }
