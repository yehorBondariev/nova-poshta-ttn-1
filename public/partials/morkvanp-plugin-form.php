<?php
session_start();
require("morkvanp-plugin-invoice-controller.php");
require("functions.php");
include("morkvanp-plugin-invoice.php");

$showpage = true; $order_id = 0;

//set order id if  HTTP REFFERRER  is woocommerce order
if(isset($_SERVER['HTTP_REFERER'])) {
    $qs = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if(!empty($qs)){
        parse_str($qs, $output);
        // TODO check for key existence
        if(isset($output['post'])){
          $order_id =  $output['post'];  // id
        }
      }
}

//if isset order from previous step id and not null srialize order id to session
//else do  not show ttn form
if(isset($order_id) && ($order_id != 0) ){
    $order_data0 = wc_get_order( $order_id );
    if(isset($order_data0) && (!$order_data0 == false)){
        $order_data = $order_data0->get_data();
        $_SESSION['order_id'] = serialize($order_id);
    }
    else{
      $showpage =false;
    }
}

//if isset order id only from session  get it
else if ( isset($_SESSION['order_id']) ) {
    //$order_id = 0;
    $ret = @unserialize($_SESSION['order_id']);
    if(  gettype($ret) == 'boolean' ){
      $order_id = $_SESSION['order_id'];
    }
    else{
      $order_id = unserialize($_SESSION['order_id']);
    }

    $order_data0 = wc_get_order( $order_id );
    $order_data = $order_data0->get_data();
    //print_r($order_data);
}
//else do not show form ttn
else{
    $showpage =false;
}




if(isset($order_data)){
$warehouse_billing = process_warehouse_billing($order_data);
}

//////////////??????????????

//processing first name
if ( isset($order_data["billing"]["first_name"]) ) {
  $shipping_first_name = $order_data["billing"]["first_name"];
}
else if ( isset( $order_data["shipping"]["first_name"] ) ) {
  $shipping_first_name = $order_data["shipping"]["first_name"];
}
else {
  $shipping_first_name = "";
}


//processing last name
if ( isset($order_data["billing"]["last_name"]) ) {
  $shipping_last_name = $order_data["billing"]["last_name"];
}
else if ( isset($order_data["shipping"]["last_name"]) ) {
  $shipping_last_name = $order_data["shipping"]["last_name"];
}
else {
  $shipping_last_name = "";
}

//processing and billing address
if ( isset($order_data["billing"]["address_2"]) ) {
  $shipping_address = $order_data["billing"]["address_2"];
  $shipping_address = explode(" ", $shipping_address);
}
else if ( isset($order_data["shipping"]["address_2"]) ) {
  $shipping_address = $order_data["shipping"]["address_2"];
  $shipping_address = explode(" ", $shipping_address);
}
else {
  $shipping_address[0] = "";
  $shipping_address[1] = "";
}

//if isset billing/shipping city set up
if ( isset($order_data["billing"]["city"])  && ($order_data["billing"]["city"] != '') ) {
  $shipping_city = $order_data["billing"]["city"];
}
else if ( isset($order_data["shipping"]["city"]) ) {
  $shipping_city = $order_data["shipping"]["city"];
}
else {
  $shipping_city = "";
}

//replace not needed space in city string
$shipping_city = preg_replace('/\s\s+/', ' ', $shipping_city);

//set up billing/shipping state
if ( isset($order_data["billing"]["state"]) ) {
  $shipping_state = $order_data["billing"]["state"];
}
else if ( isset($order_data["shipping"]["state"]) ) {
  $shipping_state = $order_data["shipping"]["state"];
}
else {
  $shipping_state = "";
}

//replace substring in state string
$shipping_state = str_replace("область", "", $shipping_state);
$shipping_phone = '';
if(isset($order_data)){
$shipping_phone = get_shipping_phone($order_data);
}

// start calculating alternate weight
$varia = null;
if(isset ($order_data['line_items'])){
  $varia = $order_data['line_items'];
}
$alternate_weight = 0;
$dimentions = array();
$d_vol_all = 0;
$weighte = '';
$prod_quantity = 0;
$prod_quantity2 = 0;
$list = '';
$list2 = '';
$descr = '';

//alternative weight functions
if(isset ($varia)){
  foreach ($varia as $item){
    $data = $item->get_data();
    $quantity = ($data['quantity']);
    $quanti = $quantity;
    $pr_id = $data['product_id'];
    $product = wc_get_product($pr_id);
    if ( $product->is_type('variable') ) {
      $var_id = $data['variation_id'];
      $variations      = $product->get_available_variations();
      for ($i=0; $i < sizeof($variations) ; $i++){
          if($variations[$i]['variation_id'] == $var_id ){
            //print_r($variations[$i]);
            while ($quanti > 0) {
              if (is_numeric(  $variations[$i]['weight'] )){
                $alternate_weight += $variations[$i]['weight'];
              }
              if( !($variations[$i]['weight'] > 0)  ){
                $weighte = 'Маса вказана не  для всіх товарів в кошику. Радимо уточнити цифри.';
              }

              array_push($dimentions, $variations[$i]['dimensions']);

              if ( is_numeric( $variations[$i]['dimensions']['length'] ) && is_numeric( $variations[$i]['dimensions']['width'] ) && is_numeric( $variations[$i]['dimensions']['height'] ) ){
                $d_vol = $variations[$i]['dimensions']['length'] * $variations[$i]['dimensions']['width'] * $variations[$i]['dimensions']['height'];
                $d_vol_all += $d_vol;
              }
              $quanti--;
            }
            //$product = new WC_Product($var_id);
            $sku = $variations[$i]['sku'];
            if(!empty($sku)){
              $sku = '('.$sku.')';
            }
            $name = $product->get_title();
            $list2  .= $name .$sku. ' x '.$quantity.'шт ;';
            $list  .= $name .' x '.$quantity.'шт ;';
            $prod_quantity += 1;
            $prod_quantity2 += $quantity;
          }
        }
      }
      else{
        $sku = $product->get_sku();
        if(!empty($sku)){
          $sku = '('.$sku.')';
        }
        $name = $product->get_title();
        $list2  .= $name .$sku. ' x '.$quantity.'шт ;';
        $list  .= $name . ' x '.$quantity.'шт ;';
        $prod_quantity += 1;
        $prod_quantity2 += $quantity;
        $diment =0;
        if( (is_numeric($product->get_width()) ) && (is_numeric($product->get_length())) && (is_numeric($product->get_height())) ) {
          $diment = $product->get_length() * $product->get_width() * $product->get_height();
          $d_array = array('length'=>$product->get_length(),'width'=> $product->get_width(), 'height'=>$product->get_height() );
          array_push($dimentions, $d_array);
          $d_vol_all += $diment;
        }
        while ($quantity > 0) {
          $weight = $product->get_weight();
          if ($weight > 0){
            $alternate_weight += $weight;
          }
          else {
            $weighte = 'Маса вказана не  для всіх товарів в кошику. Радимо уточнити цифри.';
          }
        $quantity--;
      }
    }
  }
}
$alternate_vol=0;
$volumemessage = '';
if((sizeof($dimentions) > 1)){
  $alternate_vol = $d_vol_all;
  $volumemessage = 'УВАГА! В відправленні кілька товарів. Ми порахували арифметичний сумарний об\'єм посилки, враховуючи мета-дані товарів. Ви можете змінити об\'єм зараз вручну, щоб бути більш точним.' ;
}
else{
  if ( isset($variations) ){
    if ( is_numeric( $variations[0]['dimensions']['length'] ) &&  is_numeric( $variations[0]['dimensions']['width'] ) &&  is_numeric( $variations[0]['dimensions']['height'] ) ){
        $alternate_vol = $variations[0]['dimensions']['length'] * $variations[0]['dimensions']['width'] * $variations[0]['dimensions']['height'];
        $volumemessage = '';
    }
  }
}
$alternate_vol = $alternate_vol / 1000000;
$wooshipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
//print_r($wooshipping_settings);
?>


<?php

if(isset($order_data['created_via'])){

  if($order_data['created_via'] == 'admin'){
    //if created via admin set warehouse from postcode
    $warehouse_billing[2] = ( isset($order_data['billing']['postcode']) ) ? $order_data['billing']['postcode'] : $order_data['shipping']['postcode'];
  }
}


loadsrcs();
mnp_display_nav(); ?>
<div class="container">
<?php if($showpage){ ?>

<form class="form-invoice" action="admin.php?page=morkvanp_invoice" method="post" name="invoice">
  <div id="messagebox" class="messagebox_show">
  </div>
  <?php formlinkbox($order_data['id']); ?>
  <div class="tablecontainer">
    <table class="form-table full-width-input">
      <tbody id=tb1>
        <?php formblock_title('Відправник'); ?>
        <?php


        $path = PLUGIN_PATH . '/public/partials/morkvanp-plugin-invoices-page.php';
        if(file_exists($path)){
          $descriptionarea = decodedescription(get_option('invoice_description'), $list2,  $list, $prod_quantity, $prod_quantity2, $order_data['total'] );
        }
        else{
          $descriptionarea = '';
        }
        ?>
        <?php formblock_sender( get_option('names'), get_option('woocommerce_nova_poshta_shipping_method_settings'), get_option('phone'), $descriptionarea ); ?>
       <script>
        var el = document.getElementById('invoice_sender_ref');
el.addEventListener('change', function(e){

  var phone = el.options[el.selectedIndex].getAttribute('phone');
  var phoneel = document.getElementById('sender_phone');
  phoneel.value = phone;
  console.log(phone);
  var namero = el.options[el.selectedIndex].getAttribute('namero');
  var sender_name = document.getElementById('sender_name');
  sender_name.value = namero;


});
       </script>
      </tbody>
    </table>
    <table class="form-table full-width-input">
      <tbody>
        <?php formblock_title('Одержувач'); ?>
        <?php
        $city = '';
        if ( is_plugin_active ( 'premmerce-nova-poshta-premium/premmerce-nova-poshta.php' ) ) {
          $vowels = array("місто", "city", "город");
          $shipping_citypmc = str_replace($vowels, "", $shipping_city);
          $shipping_citypmc = preg_replace('/\s\s+/', ' ', $shipping_citypmc);
          $shipping_citypmc = trim($shipping_citypmc);
          $city = $shipping_citypmc;
        }
        else {
          $shipping_city = preg_replace('/\s\s+/', ' ', $shipping_city);
          $city = $shipping_city;
        }
        //remove bad symbols from billing adress
        $bad_symbols = array('№', ':');
        if(isset( $billing_address[1] )){
          $billing_address[1] = str_replace($bad_symbols, "", $billing_address[1]);
        }

          //form block recipient
          $orderx = new WC_Order( $order_id );
          $shipping_method = @array_shift($orderx->get_shipping_methods());
          $shipping_method_id = $shipping_method['method_id'];

          if( $shipping_method_id == 'np_address_shipping_method'){//print address recipient form block
            $order_message = 'в замовлені обрано адресну доставку новою поштою';
                //formblock_address_recipient($shipping_first_name, $shipping_last_name, $city, $shipping_state, $warehouse_billing[2], $shipping_phone);
            formblock_address_recipient($shipping_first_name, $shipping_last_name, $city, $order_data['billing'], $order_data['shipping'], $shipping_phone);
          }
          else{//print normal recipient form block
            formblock_recipient($shipping_first_name, $shipping_last_name, $city, $shipping_state, $warehouse_billing[2], $shipping_phone);
            //formblock_recipient($shipping_first_name, $shipping_last_name, $city, $order_data['billing'], $order_data['shipping'], $shipping_phone);
          }
        ?>

      </tbody>
    </table>
  </div>
  <div class="tablecontainer">
    <table class="form-table full-width-input">
      <tbody>
        <?php formblock_title('Параметри відправлення'); ?>
        <?php

        $invoice_addweight = floatval(get_option( 'invoice_addweight' ));
        $invoice_allvolume = get_option( 'invoice_allvolume' );

        formblock_param(
          get_option( 'type_example' ),
          get_option( 'invoice_dpay' ),
          get_option( 'invoice_payer'),
          $alternate_weight,
          $invoice_addweight,
          $invoice_allvolume,
          $dimentions,
          $alternate_vol,
          $volumemessage,
          $weighte,
          $order_data
        ); ?>
      </tbody>
    </table>
    <table class="form-table full-width-input">
      <tbody>
        <tr>
          <td>
            <input type="submit" value="Створити" class="checkforminputs button button-primary" id="submit"/>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <?php include 'card.php' ; ?>
  </div>
</form>
<?php } ?>
<?php if(!$showpage){
   echo '<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку замовлення</a></h3>';
 } ?>
</div>



<?php

  $invoice = new MNP_Plugin_Invoice();
  $invoiceController = new MNP_Plugin_Invoice_Controller();

  $invoice->setPosts();

  $owner_address = get_option('warehouse');
  $owner_address = explode(" ", $owner_address);

  if ( empty($owner_address[0] or empty($owner_address[1])) ) {
    $owner_address[0] = "";
    $owner_address[1] = "";
    exit('Поле адреса віділення в налаштуваннях пусте, заповніть його, будь ласка');
  }

  $invoice->sender_street = $owner_address[0];
  if(isset($owner_address[1])){
    $invoice->sender_building = $owner_address[1];
  }
  if(isset( $order_data["total"] )){
  $invoice->order_price = $order_data["total"];
  }

  $invoiceController->isEmpty();

  $bad_symbols = array( '+', '-', '(', ')', ' ' );

  $invoice->sender_phone = str_replace( $bad_symbols, '', $invoice->sender_phone );

  $invoice->cargo_weight = str_replace(".", ",", $invoice->cargo_weight);

  $invoice->register();
  $invoice->getCitySender();
  $invoice->getSender();
 // $invoice->createSenderContact();
  $invoice->senderFindArea();
  $invoice->senderFindStreet();
  $invoice->createSenderAddress();
  $invoice->newFindRecipientArea();
  $invoice->findRecipientArea();
  $invoice->createRecipient();
  $invoice->howCosts();
  $invoice->order_id = $order_data["id"];
  $invoice->createInvoice();

  print_r($invoice);



  $order_id = $order_data["id"];

  if (isset($order_id)) {
    $order = wc_get_order( $order_id );

    $meta_key = 'novaposhta_ttn';
    $meta_values = get_post_meta( $order_id,  $meta_key , true );
    if(empty($meta_values)){
      add_post_meta( $order_id, $meta_key, $_SESSION['invoice_id_for_order'], true );
    }
    else{
      update_post_meta( $order_id, $meta_key, $_SESSION['invoice_id_for_order'] );
    }
    $note = "Номер накладної: " . $_SESSION['invoice_id_for_order'];
    $order->add_order_note( $note );
    $order->save();

    unset( $_SESSION['invoice_id_for_order'] );
  }

if(isset ($_SESSION['req']) ){
  echo "<div style=display:none><p>Запит:</p>".$_SESSION['req']."</div>";
  unset( $_SESSION['req'] );
}
?>
