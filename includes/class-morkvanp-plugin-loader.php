<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link        http://morkva.co.ua/
 * @since       1.0.0
 *
 * @package     morkvanp-plugin
 * @subpackage  morkvanp-plugin/includes
 */
/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    morkvanp-plugin
 * @subpackage morkvanp-plugin/includes
 * @author     MORKVA <hello@morkva.co.ua>
 */

require("class-morkvanp-plugin-callbacks.php");

class MNP_Plugin_Loader {
	
	/**
	 * The array of pages for plugin menu
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $pages 	Pages for plugin menu
	 */
	protected $pages;

	/**
	 * The array of subpages for plugin menu
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $subpages 	Subpages for plugin menu
	 */
	protected $subpages;

	/**
	 * Array of settings groups fields for plugin
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $settings 
	 */
	protected $settings;

	/**
	 * Array of sections for settings fields for plugin
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $sections
	 */
	protected $sections;

	/**
	 * Array of fields for settings fields for plugin
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array $fields
	 */
	protected $fields;

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;
	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Object of callbacks class
	 *
	 * @since 	1.0.0
	 * @access  protected
	 * @var 	string $callbacks 		Class of callbacks
	 */
	protected $callbacks;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $wp_settings_sections;
		$this->actions = array();
		$this->filters = array();
		$this->pages = array();
		$this->subpages = array();
		$this->settings = array();
		$this->sections = array();
		$this->fields = array();

		$this->callbacks = new MNP_Plugin_Callbacks();

		$this->add_settings_fields();
		$this->register_fields_sections();
		$this->register_settings_fields();

		$this->register_menu_pages();
		$this->register_menu_subpages();

		add_action( 'admin_menu', array( $this, 'register_plugin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'mv_add_meta_boxes' ) );
		add_action( 'admin_init', array( $this, 'register_plugin_settings' ));
	}
	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}
	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}
	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);
		return $hooks;
	}
	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

	/**
	 * Registering plugin pages to menu
	 *
	 * @since 	1.0.0
	 */
	public function register_menu_pages()
	{
		$this->pages = array(
			array(
				'page_title' => __('Nova Poshta TTN', 'textdomain'), 
				'menu_title' => 'Nova Poshta TTN', 
				'capability' => 'manage_options', 
				'menu_slug' => 'morkvanp_plugin', 
				'callback' => array($this, 'add_settings_page'), 
				'icon_url' => plugins_url("nova_poshta_25px.png", __FILE__), 
				'position' => 60
			)
		);

		return $this;
	}

	/**
	 *	Add Plugin Settings page
	 *
	 *	@since 	1.0.0
	 */
	public function add_settings_page()
	{
		require_once(get_home_path() . 'wp-content/plugins/nova-poshta-ttn/public/partials/morkvanp-plugin-settings.php');
	}

	/**
	 * Registering subpages for menu of plugin
	 *
	 * @since 	1.0.0
	 */
	public function register_menu_subpages()
	{
		$title = "Налаштування";

		$this->subpages = array(
			array(
				'parent_slug'	=> 'morkvanp_plugin',
				'page_title'	=> 'Налаштування',
				'menu_title'	=> 'Налаштування',
				'capability'	=> 'manage_options',
				'menu_slug'		=> 'morkvanp_plugin',
				'callback'		=> array( $this, 'add_settings_page' )
			),
			array(
				'parent_slug' 	=> 'morkvanp_plugin', 
				'page_title' 	=> 'Створити Накладну', 
				'menu_title' 	=> 'Створити Накладну', 
				'capability' 	=> 'manage_options', 
				'menu_slug' 	=> 'morkvanp_invoice', 
				'callback' 		=>  array( $this, 'add_invoice_page' )
			),
			/*array(
				'parent_slug' => 'morkvanp_plugin', 
				'page_title' => 'Nova Poshta TTN', 
				'menu_title' => ($title) ? $title : $pages['menu_title'], 
				'capability' => 'manage_options', 
				'menu_slug' => 'morkvanp_plugin', 
				'callback' => array($this, 'add_settings_page')
			)*/
		);

		return $this;		
	}

	/**
	 * Adding subpage o plugin
	 *
	 * @since 1.0.0
	 */
	public function add_invoice_page()
	{
		require_once(get_home_path() . 'wp-content/plugins/nova-poshta-ttn/public/partials/morkvanp-plugin-form.php');
	}

	/**
	 * Register plugin menu
	 *
	 * @since 	1.0.0
	 */
	public function register_plugin_menu()
	{
		foreach ($this->pages as $page) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}

		foreach ($this->subpages as $subpage ) {
			add_submenu_page( $subpage['parent_slug'], $subpage['page_title'], $subpage['menu_title'], $subpage['capability'], $subpage['menu_slug'], $subpage['callback'] );
		}
	}

	/**
	 * Add setting fields for plugin
	 *
	 * @since 	1.0.0
	 */
	public function add_settings_fields()
	{
		$args = array(
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'text_example'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'city'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'names'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'region'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'activate_plugin'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'phone'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'flat'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'warehouse'
			)
		);

		$this->settings = $args;

		return $this;
	}

	/**
	 *	Register all sections for settings fields
	 *
	 *	@since 	 1.0.0
	 */
	public function register_fields_sections()
	{
		$args = array(
			array(
				'id' => 'morkvanp_admin_index',
				'title' => 'Налаштування',
				'callback' => function() { echo ""; },
				'page' => 'morkvanp_plugin'
			)
		);

		$this->sections = $args;

		return $this;
	}

	/**
	 * Register settings callbacks fields 
	 *
	 * @since 	1.0.0
	 */
	public function register_settings_fields()
	{
		$args = array(
			array(
				'id' => 'activate_plugin',
				'title' => 'Активувати плагін?',
				'callback' => array( $this->callbacks, 'morkvanpActivate' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'activate_plugin'
				)
			),
			array(
				'id' => 'text_example',
				'title' => 'API ключ',
				'callback' => array( $this->callbacks, 'morkvanpTextExample' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'text_example',
					'class' => 'example-class'
				)
			),
			array(
				'id' => 'names',
				'title' => 'Назва (П.І.Б. повністю) Відправника',
				'callback' => array( $this->callbacks, 'morkvanpNames' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'names',
					'class' => 'names'
				)
			),
			array(
				'id' => 'region',
				'title' => 'Область',
				'callback' => array( $this->callbacks, 'morkvanpSelectRegion' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'region',
					'class' => 'region'
				)
			),
			array(
				'id' => 'city',
				'title' => 'Місто',
				'callback' => array( $this->callbacks, 'morkvanpSelectCity' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'city',
					'class' => 'city'
				)
			),
			array(
				'id' => 'phone',
				'title' => 'Номер телефону',
				'callback' => array( $this->callbacks, 'morkvanpPhone' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'phone',
					'class' => 'phone'
				)
			),
			array(
				'id' => 'warehouse',
				'title' => 'Адреса Віділення',
				'callback' => array( $this->callbacks, 'morkvanpWarehouseAddress'),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'warehouse',
					'class' => 'warehouse'
				)
			)
		);

		$this->fields = $args;

		return $this;
	}

	/**
	 *	Registering all settings fields for plugin
	 *
	 *	@since 	 1.0.0
	 */
	public function register_plugin_settings()
	{
		foreach ($this->settings as $setting) {
			register_setting( $setting["option_group"], $setting["option_name"], ( isset( $setting["callback"] ) ? $setting["callback"] : '' ) );
		}

		foreach ($this->sections as $section) {
			add_settings_section( $section["id"], $section["title"], ( isset( $section["callback"] ) ? $section["callback"] : '' ), $section["page"] );
		}

		foreach ($this->fields as $field) {
			add_settings_field( $field["id"], $field["title"], ( isset( $field["callback"] ) ? $field["callback"] : '' ), $field["page"], $field["section"], ( isset( $field["args"] ) ? $field["args"] : '' ) );
		}
	}

	/**
	 * Add meta box to WooCommerce order's page
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_meta_box()
	{
		 session_start();

        if ( isset($_GET["post"]) ) { $order_id = $_GET["post"]; }
        if ( isset($order_id) ) { 
            $order_data = wc_get_order( $order_id );
            $order = $order_data->get_data();
            $_SESSION['order_data'] = $order;
            $_SESSION['order_id'] = $order_id;
        }

        echo "<img src='https://apimgmtstorelinmtekiynqw.blob.core.windows.net/content/MediaLibrary/Logo/logo-hor-ua.png' height='50' width='250' />";
        echo "<a class='button button-primary send' href='admin.php?page=morkvanp_invoice'>Створити експрес-накладну</a>";
	}

	/**
	 * Generating meta box
	 *
	 * @since 1.0.0
	 */
	public function mv_add_meta_boxes()
    {
        add_meta_box( 'mv_other_fields', __('Створити накладну','woocommerce'), array( $this, 'add_plugin_meta_box' ), 'shop_order', 'side', 'core' );
    }
}
