<?php

session_start();
/**
 * Provide a functions of Invoice controller
 *
 *
 * @link       http://morkva.co.ua
 * @since      1.0.0
 *
 * @package    morkvanp-plugin
 * @subpackage morkvanp-plugin/public/partials
 */

require("morkvanp-plugin-invoice-controller.php");
include("morkvanp-plugin-invoice.php");

if ( isset($_SESSION['order_data']) ) { $order_data = $_SESSION['order_data']; }


$showpage = true;
if(isset($_SERVER['HTTP_REFERER'])) {
    $qs = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if(!empty($qs)){
        parse_str($qs, $output);
        // TODO check for key existence
        if (isset($output['post'])){
        $order_id =  $output['post'];
        }
      }
}
if(isset($order_id)){
        $order_data0 = wc_get_order( $order_id );
        $order_data = $order_data0->get_data();
        $_SESSION['order_data'] = $order_data;
}
else if ( isset($_SESSION['order_data']) ) {
  echo '<script>console.log("session order id:'.$_SESSION['order_data']['id'].'");</script>';
  $order_data = $_SESSION['order_data'];
}
else{
  $showpage =false;
}


if ( isset($order_data["billing"]["address_1"]) ) {
 	$billing_address = $order_data["billing"]["address_1"];
	$warehouse_billing = explode(" ", $billing_address);
	$warehouse_billing = str_replace([":", "№"], "", $warehouse_billing);

	if (empty($warehouse_billing[1])) {
		unset($warehouse_billing[1]);
	} else {
		$warehouse_billing[2] = $warehouse_billing[1];
	}
} else if ( isset( $order_data['shipping']['address_1'] ) ) {
	$billing_address = $order_data["shipping"]["address_1"];
	$warehouse_billing = explode(" ", $billing_address);
	$warehouse_billing = str_replace([":", "№"], "", $warehouse_billing);

	if (empty($warehouse_billing[1])) {
		unset($warehouse_billing[1]);
	} else {
		$warehouse_billing[2] = $warehouse_billing[1];
	}
} else {
	$billing_address[0] = "";
	$billing_address[1] = "";
}

if ( isset($order_data["billing"]["first_name"]) ) {
 	$shipping_first_name = $order_data["billing"]["first_name"];
} else if ( isset( $order_data['shipping']['first_name'] ) ) {
	$shipping_first_name = $order_data["shipping"]["first_name"];
} else {
	$shipping_first_name = "";
}

if ( isset($order_data["billing"]["last_name"]) ) {
	$shipping_last_name = $order_data["billing"]["last_name"];
} else if ( isset( $order_data["shipping"]["last_name"] ) ) {
	$shipping_last_name = $order_data["shipping"]["last_name"];
} else {
	$shipping_last_name = "";
}

if ( isset($order_data["billing"]["address_2"]) ) {
	$shipping_address = $order_data["billing"]["address_2"];
	$shipping_address = explode(" ", $shipping_address);
} else if ( isset( $order_data["shipping"]["address_2"] ) ) {
	$shipping_address = $order_data["shipping"]["address_2"];
	$shipping_address = explode(" ", $shipping_address);
} else {
	$shipping_address[0] = "";
	$shipping_address[1] = "";
}
/* OTHER GETTING DATA FUNCTIONS */

if ( isset($order_data["billing"]["city"]) ) {
	$shipping_city = $order_data["billing"]["city"];
} else if ( isset( $order_data["shipping"]["city"] ) ) {
	$shipping_city = $order_data["shipping"]["city"];
} else {
	$shipping_city = "";
}

if ( isset($order_data["billing"]["state"]) ) {
	$shipping_state = $order_data["billing"]["state"];
} else if ( isset( $order_data["shipping"]["state"] ) ) {
	$shipping_state = $order_data["shipping"]["state"];
} else {
	$shipping_state = "";
}

if ( isset($order_data["billing"]["phone"]) ) {
	$shipping_phone = $order_data["billing"]["phone"];
} else if ( isset( $order_data["shipping"]["phone"] ) ) {
	$shipping_phone = $order_data["shipping"]["phone"];
} else {
	$shipping_phone = "";
}
?>

<script src="<? echo plugins_url('nova-poshta-ttn/public/js/script.js'); ?>"></script>

<link rel="stylesheet" href="<? echo plugins_url('nova-poshta-ttn/public/css/style.css'); ?>"/>


<div class="container">
  <? if($showpage){ ?>
	<form class="form-invoice" action="admin.php?page=morkvanp_invoice" method="post" name="invoice">
    <div id="messagebox" class="messagebox_show"></div>
    <?php
        if ( !empty( $order_data["id"] ) ) {
          echo '<a class="btn" href="/wp-admin/post.php?post=' . $order_data["id"] . '&action=edit">Повернутись до замовлення</a>';
          echo '';
        }
      ?>
		<div class="tablecontainer">
			<table class="form-table full-width-input">
				<tbody>
					<tr>
				       <th colspan="2">
				          <h3 class="formblock_title">Відправник</h3>
				       </th>
				    </tr>
				    <tr>
			          <th scope="row">
			            <label for="sender_name">Відправник (П. І. Б)</label>
			          </th>
			          <td>
			            <input type="text" id="sender_name" name="invoice_sender_name" class="input sender_name" value="<?php echo get_option('names'); ?>" />
			          </td>
			        </tr>

			        <tr>
			          <th scope="row">
			            <label for="sender_name">Місто</label>
			          </th>
			          <td>
			            <!-- <select name="invoice_sender_city" class="sender_city">
			  						<p>
			  						<p>
			  						<option value="<?php echo $shipping_settings["city_name"]; ?>"><?php echo get_option('city'); ?></option>
			  						<option value="Київ">Київ</option>
			  						<option value="Львів">Львів</option>
			  						<option value="Івано-Франківськ">Івано-Франківськ</option>
			  						<option value="Вінниця">Вінниця</option>
			  						<option value="Дніпро">Дніпро</option>
			  						<option value="Одеса">Одеса</option>
			  						<option value="Харків">Харків</option>
			  						<option value="Херсон">Херсон</option>
			  						<option value="Донецьк">Донецьк</option>
			  						<option value="Кривий Ріг">Кривий Ріг</option>
			  						<option value="Луцьк">Луцьк</option>
			  						<option value="Житомир">Житомир</option>
			  						<option value="Чернігів">Чернігів</option>
			  						<option value="Кам'янське">Кам'янське</option>
			  						<option value="Чернівці">Чернівці</option>
			  						<option value="Ужгород">Ужгород</option>
			  						<option value="Суми">Суми</option>
			  						<option value="Миколаїв">Миколаїв</option>

			  						<option value="Полтава">Полтава</option>
			  						<option value="Кам'янець-Подільське">Кам'янець-Подільське</option>
			  						<option value="Рівне">Рівне</option>
			  						<option value="Біла Церква">Біла Церква</option>
			  						<option value="Мукачево">Мукачево</option>
			  						</p>
			  						<option>Переяслав-Хмельницький</option>
			  						<option>Дрогобич</option>
			  						<option>Білгород-Дністровський</option>
			  						<option>Коломия</option>
			  						<option>Макіївка</option>
			  					</select> -->
			  					<?php
			  							$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
			  						?>
			  					<input type="text" value="<?php echo $shipping_settings["city_name"]; ?>" readonly name="invoice_sender_city" />
			          </td>
			        </tr>
			        <tr>
			          <th scope="row">
			            <label for="sender_phone">Телефон</label>
			          </th>
			          <td>
			            <input type="text" id="sender_phone" name="invoice_sender_phone" class="input sender_phone" value="<?php echo get_option('phone'); ?>" />
			          </td>
			        </tr>
			        <tr>
			          <th scope="row">
			            <label for="invoice_description">Опис відправлення</label>
			          </th>
			          <td>
			            <textarea  type="text" id="invoice_description" name="invoice_description" class="input" minlength="1" required><? echo get_option('invoice_description'); ?></textarea>
			  					<p id="error_dec"></p>
			          </td>
			        </tr>
				</tbody>
			</table>
			<table class="form-table full-width-input">
				<tbody>
					<tr>
			          <th colspan=2>
			            <h3 class="formblock_title">Параметри відправлення</h3>
			          </th>
			        </tr>
			        <tr>
			          <th scope="row">
			            <label for="sender_cargo">Тип вантажу</label>
			          </th>
			          <td>
			            <select id="sender_cargo" name="invoice_cargo_type">
			  						<option value="Cargo">Вантаж</option>
			  						<option value="Documents">Документи</option>
			  						<option value="TiresWheels">Шини Диски</option>
			  						<option value="Pallet">Паллети</option>
			  						<option value="Parcel">Посилка</option>
			  					</select>
			          </td>
			        </tr>
			        <tr>
			          <th colspan=2>
			            <p>
			    				Платник по замовчуванню отримувач.
			    				Оголошена вартість автоматично дорівнює вартості замовлення.
			    				</p>
			          </th>
			        </tr>
				</tbody>
			</table>
		</div>
		<div class="tablecontainer">
			<table class="form-table full-width-input">
				<tbody>
					<tr>
			          <th colspan=2>
			            <h3 class="formblock_title">Одержувач</h3>
			          </th>
			        </tr>
			        <tr>
			          <th scope="row">
			            <label for="recipient_name">Одержувач (П.І.Б)</label>
			          </th>
			          <td>
			            <input type="text" name="invoice_recipient_name" id="recipient_name" class="input recipient_name" value="<?php echo $shipping_first_name . " " . $shipping_last_name; ?>" />
			          </td>
			        </tr>
			        <tr>
			          <th scope="row">
			            <label for="recipient_city">Місто</label>
			          </th>
			          <td>
			            <input type="text" name="invoice_recipient_city" id="recipient_city" class="recipient_city" value="<?php echo $shipping_city; ?>" readonly/>
			          </td>
			        </tr>
					<tr>
			          <th scope="row">
			            <label for="invoice_recipient_region">Область</label>
			          </th>
			          <td>
			            <input type="text" name="invoice_recipient_region" class="input recipient_region" value="<?php echo $shipping_state; ?>" readonly/>
			          </td>
			        </tr>
			 <?php
				$bad_symbols = array('№', ':');
				$billing_address[1] = str_replace($bad_symbols, "", $billing_address[1]);
			?>
					<tr>
				        <th scope="row">
				          <label for="sender_name">Номер відділення</label>
				        </th>
				        <td>
				          <input type="text" name="invoice_recipient_warehouse" class="input recipient_region" value="<?php echo $warehouse_billing[2]; ?>" />
				        </td>
				    </tr>
				    <tr>
				        <th scope="row">
				          <label for="sender_name">Телефон</label>
				        </th>
				        <td>
				          <input type="text" name="invoice_recipient_phone" class="input recipient_phone" id="recipient_phone" value="<?php echo $shipping_phone; ?>" />
				        </td>
				      </tr>
				      <tr>
				          <td>
				            <input type="submit" value="Створити" class="checkforminputs button button-primary" id="submit"/>
				          </td>
				      </tr>

				</tbody>
			</table>
		</div>


		<div>
		    <div class="updatedpro mtb16 p0">
			    <div class="card">
					<div class="card-header">
						<h3>
						Підтримка
						</h3>
					</div>

				<div class="card-body">
					<p>
						Якщо у вас виникли проблеми із створенням накладної або щось інше, то звертайтесь до нашої підтримки в Facebook.
					</p>
					<h5><a href="https://www.facebook.com/groups/morkvasupport" class="wpbtn button button-primary" target="_blank"><?php echo '<img class="imginwpbtn" src="' . plugins_url('img/messenger.png', __FILE__) . '" height="25" width="25"  />'; ?> Написати в чат</a></h5>
				</div>
			</div>

			<div class="card">
			<div class="card-header">
				<h3>Pro версія</h3>
			</div>
			<div class="card-body">
				<p>
				Потрібно більше можливостей? Оновіться до Pro-версії зараз!
				</p>
				<h5><a href="https://www.morkva.co.ua/woocommerce-plugins/avtomatychna-heneratsiia-nakladnykh-nova-poshta/" class="button button-primary">Хочу Pro</a></h5>
			</div>
			</div>
		</div>
	  	</div>
	</form>
<? } ?>
<? if(!$showpage){ echo '<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку замовлення</a></h3>';} ?>

</div>

<?php
	$invoice = new MNP_Plugin_Invoice();
	$invoiceController = new MNP_Plugin_Invoice_Controller();

	$invoice->setPosts();

	$owner_address = get_option('warehouse');
	$owner_address = explode(" ", $owner_address);


	$bad_symbols = array( '+', '-', '(', ')', ' ' );

  	$invoice->sender_phone = str_replace( $bad_symbols, '', $invoice->sender_phone );



	if ( empty($owner_address[0] or empty($owner_address[1])) ) {
		$owner_address[0] = "";
		$owner_address[1] = "";
		exit('Поле адреса віділення в налаштуваннях пусте, заповніть його, будь ласка');
	}

	$invoice->sender_street = $owner_address[0];
	$invoice->sender_building = $owner_address[1];

	$invoiceController->isEmpty();

	$invoice->register();
	$invoice->getCitySender();
	$invoice->getSender();
	$invoice->createSenderContact();
	$invoice->senderFindArea();
	$invoice->senderFindStreet();
	$invoice->createSenderAddress();
	$invoice->newFindRecipientArea();
	$invoice->findRecipientArea();
	$invoice->createRecipient();
	$invoice->howCosts();
	$invoice->order_id = $order_data["id"];
	$invoice->createInvoice();

	$order_id = $order_data["id"];

	if (isset($order_id)) {
		$order = wc_get_order( $order_id );

    //print_r($_SESSION);
		$note = "Номер накладної: " . $_SESSION['invoice_id'];
		$order->add_order_note( $note );
		$order->save();
	}

?>
