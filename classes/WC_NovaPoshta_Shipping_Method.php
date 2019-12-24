<?php

 use plugins\NovaPoshta\classes\base\ArrayHelper;
 use plugins\NovaPoshta\classes\base\Options;
 use plugins\NovaPoshta\classes\Checkout;
 use plugins\NovaPoshta\classes\Customer;

/**
 * Class WC_NovaPoshta_Shipping_Method
 */
class WC_NovaPoshta_Shipping_Method extends WC_Shipping_Method
{
     public function __construct($instance_id = 0){

    $this->instance_id = absint( $instance_id );
    parent::__construct($instance_id);
    $this->id = NOVA_POSHTA_TTN_SHIPPING_METHOD;
    $this->method_title = __('Nova Poshta', NOVA_POSHTA_TTN_DOMAIN);
    $this->method_description = $this->getDescription();

    if(get_option('zone_example')){
    $this->supports = array(
        'shipping-zones',
        'instance-settings',
        'instance-settings-modal',
        );
    }
        $this->init();

        // Get setting values
        $this->title = $this->settings['title'];
        $this->enabled = true;




    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init()
    {
        $this->init_form_fields();
        $this->init_settings();
        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    public function test($packages)
    {

        return $packages;
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields()
    {

        if(get_option('zone_example')){
        $this->instance_form_fields = array(
            'title' => array(
                'title' => __('Nova Poshta', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => __('Nova Poshta', NOVA_POSHTA_TTN_DOMAIN)
            ),

            Options::USE_FIXED_PRICE_ON_DELIVERY => array(
                'title' => __('Set Fixed Price for Delivery.', NOVA_POSHTA_TTN_DOMAIN),
                'label' => __('If checked, fixed price will be set for delivery.', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => '',
            ),
            Options::FIXED_PRICE => array(
                'title' => __('Fixed price', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'text',
                'description' => __('Delivery Fixed price.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => 0.00
            ),

            'settings' => array(
                'title' => __('', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'hidden',
                'description' => __('Решта налаштувань доступні за <a href="admin.php?page=morkvanp_plugin">посиланям</a>.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => __(' ', NOVA_POSHTA_TTN_DOMAIN)
            ),
        );

    }

    else{
         $this->form_fields = array(
            'title' => array(
                'title' => __('Nova Poshta', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => __('Nova Poshta', NOVA_POSHTA_TTN_DOMAIN)
            ),



            Options::USE_FIXED_PRICE_ON_DELIVERY => array(
                'title' => __('Set Fixed Price for Delivery.', NOVA_POSHTA_TTN_DOMAIN),
                'label' => __('If checked, fixed price will be set for delivery.', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'checkbox',
                'default' => 'no',
                'description' => '',
            ),
            Options::FIXED_PRICE => array(
                'title' => __('Fixed price', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'text',
                'description' => __('Delivery Fixed price.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => 0.00
            ),

            'settings' => array(
                'title' => __('', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'hidden',
                'description' => __('Решта налаштувань доступні за <a href="admin.php?page=morkvanp_plugin">посиланям</a>.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => __(' ', NOVA_POSHTA_TTN_DOMAIN)
            ),


         );
    }

}


    /**
     * calculate_shipping function.
     *
     * @access public
     *
     * @param array $package
     */
    public function calculate_shipping($package = array())
    {
        $rate = array(
            'id' => $this->id,
            'label' => $this->title,
            'cost' => 0,
            'calc_tax' => 'per_item'
        );

        $location = Checkout::instance()->getLocation();
        $cityRecipient = Customer::instance()->getMetadata('nova_poshta_city', $location)
            //for backward compatibility with woocommerce 2.x.x
            ?: Customer::instance()->getMetadata('nova_poshta_city', '');

        if (NPttn()->options->useFixedPriceOnDelivery) {
            $rate['cost'] = NPttn()->options->fixedPrice;
        } elseif ($cityRecipient) {
            $citySender = NPttn()->options->senderCity;
            $serviceType = 'WarehouseWarehouse';
            /** @noinspection PhpUndefinedFieldInspection */
            $cartWeight = max(1, WC()->cart->cart_contents_weight);
            /** @noinspection PhpUndefinedFieldInspection */
            $cartTotal = max(1, WC()->cart->cart_contents_total);
            try {
                $result = NPttn()->api->getDocumentPrice($citySender, $cityRecipient, $serviceType, $cartWeight, $cartTotal);
                $cost = array_shift($result);
                $rate['cost'] = ArrayHelper::getValue($cost, 'Cost', 0);
            } catch (Exception $e) {
                NPttn()->log->error($e->getMessage());
            }
        }
        // Register the rate
        $rate = apply_filters('woo_shipping_for_nova_poshta_before_add_rate', $rate, $cityRecipient);
        $this->add_rate($rate);
    }

    /**
     * Is this method available?
     * @param array $package
     * @return bool
     */
    public function is_available($package)
    {
        return $this->is_enabled();
    }

    /**
     * @return string
     */
    private function getDescription()
    {
        $href = "https://wordpress.org/support/view/plugin-reviews/nova-poshta-ttn?filter=5#postform";
        $link = '<a href="' . $href . '" target="_blank" class="np-rating-link">&#9733;&#9733;&#9733;&#9733;&#9733;</a>';

        $descriptions = array();
        $descriptions[] = __('Shipping with popular Ukrainian logistic company Nova Poshta', NOVA_POSHTA_TTN_DOMAIN);
        if (NPttn()->options->pluginRated) {
            $descriptions[] = __('Thank you for encouraging us!', NOVA_POSHTA_TTN_DOMAIN);
        } else {
            $descriptions[] = sprintf(__("If you like our work, please leave us a %s rating!", NOVA_POSHTA_TTN_DOMAIN), $link);
        }
        return implode($descriptions, '<br>');
    }
}


class WC_NovaPoshta_Shipping_Method1 extends WC_Shipping_Method {
/**
* Constructor.
*
* @param int $instance_id
*/
public function __construct( $instance_id = 0 ) {
$this->id = 'nova_poshta_shipping_method';
$this->instance_id = absint( $instance_id );
$this->method_title = __( "Woo Nova Poshta ", 'imp' );
$this->supports = array(
'shipping-zones',
'instance-settings',
'instance-settings-modal',
);
$this->init();
}

/**
* Initialize custom shiping method.
*/
public function init() {

// Load the settings.
$this->init_form_fields();
$this->init_settings();

// Define user set variables
$this->title = $this->get_option( 'title' );

// Actions
add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
}

/**
* Calculate custom shipping method.
*
* @param array $package
*
* @return void
*/
public function calculate_shipping( $package = array() ) {
$this->add_rate( array(
'label' => $this->title,
'package' => $package,
) );
}

/**
* Init form fields.
*/
public function init_form_fields() {
$this->instance_form_fields = array(


'title' => array(
'title' => __( 'назва способу', 'imp' ),
'type' => 'text',
'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
'default' => __( 'Woo Nova Poshta ', 'imp' ),
'desc_tip' => true,
),
'means' => array(
'title' => __( 'опис способу', 'imp' ),
'type' => 'text',
'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
'default' => __( 'Woo Nova Poshta ', 'imp' ),
'desc_tip' => true,
),
);
}
}
