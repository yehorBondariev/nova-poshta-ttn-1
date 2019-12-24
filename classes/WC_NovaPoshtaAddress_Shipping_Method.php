<?php



/**
 * Class WC_NovaPoshtaAddress_Shipping_Method
 */


 class WC_NovaPoshtaAddress_Shipping_Method extends WC_Shipping_Method
 {
      public function __construct($instance_id = 0){

     $this->instance_id = absint( $instance_id );
     parent::__construct($instance_id);
     $this->id = NOVA_POSHTA_TTN_ADDRESS_SHIPPING_METHOD;
     $this->method_title = __('Address Nova Poshta', NOVA_POSHTA_TTN_DOMAIN);
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
         $this->title = 'Address Nova Poshta' ;
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
         $descriptions[] = __('Address Shipping with popular Ukrainian logistic company Nova Poshta', NOVA_POSHTA_TTN_DOMAIN);
         if (NPttn()->options->pluginRated) {
             $descriptions[] = __('Thank you for encouraging us!', NOVA_POSHTA_TTN_DOMAIN);
         } else {
             $descriptions[] = sprintf(__("If you like our work, please leave us a %s rating!", NOVA_POSHTA_TTN_DOMAIN), $link);
         }
         return implode($descriptions, '<br>');
     }
 }


class WC_NovaPoshtaAddress_Shipping_Method1 extends WC_Shipping_Method
{
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @param int $instance_id
     */
    public function __construct($instance_id = 0)
    {
      $this->instance_id = absint( $instance_id );
      parent::__construct($instance_id);

        $this->id = NOVA_POSHTA_TTN_ADDRESS_SHIPPING_METHOD;
        $this->method_title = __('Адресна доставка Нова Пошта', NOVA_POSHTA_TTN_DOMAIN);
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
 $this->title = $this->method_title;
      //  $this->title = $this->settings['title'];
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
        $this->init_settings();    // Save settings in admin if you have any defined
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
            'title' => __('Адресна доставка Нова Пошта', NOVA_POSHTA_TTN_DOMAIN),
            'type' => 'text',
            'description' => __('Властивість керує заголовком, який бачить користувач під час перевірки.', NOVA_POSHTA_TTN_DOMAIN),
            'default' => __('Адресна доставка Нова Пошта', NOVA_POSHTA_TTN_DOMAIN)
        ),
      );
  }

  else{
        $this->form_fields = array(
            'title' => array(
                'title' => __('Адресна доставка Нова Пошта', NOVA_POSHTA_TTN_DOMAIN),
                'type' => 'text',
                'description' => __('Властивість керує заголовком, який бачить користувач під час перевірки.', NOVA_POSHTA_TTN_DOMAIN),
                'default' => __('Адресна доставка Нова Пошта', NOVA_POSHTA_TTN_DOMAIN)
            )
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


        $rate = apply_filters('woo_shipping_for_nova_poshta_before_add_rate', $rate, '');
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
        return '';
    }
}
