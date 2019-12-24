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
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'woo_custom_column' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'woo_column_get_data' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_invoice_meta_box' ) );

		add_filter( 'wp_mail_from_name', array( $this, 'my_mail_from_name' ) );
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
				'page_title' => __( MNP_PLUGIN_NAME, 'textdomain'),
				'menu_title' => 'Woo Nova Poshta ',
				'capability' => 'manage_woocommerce',
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
		require_once( PLUGIN_PATH . '/public/partials/morkvanp-plugin-settings.php');
	}

	/**
	 * Registering subpages for menu of plugin
	 *
	 * @since 	1.0.0
	 */
	public function register_menu_subpages()
	{
		$title = "Налаштування";

		if( get_option( 'invoice_short' )){
			$this->subpages = array(
			array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Налаштування',
				'menu_title' 	=> 'Налаштування',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvanp_plugin',
				'callback' 		=> array( $this, 'add_settings_page' )
			),
			array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Створити Накладну',
				'menu_title' 	=> 'Створити Накладну',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvanp_invoice',
				'callback' 		=>  array( $this, 'add_invoice_page' )
			),
			array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Мої накладні',
				'menu_title'	=> 'Мої накладні',
				'capability'	=> 'manage_woocommerce',
				'menu_slug'		=> 'morkvanp_invoices',
				'callback'		=> array( $this, 'invoices_page' )
			),
			array(
				'parent_slug'	=> 'morkvanp_plugin',
				'page_title'	=> 'Про плагін',
				'menu_title'	=> 'Про плагін',
				'capability'	=> 'manage_woocommerce',
				'menu_slug'		=> 'morkvanp_about',
				'callback'		=> array( $this, 'about_page' )
			)

			,array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Шорткоди',
				'menu_title' 	=> 'Шорткоди',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvanp_short',
				'callback' 		=> array( $this, 'add_settings_page' )
			),
		);
		}
		else{
			$this->subpages = array(
			array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Налаштування',
				'menu_title' 	=> 'Налаштування',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvanp_plugin',
				'callback' 		=> array( $this, 'add_settings_page' )
			),
			array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Створити Накладну',
				'menu_title' 	=> 'Створити Накладну',
				'capability' 	=> 'manage_woocommerce',
				'menu_slug' 	=> 'morkvanp_invoice',
				'callback' 		=>  array( $this, 'add_invoice_page' )
			),
			array(
				'parent_slug' 	=> 'morkvanp_plugin',
				'page_title' 	=> 'Мої накладні',
				'menu_title'	=> 'Мої накладні',
				'capability'	=> 'manage_woocommerce',
				'menu_slug'		=> 'morkvanp_invoices',
				'callback'		=> array( $this, 'invoices_page' )
			),
			array(
				'parent_slug'	=> 'morkvanp_plugin',
				'page_title'	=> 'Про плагін',
				'menu_title'	=> 'Про плагін',
				'capability'	=> 'manage_woocommerce',
				'menu_slug'		=> 'morkvanp_about',
				'callback'		=> array( $this, 'about_page' )
			)

			,
		);
		}



		return $this;
	}

	/**
	 * Adding subpage of plugin
	 *
	 * @since 1.0.0
	 */
	public function add_invoice_page()
	{
		require_once( PLUGIN_PATH . '/public/partials/morkvanp-plugin-form.php');
	}

	/**
	 * Add invoices subpage of plugin
	 *
	 * @since 1.0.0
	 */
	public function invoices_page()
	{
		$path = PLUGIN_PATH . '/public/partials/morkvanp-plugin-invoices-page.php';
		if(file_exists($path)){
		require_once( $path );
		}
		else{
			$path = PLUGIN_PATH . '/public/partials/morkvanp-plugin-invoices-page-demo.php';
			require_once( $path );

		}
	}

	/**
	 * Add about page of plugin
	 *
	 * @since 1.0.0
	 */
	public function about_page()
	{
		$path = PLUGIN_PATH . '/public/partials/morkvanp-plugin-about-page.php';
		require_once( $path );
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
				'option_name' => 'woocommerce_nova_poshta_shipping_method_area_name'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'woocommerce_nova_poshta_shipping_method_area'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'woocommerce_nova_poshta_shipping_method_city_name'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'woocommerce_nova_poshta_shipping_method_city'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'woocommerce_nova_poshta_shipping_method_warehouse_name'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'woocommerce_nova_poshta_shipping_method_warehouse'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'text_example'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'zone_example'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'np_address_shpping_notuse'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'show_calc'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'autoinvoice'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'type_example'
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
				'option_name' => 'phone'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'flat'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'warehouse'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_description'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_addweight'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_allvolume'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_date'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_cod'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_short'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_dpay'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_payer'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_zpayer'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_cpay'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_tpay'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_cron'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'invoice_auto'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'morkvanp_email_template'
			),
			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'morkvanp_email_subject'
			),

			array(
				'option_group' => 'morkvanp_options_group',
				'option_name' => 'morkvanp_checkout_count'
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

		$path = PLUGIN_PATH . '/public/partials/morkvanp-plugin-invoices-page.php';
		if(file_exists($path)){
		$args = array(
			//start base settings
			array(
				'id' => 'text_example',
				'title' => 'API ключ',
				'callback' => array( $this->callbacks, 'morkvanpTextExample' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'text_example',
					'class' => 'basesettings allsettings show'
				)
			),
			array(
				'id' => 'zone_example',
				'title' => 'Працювати із зонами доставки',
				'callback' => array( $this->callbacks, 'morkvanpzone' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'zone_example',
					'class' => 'basesettings allsettings show'
				)
			),

			array(
				'id' => 'np_address_shpping_notuse',
				'title' => 'Вимкнути адресну доставку',
				'callback' => array( $this->callbacks, 'morkvanp_address_shpping_notuse' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'names',
					'class' => 'basesettings allsettings show'
				)
			),

			array(
				'id' => 'morkvanp_checkout_count',
				'title' => 'Поля при оформленні замовлення',
				'callback' => array( $this->callbacks, 'morkvanpCheckoutExample' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'morkvanp_checkout_count',
					'class' => 'additional allsettings'
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
					'class' => 'basesettings allsettings show'
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
					'class' => 'basesettings allsettings show'
				)
			),
			array(
				'id' => 'regiond',
				'title' => 'Відділення відправки:',
				'callback' => array( $this->callbacks, 'emptyfunccalbask' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'region',
					'class' => 'h3as basesettings allsettings show'
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
					'class' => 'basesettings allsettings show'
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
					'class' => 'basesettings allsettings show'
				)
			),
			array(
				'id' => 'warehouse',
				'title' => 'Віділення',
				'callback' => array( $this->callbacks, 'morkvanpWarehouseAddress'),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'warehouse',
					'class' => 'basesettings allsettings show'
				)
			),
			// end base settings

			// start additional settings

			array(
				'id' => 'show_calc',
				'title' => 'Показати розрахунок вартості доставки при оформленні замовлення',
				'callback' => array( $this->callbacks, 'morkvanpcalc' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'zone_example',
					'class' => 'allsettings additional'
				)
			),







						array(
							'id' => 'type_example',
							'title' => 'Тип відправлення за замовчуванням',
							'callback' => array( $this->callbacks, 'morkvanpTypeExample' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'type_example',
								'class' => 'additional allsettings '
							)
						),






						array(
							'id' => 'invoice_description',
							'title' => 'Опис відправлення (за замовчуванням)',
							'callback' => array( $this->callbacks, 'morkvanpInvoiceDescription' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_description',
								'class' => 'allsettings additional'
							)
						),

						array(
							'id' => 'invoice_tpay',
							'title' => 'Тип оплати за замовчуванням',
							'callback' => array( $this->callbacks, 'morkvanpInvoicetpay' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_tpay',
								'class' => 'allsettings additional'
							)
						),

						array(
							'id' => 'invoice_payer',
							'title' => 'Хто платить за доставку за замовчуванням?',
							'callback' => array( $this->callbacks, 'morkvanpInvoicepayer' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_payer',
								'class' => 'allsettings additional'
							)
						),

							array(
							'id' => 'invoice_zpayer',
							'title' => 'Хто платить за зворотню доставку за замовчуванням?',
							'callback' => array( $this->callbacks, 'morkvanpInvoicezpayer' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_zpayer',
								'class' => 'allsettings additional'
							)
						),

						array(
							'id' => 'morkvanp_email_subject',
							'title' => 'Шаблон заголовку email повідомлення',
							'callback' => array( $this->callbacks, 'morkvanpEmailSubject' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'morkvanp_email_subject',
								'class' => 'allsettings additional morkvanp_email_subject'
							)
						),

						array(
							'id' => 'invoice_email_template',
							'title' => 'Шаблон email',
							'callback' => array( $this->callbacks, 'morkvanpEmailTemplate' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_date',
								'class' => 'allsettings additional'
							)
						),




						array(
							'id' => 'invoice_allvolume',
							'title' => 'Об\'єм упаковки',
							'callback' => array( $this->callbacks, 'morkvanpInvoiceAllvol' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_addweight',
								'class' => 'allsettings additional'
							)
						),

						array(
							'id' => 'invoice_addweight',
							'title' => 'Вага упаковки',
							'callback' => array( $this->callbacks, 'morkvanpInvoiceAddWeight' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_addweight',
								'class' => 'allsettings additional'
							)
						),


						array(
							'id' => 'invoice_date',
							'title' => 'Контроль дати відправки',
							'callback' => array( $this->callbacks, 'morkvanpInvoiceDate' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_date',
								'class' => 'allsettings additional'
							)
						),

						array(
							'id' => 'invoice_cod',
							'title' => 'Вимкнути автоматичний наложений платіж ',
							'callback' => array( $this->callbacks, 'morkvanpInvoicecod' ),
							'page' => 'morkvanp_plugin',
							'section' => 'morkvanp_admin_index',
							'args' => array(
								'label_for' => 'invoice_cod',
								'class' => 'allsettings additional'
							)
						),

			//start auto settings
			array(
				'id' => 'autoinvoice',
				'title' => 'Створювати накладні автоматично',
				'callback' => array( $this->callbacks, 'morkvanpInvoiceautottn' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'autoinvoice',
					'class' => 'autosettings allsettings'
				)
			),



			array(
				'id' => 'invoice_cpay',
				'title' => 'Контроль платежу',
				'callback' => array( $this->callbacks, 'morkvanpInvoicecpay' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'invoice_cpay',
					'class' => 'allsettings forjuridical'
				)
			),








						// end additional settings


// start auto settings
			array(
				'id' => 'invoice_dpay',
				'title' => 'Автоматизація залежно від суми замовлення',
				'callback' => array( $this->callbacks, 'morkvanpInvoicedpay' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'invoice_dpay',
					'class' => 'autosettings allsettings'
				)
			),



			array(
				'id' => 'invoice_cron',
				'title' => 'Крон оновлення статусів замовлення',
				'callback' => array( $this->callbacks, 'morkvanpInvoicecron' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'invoice_cron',
					'class' => 'autosettings allsettings'
				)
			),

			array(
				'id' => 'invoice_auto',
				'title' => 'Автооновлення статусів замовлення',
				'callback' => array( $this->callbacks, 'morkvanpInvoiceauto' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'invoice_auto',
					'class' => 'autosettings allsettings'
				)
			),

		);
		}
		else{
				$args = array(

				array(
					'id' => 'text_example',
					'title' => 'API ключ',
					'callback' => array( $this->callbacks, 'morkvanpTextExample' ),
					'page' => 'morkvanp_plugin',
					'section' => 'morkvanp_admin_index',
					'args' => array(
						'label_for' => 'text_example',
						'class' => 'basesettings allsettings'
					)
				),

				array(
					'id' => 'type_example',
					'title' => 'Тип відправлення за замовчуванням',
					'callback' => array( $this->callbacks, 'morkvanpTypeExample' ),
					'page' => 'morkvanp_plugin',
					'section' => 'morkvanp_admin_index',
					'args' => array(
						'label_for' => 'type_example',
						'class' => 'example-class'
					)
				),

				array(
				'id' => 'morkvanp_checkout_count',
				'title' => 'Поля при оформленні замовлення',
				'callback' => array( $this->callbacks, 'morkvanpCheckoutExample' ),
				'page' => 'morkvanp_plugin',
				'section' => 'morkvanp_admin_index',
				'args' => array(
					'label_for' => 'morkvanp_checkout_count',
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
		}

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
				echo '<img src="'.NOVA_POSHTA_TTN_SHIPPING_PLUGIN_URL.'/includes/nova_poshta_25px.png"
		 style="height: 25px;width: 25px; margin-right: 20px; margin-top: 2px;">';
         echo "<a class='button button-primary send' href='admin.php?page=morkvanp_invoice'>Створити накладну</a>";
			//	echo "<script src='". PLUGIN_URL . "public/js/script.js'></script>";
			//	echo "<link href='". PLUGIN_URL . "public/css/style.css' />";
	}

	/**
	 * Generating meta box
	 *
	 * @since 1.0.0
	 */
	public function mv_add_meta_boxes()
    {
        add_meta_box( 'mv_other_fields', __('Відправлення Нова Пошта','woocommerce'), array( $this, 'add_plugin_meta_box' ), 'shop_order', 'side', 'core' );
    }

    /**
     * Creating custom column at woocommerce order page
     *
     * @since 1.1.0
     */
    public function woo_custom_column( $columns )
    {
    	$columns['created_invoice'] = 'Накладна';
    	$columns['invoice_number'] = 'Номер накладної';
    	return $columns;
    }

    /**
     * Getting data of order column at order page
     *
     * @since 1.1.0
     */
    public function woo_column_get_data( $column ) {
    	global $post;
    	$data = get_post_meta( $post->ID );

    	$order_id = $post->ID;
	    $selected_order = wc_get_order( $post->ID );
	    $order = $selected_order->get_data();
	    $meta_ttn = get_post_meta( $order_id, 'novaposhta_ttn', true );

    	if ( $column == 'created_invoice' ) { //will be deprecated
    		global $wpdb;

    		$order_id = $post->ID;
    		$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}novaposhta_ttn_invoices WHERE order_id = '$order_id'", ARRAY_A );



    		if ( !empty($results) || !empty($meta_ttn) ) {
    			$img = "/nova_poshta_25px.png";
    			echo '<img src="' . site_url() . '/wp-content/plugins/' . plugin_basename( __DIR__ ) . $img . '" />';
    		} else {
    			$img = '/nova_poshta_grey_25px.png';
    			echo '<img src="' . site_url() . '/wp-content/plugins/' . plugin_basename( __DIR__ ) . $img . '" />';
    		}
    	}

    	if ( $column == 'invoice_number' ) {
    		global $wpdb;

    		$order_id = $post->ID;
    		$number_result = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}novaposhta_ttn_invoices WHERE order_id = '$order_id'", ARRAY_A );

    		if ( !empty($results) ) {
    			echo $number_result["order_invoice"];
    		} else {
    			if(isset($meta_ttn)){
    				echo  $meta_ttn;
    			}
    			else{
    			echo "";
    			}
    		}
    	}
    	/*
    	global $post;
    	$data = get_post_meta( $post->ID );
    	$order_id = $post->ID;
    	$selected_order = wc_get_order( $post->ID );
    	$order = $selected_order->get_data();
    	$meta_ttn = get_post_meta( $order_id, 'novaposhta_ttn', true );
    	if ( $column == 'created_invoice' ) {
    		if (isset($meta_ttn)) {
    			$img = "/nova_poshta_25px.png";
    			echo '<img src="' . site_url() . '/wp-content/plugins/' . plugin_basename( __DIR__ ) . $img . '" />';
    		}
    		else {
    			$img = '/nova_poshta_grey_25px.png';
    			echo '<img src="' . site_url() . '/wp-content/plugins/' . plugin_basename( __DIR__ ) . $img . '" />';
    		}
    	}
    	if ( $column == 'invoice_number' ) {
			if(isset($meta_ttn)){
				echo $meta_ttn;
			}
			else {
    			echo "-";
    		}
    	}
    	*/
    }

    /**
     * Add meta box with invoice information
     *
     * @since 1.1.0
     */
    public function add_invoice_meta_box()
    {
    	if ( isset($_GET["post"]) ) {
    		add_meta_box( 'invoice_other_fields', __('Накладна','woocommerce'), array( $this, 'invoice_meta_box_info' ), 'shop_order', 'side', 'core' );
    	}

    }

    /**
     * Add info of invoice meta box
     *
     * @since 1.1.0
     */
    public function invoice_meta_box_info()
    {
    	if ( isset($_GET["post"]) ) { $order_id = $_GET["post"]; }

    	global $wpdb;
    	$api_key = get_option('text_example');
    	$selected_order = wc_get_order( $order_id );

		$order = $selected_order->get_data();
		$meta_ttn = get_post_meta( $order_id, 'novaposhta_ttn', true );
		if ( empty( $meta_ttn ) ) {//legacy support
			global $wpdb;
    	$result = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}novaposhta_ttn_invoices WHERE order_id = '$order_id'", ARRAY_A );
			if(isset( $result[0]['order_invoice'] )){
    	$meta_ttn = $result[0]['order_invoice'];
			//номер тут не  завжди правильно показує
			}
		}
		$invoice_email = $order['billing']['email'];

    	if ( ! empty( $meta_ttn ) ) {
    		$invoice_number = $meta_ttn;
    		echo 'Номер накладної: ' . $meta_ttn;
    		echo '<a style="margin: 5px;" href="https://my.novaposhta.ua/orders/printDocument/orders[]/' .  $invoice_number . '/type/pdf/apiKey/' .  $api_key . '" class="button" target="_blank">' . '<img src="' . plugins_url('img/004-printer.png', __FILE__) . '" height="15" width="15" />' . ' Друк накладної</a>';
    		echo '<a style="margin: 5px;" href="https://my.novaposhta.ua/orders/printMarkings/orders[]/' . $invoice_number . '/type/pdf/apiKey/' . $api_key . '" class="button" target="_blank">' . '<img src="' . plugins_url('img/003-barcode.png', __FILE__) . '" height="15" width="15"  />' . ' Друк стікера</a>';


    	$api_key = get_option('text_example');

		$methodProperties = array(
			"Documents" => array(
				array(
					"DocumentNumber" => $invoice_number
					),
				)
		);

		$invoiceData = array(
			"apiKey" => $api_key,
    		"modelName" => "TrackingDocument",
    		"calledMethod" => "getStatusDocuments",
    		"methodProperties" => $methodProperties
		);

		$curl = curl_init();

		$url = "https://api.novaposhta.ua/v2.0/json/";

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => True,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode( $invoiceData ),
			CURLOPT_HTTPHEADER => array("content-type: application/json",),
		));

		$response = curl_exec( $curl );
		$error = curl_error( $curl );
		curl_close( $curl );

			if ( $error ) {

			} else {
			$response_json = json_decode( $response, true );

			 // echo "<pre>";
			if(isset($response_json["data"][0])){
				$obj = (array) $response_json["data"][0];
			}
			}

			// var_dump( $obj['Number'] );

			?>
			<a href="#" id="email_sent" class="button" style="margin: 5px;background: url(<?php echo  plugins_url('img/002-envelope.png', __FILE__); ?>) no-repeat scroll 7px 4px; padding-left: 30px;"> Відправити на e-mail</a>
			<input type="text" name="invoice_email" id="invoice_email" value="<?php echo $invoice_email; ?>" style="display: none;" />
			<input type="text" name="invoice_number" id="invoice_number" value="<?php echo $invoice_number; ?>" style="display: none;" />
			<input type="text" id="order_id" value="<?php echo $order_id; ?>" style="display: none;" />
			<input type="text" id="date_created" value="<?php echo $obj['DateCreated']; ?>" style="display: none;" />

    		<script type="text/javascript">
    			jQuery(document).ready( function($) {

    				jQuery( '#email_sent' ).click( function() {
    					var invoice_email = jQuery('#invoice_email').val();
    					var invoice_number = jQuery('#invoice_number').val();
    					var order_id = jQuery('#order_id').val();
    					var DateCreated = jQuery('#date_created').val();

    					$.ajax({
    						url: '/wp-admin/admin.php?page=morkvanp_invoices',
    						type: 'POST',
    						data: {
    							email: invoice_email,
    							number: invoice_number,
    							order: order_id,
    							date: DateCreated,
    						},
    						beforeSend: function( xhr ) {
    							jQuery('#email_sent').text('Відправлення...');
    						},
    						success: function( data ) {
    							jQuery('#email_sent').text('Відправити на e-mail');
    							console.log('Request created for mail sent');
    						}
    					});
    				});

    			});
    		</script>
			<?php

    	} else {
    		echo 'Номер накладної: -';
    	}

    }


	/**
	 * From name email
	 *
	 * @since 1.1.3
	 */
	public function my_mail_from_name( $name ) {
		//$bloginfo = get_bloginfo();
		//$title = $bloginfo->name;

    	return get_option('blogname');
	}
}
