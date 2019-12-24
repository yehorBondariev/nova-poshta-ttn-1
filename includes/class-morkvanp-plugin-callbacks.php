<?php
/**
 * Registering callbacks for settings admin page
 */
 class MNP_Plugin_Callbacks {
 	public function adminDashboard(){
		return require_once( "$this->plugin_path/templates/admin.php" );
	}

	public function adminInvoice(){
		return require_once( "$this->plugin_path/templates/invoice.php" );
	}

	public function adminSettings(){
		return require_once( "$this->plugin_path/templates/taxonomy.php" );
	}

	public function morkvanpOptionsGroup( $input ){
		return $input;
	}

	public function morkvanpAdminSection(){
		echo 'Введіть свій API ключ для початку щоб плагін міг працювати.';
	}
	public function morkvanpTypeExample(){
		$value = esc_attr( get_option( 'type_example' ) );
    	$values= array('Cargo','Documents','TiresWheels', 'Pallet', 'Parcel' );
    	$volues= array('Вантаж','Документи','Диски', 'Паллети', 'Посилка' );
    	$vilues= array(' ',' ',' ', ' ', '  ');
	    for( $i=0; $i<sizeof($values); $i++){
	      if( $values[$i] == $value){
	        $vilues[$i] = 'selected';
	      }
	    }
		echo '<select '.$value.' id="type_example" name="type_example">';

		    for( $i=0; $i<sizeof($values); $i++){
		      echo '<option '.
		      $vilues[$i] .'
		       value="'.$values[$i].'">
		       '.$volues[$i].'</option>';
		    }
		echo '</select>';

	}

	public function morkvanpTextExample(){
		$value = esc_attr( get_option( 'text_example' ) );
		echo '<input type="text" class="regular-text" name="text_example" value="' . $value . '" placeholder="API ключ">';
		echo '<p>Якщо у вас немає API ключа, то можете отримати його за посиланням <a href="http://my.novaposhta.ua/settings/index#apikeys">my.novaposhta.ua/settings/index#apikeys</a></p>';
	}

	public function morkvanpSelectRegion(){
		$region = esc_attr( get_option( 'region' ) );

		$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings'); //1.6.x support
		$region = $shipping_settings["area_name"]; //1.6.x support

		if(get_option('woocommerce_nova_poshta_shipping_method_area_name')){
			$region = get_option('woocommerce_nova_poshta_shipping_method_area_name');
		}


		echo '<input type="text" class="input-text regular-input  ui-autocomplete-input" name="woocommerce_nova_poshta_shipping_method_area_name" id="woocommerce_nova_poshta_shipping_method_area_name" value="' . $region . '" placeholder=" " readonlyd>';



		$regionid = get_option('woocommerce_nova_poshta_shipping_method_area');

		echo '<input class="input-text regular-input jjs-hide-nova-poshta-option" type="hidden" name="woocommerce_nova_poshta_shipping_method_area" id="woocommerce_nova_poshta_shipping_method_area" style="" value="'.$regionid.'" placeholder="">';
	}

	public function morkvanpSelectCity()
	{
		$value1 = esc_attr( get_option( 'city' ) );

		/**
		 * Getting settings of WooShipping plugin
		 */
		$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
		$value1 = $shipping_settings["city_name"];



		if(get_option('woocommerce_nova_poshta_shipping_method_city_name')){
			$value1 = get_option('woocommerce_nova_poshta_shipping_method_city_name');
		}

		echo '<input type="text" class="input-text regular-input  ui-autocomplete-input" name="woocommerce_nova_poshta_shipping_method_city_name" id="woocommerce_nova_poshta_shipping_method_city_name" value="' . $value1 . '" placeholder=" " readonlyd>';


		if(get_option('woocommerce_nova_poshta_shipping_method_city')){
			$city = get_option('woocommerce_nova_poshta_shipping_method_city');
		}


		echo '<input class="input-text regular-input" type="hidden" name="woocommerce_nova_poshta_shipping_method_city" id="woocommerce_nova_poshta_shipping_method_city" style="" value="'.$city.'" placeholder="">';

	}



	public function morkvanpPhone() {
		$phone = esc_attr( get_option( 'phone' ) );
		echo '<input type="text" class="regular-text" name="phone" value="' . $phone . '" placeholder="380901234567">';
		echo '<p>Підказка: вводьте телефон у таком форматі 380901234567</p>';
	}

	public function morkvanpNames() {
		$names = esc_attr( get_option( 'names' ) );
		echo '<input type="text" class="regular-text" name="names" value="' . $names . '" placeholder="Петронко Петро Петрович">';
	}

  public function morkvanpCheckoutExample(){
    $value = esc_attr( get_option( 'morkvanp_checkout_count' ) );
    $values= array('3fields', '2fields');
    $volues= array('Область + Місто + Відділення', 'Місто + Відділення' );
    $vilues= array(' ',' ',' ', ' ', '  ');
    for( $i=0; $i<sizeof($values); $i++){
      if( $values[$i] == $value){
        $vilues[$i] = 'selected';
      }
    }

    echo '<select '.$value.' id="morkvanp_checkout_count" name="morkvanp_checkout_count">';

    for( $i=0; $i<sizeof($values); $i++){
      echo '<option '.
      $vilues[$i] .'
       value="'.$values[$i].'">
       '.$volues[$i].'</option>';
    }

    echo '</select>';

  }

	public function morkvanpFlat(){
		$flat = esc_attr( get_option( 'flat' ) );
		echo '<input type="text" class="regular-text" name="flat" value="' . $flat . '" placeholder="номер">';
	}
  public function emptyfunccalbask(){
    echo '';
  }
	public function morkvanpWarehouseAddress(){
		// $warehouse = esc_attr( get_option( 'warehouse' ) );
		$shipping_settings = get_option('woocommerce_nova_poshta_shipping_method_settings');
		// $shipping_settings["warehouse_name"];
		$warehouse = $shipping_settings["warehouse_name"];


		if(get_option('woocommerce_nova_poshta_shipping_method_warehouse_name')){
			$warehouse = get_option('woocommerce_nova_poshta_shipping_method_warehouse_name');
		}

		echo '<input type="text" class="input-text regular-input  ui-autocomplete-input" id="woocommerce_nova_poshta_shipping_method_warehouse_name" name="woocommerce_nova_poshta_shipping_method_warehouse_name" value="' . $warehouse . '" placeholder="" readonlyd>';


		if(get_option('woocommerce_nova_poshta_shipping_method_warehouse')){
			$warehouseid = get_option('woocommerce_nova_poshta_shipping_method_warehouse');
		}

		echo '<input class="input-text regular-input jjs-hide-nova-poshta-option" type="hidden" name="woocommerce_nova_poshta_shipping_method_warehouse" id="woocommerce_nova_poshta_shipping_method_warehouse" style="" value="'.$warehouseid.'" placeholder="">';

	    ///echo '<p>Налаштування полей міста і регіона беруться із налаштувань плагіну <a href="admin.php?page=wc-settings&tab=shipping&section=nova_poshta_shipping_method">Woocommerce</a></p>';
	}

	public function morkvanpInvoiceDescription(){
		$invoice_description = get_option('invoice_description');

		echo '<textarea  id=td45 name="invoice_description" rows="5" cols="54">' . $invoice_description . '</textarea>
		<span id=sp1 class=shortspan>+ Вартість</span>
		<select class=shortspan id=shortselect>
			<option value="0" disabled selected style="display:none"> + Перелік</option>
			<option value="list" > + Перелік товарів (з кількістю)</option>
			<option value="list_qa"> + Перелік товарів ( з артикулами та кількістю)</option>
		</select>
		<select class=shortspan id=shortselect2>
			<option value="0" disabled selected style="display:none"> + Кількість</option>
			<option value="qa"> + Кількість позицій</option>
			<option value="q"> + кількість товарів</option>
		</select>
		<p>значення шорткодів, при натисненні кнопок додаються в кінець текстового поля</p>
		';
	}

	public function morkvanpInvoiceAddWeight(){
		$invoice_description = get_option('invoice_addweight');

		echo '<input name="invoice_addweight" value="' . $invoice_description . '"/> <p>Вага в кілограмах. Буде додано до ваги покупок при оформленні відправлення на сайті. Використовуйте пусте значення або нуль, якщо не використовуєте власну упаковку для замовлень.</p>';
	}

	public function morkvanpInvoiceAllvol(){
		$invoice_description = get_option('invoice_allvolume');

		echo '<input name="invoice_allvolume" value="' . $invoice_description . '"/> <p>Об\'єм тари (м<sup>3</sup>).  Буде використовуватись якщо буде більшим за об\'єм покупок. Якщо замовлення не вміщується у вашу тару, при створенні накладно буде показано відповідне попередження.  Використовуйте пусте значення або нуль, якщо не використовуєте власну упаковку або тару для замовлень.</p>';
	}




	public function morkvanpInvoiceWeight(){
		$activate = get_option( 'invoice_weight' );
		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input type="checkbox" class="regular-text" name="invoice_weight" value="1" ' . checked($checked, $current, $echo) . ' />';
	}

	public function morkvanpInvoiceDate(){
		$activate = get_option( 'invoice_date' );
		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input type="checkbox" class="regular-text" name="invoice_date" value="1" ' . checked($checked, $current, $echo) . ' />';
	}

  public function morkvanpInvoiceautottn(){
		$activate = get_option( 'autoinvoice' );

		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input '. $activate .' type="checkbox" class="regular-text" name="autoinvoice" value="1" ' . checked($checked, $current, $echo) . ' /><p>Накладні, за можливості, формуватимуться автоматично при оформленні замовлення. <br><strong style="color:#a55">Функція ще в процесі тестування, тому перевіряйте правильність створення накладних за <a href=admin.php?page=morkvanp_about#test>посиланням</a></strong> </p>';
	}


	  public function morkvanp_address_shpping_notuse(){
		$activate = get_option( 'np_address_shpping_notuse' );

		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input '. $activate .' type="checkbox" class="regular-text" name="np_address_shpping_notuse" value="1" ' . checked($checked, $current, $echo) . ' />За замовчуванням використовується адресна доставка. Проте цей пункт дозволить її вимкнути.';
	}

  public function morkvanpzone(){
  $activate = get_option( 'zone_example' );

  $checked = $activate;
  $current = 1;
  $echo = false;
  echo '<input '. $activate .' type="checkbox" class="regular-text" name="zone_example" value="1" ' . checked($checked, $current, $echo) . ' />За замовчуванням зони не використовуються. Проте якщо вам потрібно настроїти зони доставки, використовуйте цей пункт.<p>Якщо після настройок <a href="admin.php?page=wc-settings&tab=shipping">тут</a> не відображається метод доставки при оформленні замовлення вимкніть цей пункт</p>';
}

  public function morkvanpcalc(){
  $activate = get_option( 'show_calc' );

  $checked = $activate;
  $current = 1;
  $echo = false;
  echo '<input '. $activate .' type="checkbox" class="regular-text" name="show_calc" value="1" ' . checked($checked, $current, $echo) . ' />Сума доставки не включається у замовлення за замовчуванням, хоч і відображається у настройках. </p>';
}


	public function morkvanpInvoicecod(){
		$activate = get_option( 'invoice_cod' );

		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input '. $activate .' type="checkbox" class="regular-text" name="invoice_cod" value="1" ' . checked($checked, $current, $echo) . ' /><p><b>Актуально якщо ви не користєтесь наложеним платежем. </b>Якщо при оформленні обрана оплата при отриманні, то ввімкнутий тумблер увімкне її підхоплення при створенні накладної.</p>';
	}

	public function morkvanpInvoiceshort(){
		$activate = get_option( 'invoice_short' );

		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input '. $activate .' type="checkbox" class="regular-text" name="invoice_short" value="1" ' . checked($checked, $current, $echo) . ' /><p>якщо увімкнено, функціонал плагіна розширюється можливістю використовувати шорткоди</p>';
	}

	public function morkvanpInvoicedpay(){
		$invoice_dpay = get_option( 'invoice_dpay' );
		$current = 1;
		$echo = false;
		echo '<input value="'. $invoice_dpay .'" type="text" class="regular-text" name="invoice_dpay"   /><p>Вимкнено, якщо  порожнє або  нуль. Оплата за доставку: якщо сума замовлення більша '. $invoice_dpay .' грн - оплачує відправник по безготівковому розрахунку, якщо сумаа замовлення менше '. $invoice_dpay .' грн - за доставку платить отримувач, готівка. При створенні накладної вимикається графа вибору платника а доставку. це відбувається автоматично.</p>';
	}


public function morkvanpInvoicepayer(){
		 		$value =  get_option( 'invoice_payer' ) ;

    $values= array('0','1' );
    $volues= array('Отримувач','Відправник');
    $vilues= array('', '');
    for( $i=0; $i<sizeof($values); $i++){
      if( $values[$i] == $value){
        $vilues[$i] = 'selected';
      }

    }

		echo '<select '.$value.' id="invoice_payer" name="invoice_payer">
		<p> </p>';


    for( $i=0; $i<sizeof($values); $i++){
      echo '<option '. $vilues[$i] .' value="'.$values[$i].'">'.$volues[$i].'</option>';
    }

    echo '</select>';

	}


	public function morkvanpInvoicezpayer(){
		 		$value =  get_option( 'invoice_zpayer' ) ;

    $values= array('0','1' );
    $volues= array('Отримувач','Відправник');
    $vilues= array('', '');
    for( $i=0; $i<sizeof($values); $i++){
      if( $values[$i] == $value){
        $vilues[$i] = 'selected';
      }

    }

		echo '<select '.$value.' id="invoice_zpayer" name="invoice_zpayer">
		<p> </p>';


    for( $i=0; $i<sizeof($values); $i++){
      echo '<option '. $vilues[$i] .' value="'.$values[$i].'">'.$volues[$i].'</option>';
    }

    echo '</select>';

	}



  public function morkvanpInvoicecron(){
		$invoice_dpay = get_option( 'invoice_cron' );

		$crontime = intval($invoice_dpay);

    $textt = '';

    if($crontime == -1){
      $textt = 'Крон вимкнуто. Якщо не бажаєте оновлювати статуси автоматично, а лише перейшовши на сторінку замовлення, не змінюйте значення -1.';
    }
    else if($crontime == 0){
      $textt = 'Значення не встановлено або 0. Крон відбуватиметься кожні 60 хв. Якщо не бажаєте оновлювати статуси автоматично, а лише перейшовши на сторінку замовлення, встановіть значення -1.';
    }
    else{
      $textt = 'Крон завдання відбуватиметься кожні '.$crontime.' хвилин.';
    }

		$echo = false;
		echo '<input value="'. $invoice_dpay .'" type="number" class="regular-text" name="invoice_cron"   /><p>
    Крон завдання для автоматичного оновлення статусів замовлення(хвилин). '.$textt.' Рекомендоване значення - 60 хвилин </p>';
	}

  public function morkvanpInvoiceauto(){
    $checked = get_option( 'invoice_auto' );
    $current = 1;
    $echo = false;
    echo '<input '. $checked .' type="checkbox" class="regular-text" name="invoice_auto"  value="1" ' . checked($checked, $current, $echo) . ' /><p>
    Крон завдання для автоматичного оновлення статусів замовлення.'. $checked .'</p>';
  }

public function morkvanpInvoicecpay(){
		$activate = get_option( 'invoice_cpay' );

		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input '. $activate .' type="checkbox" class="regular-text" name="invoice_cpay" value="1" ' . checked($checked, $current, $echo) . '" /><p><b>Контроль платежу доступний тільки для юридичних осіб які уклали таку угоду з НП.</b> <br>
		Гроші за "наложку" зараховуються на рахунок компанії і оподатковуються.<br> Якщо увімкнено, функціонал плагіна розширюється можливістю Формування запиту на створення «ЕН» з послугою «Контроль оплати» </p>';
	}


public function morkvanpInvoicetpay(){

	$value =  get_option( 'invoice_tpay' ) ;

    $values= array('Cash','NonCash' );
    $volues= array('Готівка','Безготівковий розрахунок');
    $vilues= array('', '');
    for( $i=0; $i<sizeof($values); $i++){
      if( $values[$i] == $value){
        $vilues[$i] = 'selected';
      }

    }

		echo '<select '.$value.' id="invoice_tpay" name="invoice_tpay">
		<p>якщо доставку оплачує отримувач, тоді тип оплати за замовчуванням готівка</p>';


    for( $i=0; $i<sizeof($values); $i++){
      echo '<option '. $vilues[$i] .' value="'.$values[$i].'">'.$volues[$i].'</option>';
    }

    echo '</select>';
	}

	public function morkvanpEmailTemplate(){
		$content = get_option( 'morkvanp_email_template' );
		$editor_id = 'morkvanp_email_editor_id';
		wp_editor( $content, $editor_id, array( 'textarea_name' => 'morkvanp_email_template', 'tinymce' => 0, 'media_buttons' => 0 ) );

		echo '<span id=standarttext title="щоб встановити шаблонний текст, натисніть">Шаблон email</span>';
	}

	public function morkvanpEmailSubject(){
		$subject = get_option( 'morkvanp_email_subject' );
		echo '<input type="text" name="morkvanp_email_subject" class="regular-text" value="' . $subject . '" />';
	}



 }
