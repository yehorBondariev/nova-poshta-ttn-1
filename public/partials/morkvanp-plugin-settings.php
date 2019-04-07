<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public/partials
 */
?>

<script src="<? echo plugins_url('nova-poshta-ttn/public/js/script.js'); ?>"></script>

<link rel="stylesheet" href="<? echo plugins_url('nova-poshta-ttn/public/css/style.css'); ?>"/>


<div class="container">
<div class="row">
	<h1>Nova Poshta TTN</h1>
	<?php settings_errors(); ?>
	<hr>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="tab-click nav-tab nav-tab-active" href="#tab-1">Налаштування</a>
		<a class="tab-click nav-tab " href="#tab-3">Про плагін</a>
	</nav>
	<div class="grr">
	<div class="col-sm-7">
	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<form method="post" action="options.php">
				<?php 
					settings_fields( 'morkvanp_options_group' );
					do_settings_sections( 'morkvanp_plugin' );
					submit_button();
				?>
				<?php
					$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');

					$warehouse = $shipping_settings["warehouse_name"];
					$warehouse_full = explode(" ", $warehouse);

					// echo "<pre>Warehouse full: ";
					// var_dump($shipping_settings);
					// echo "</pre>";
					$warehouse_number = $warehouse_full["1"];
					$warehouse_number = str_replace("№", "", $warehouse_number);
					// echo "<pre>Warehouse number: ";
					// var_dump($warehouse_number);
					// echo "</pre>";

					$new_arr = implode(" ", $warehouse_full);
					// echo "<pre>New arr:";
					// var_dump($new_arr);
					// echo "</pre>";

					$sup_arr = explode(":", $new_arr);
					// echo "<pre>Super arr: ";
					// var_dump($sup_arr);
					// echo "</pre>";

					$street_name = $sup_arr[1];
					$street_name = trim($street_name);
					// echo "<pre>Street name:";
					// var_dump($street_name);
					// echo "</pre>";

					$street_name = explode("вул.", $street_name);
					$street_name = implode(" ", $street_name);
					$street_name = trim($street_name);
					// echo "<pre>Street name: ";
					// var_dump($street_name);
					// echo "</pre>";

					$street_name = explode(",", $street_name);
					// echo "<pre>Street name: ";
					// var_dump($street_name);
					// echo "</pre>";

					$street_name_full = $street_name[0];
					$street_number = $street_name[1];
					$street_number = trim($street_number);
				?>
				<p>
					Якщо у вас немає API ключа, то можете отримати його за посиланням <a href="http://my.novaposhta.ua/settings/index#apikeys">my.novaposhta.ua/settings/index#apikeys</a>
				</p>
			</form>	
		
		</div>

		<div id="tab-3" class="tab-pane">
			<div>
			<p>
				Плагін автоматично генерує накладну з даних про клієнта (ім’я, прізвище, номер телефону). Вам залишиться лише скопіювати номер накладної і відправити клієнту у смс/вайбер/email.
			</p>
		</div>
		
		<div>
			<h2>
				Як згенерувати накладну?
			</h2>
			<p>
				<li>1.Натисніть “Створити експрес накладну”</li>
				<li>2.Введіть опис товару</li>
				<li>3.Введіть дату відправлення</li>
				<li>4.Натисніть “Згенерувати”</li>
			</p>
			<p>
				Плагін працює з типом доставки Відділення-Відділення.
			</p>
		</div>
		<div>
			<h2>
				Налаштування
			</h2>
			<p>
				Для роботи плагіну необхідно встановити плагін доставки Woo Shipping for Nova Poshta <a href="https://wordpress.org/plugins/woo-shipping-for-nova-poshta/">https://wordpress.org/plugins/woo-shipping-for-nova-poshta/</a>
			</p>
			
			<p>
				<li>1.Встановіть плагін через меню Plugins</li>
				<li>2.Введіть ваш АРІ ключ (можна отримати тут: https://my.novaposhta.ua/)</li>
				<li>3.Введіть реквізити Відправника</li>
			</p>
		</div>
		
		<div>
			<h2>
				Потрібно більше функцій? 
			</h2>
			<p>
				Напишіть нам: hello@morkva.co.ua
			</p>
		</div>
		
		<div>
			<h2>
				Підтримка
			</h2>
			<p>
				Виникли проблеми з плагіном? Пишіть нам на support@morkva.co.ua<br />
Або на нашу сторінку у ФБ: <a href="https://www.facebook.com/morkvasite/">https://www.facebook.com/morkvasite/</a><br />

Підтримка безкоштовної версії відбувається у вільний від комерційних проектів час. Просимо це врахувати. Придбайте плагін та отримайте пріоритетну підтримку.

			</p>
		</div>
		</div>
		<div class="clr"></div>
	</div>
	</div>
	<div  class=" mtb16">
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
				<h5><a href="https://www.facebook.com/groups/morkvasupport"  class="wpbtn button button-primary" target="_blank"><?php echo '<img class="imginwpbtn" src="' . plugins_url('img/messenger.png', __FILE__) . '"  />'; ?> Написати в чат</a></h5>
			</div>
		</div>
		<div class="card border-primary">
			<div class="card-header">
				<h3>Pro-версія</h3>
			</div>
			<div class="card-body">
				<p>
				Потрібно більше можливостей? Оновіться до Pro-версії зараз!
				</p>
				<h5><a href="https://www.morkva.co.ua/woocommerce-plugins/avtomatychna-heneratsiia-nakladnykh-nova-poshta/" class="button button-primary" >Хочу Pro</a></h5>
			</div>
		</div>
	</div>
</div>
</div>
</div>
