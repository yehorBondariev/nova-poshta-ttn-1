<?php
function request_npaddress_shipping_quote_init() {
if ( ! class_exists( 'WC_NovaPoshtaAddress_Shipping_Method' ) ) {

class WC_NovaPoshtaAddress_Shipping_Method extends WC_Shipping_Method {

public function __construct( $instance_id = 0 ) {
$this->id = 'npttn_address_shipping_method';
$this->instance_id = absint( $instance_id );
$this->method_title = __( "Адресна доставка Нова пошта ", NOVA_POSHTA_TTN_DOMAIN );
$this->supports = array(
'shipping-zones',
'instance-settings',
'instance-settings-modal',
);
$this->init();
}

public function init() {


$this->init_form_fields();
$this->init_settings();

//add_filter('woocommerce_checkout_fields', array($this, 'maybeDisableDefaultShippingMethods'));
//add_filter('nova_poshta_disable_default_fields', array($this, 'disableDefaultFields'));
// add_filter('woocommerce_checkout_fields', [ $this, 'removeDefaultFieldsFromValidation' ]);
// add_filter('woocommerce_checkout_posted_data', [ $this, 'processCheckoutPostedData' ]);

$this->title = $this->get_option( 'title' );
$this->description = $this->get_option('desc');

add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
}


private function maybeDisableDefaultFields()
{
  return isset($_POST['shipping_method']) &&
    preg_match('/^' . 'npttn_address_shipping_method' . '.*/i', $_POST['shipping_method'][0]);

}

public function removeDefaultFieldsFromValidation($fields)
{
  if ($this->maybeDisableDefaultFields()) {
    unset($fields['billing']['billing_postcode']);
  }
  return $fields;
}

public function processCheckoutPostedData($data)
{
  if (isset($data['shipping_method'])) {
    if (
      preg_match('/^' . 'npttn_address_shipping_method' . '.*/i', $data['shipping_method'][0]) &&
      isset($data['ship_to_different_address'])
    ) {

      unset($data['shipping_postcode']);
      unset($data['billing_postcode']);
    }
  }

  return $data;
}

public function maybeDisableDefaultShippingMethods($fields)
{
  echo '<script>console.log("'.$_POST['shipping_method'].'")</script>';
  if(isset ($_POST)){
      echo '<script>console.log("'.$_POST['shipping_method'].'")</script>';
    if (strpos($_POST['shipping_method'][0], 'npttn_address') !== false) {
    //if(  preg_match('/^npttn_address*/i', $_POST['shipping_method'][0])  ){
      unset($fields['billing']['billing_postcode']);
      unset($fields['shipping']['shipping_postcode']);
  }
}
return $fields;
}
public function disableDefaultFields($fields)
{
  $fields['billing']['billing_postcode']['required'] = false;
   $fields['shipping']['shipping_postcode']['required'] = false;
  unset($fields['billing']['billing_postcode']);
  unset($fields['shipping']['shipping_postcode']);
    return $fields;
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
'title' => __( 'Заголовок', NOVA_POSHTA_TTN_DOMAIN ),
'type' => 'text',
'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
'default' => __( ' Адресна доставка Нова пошта  ', NOVA_POSHTA_TTN_DOMAIN ),
'desc_tip' => true,
),
'desc' => array(
'title' => __( 'опис ', NOVA_POSHTA_TTN_DOMAIN ),
'type' => 'text',
'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
'default' => __( ' Адресна доставка Нова пошта  ', NOVA_POSHTA_TTN_DOMAIN ),
'desc_tip' => true,
),
);
}


}
}
}
add_action( 'woocommerce_shipping_init', 'request_npaddress_shipping_quote_init' );

function request_npaddress_shipping_quote_shipping_method( $methods ) {
$methods['npttn_address_shipping_method'] = 'WC_NovaPoshtaAddress_Shipping_Method';

return $methods;
}
add_filter( 'woocommerce_shipping_methods', 'request_npaddress_shipping_quote_shipping_method' );
