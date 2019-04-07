<?php

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

class MNP_Plugin_Invoice_Controller {

	#--------------------          Sender Data      --------------------

	public $sender_names;

	public $sender_city;

	public $sender_phone;

	public $sender_contact;

	public $sender_contact_phone;

	public $sender_street;

	public $sender_building;

	public $sender_flat;

	#--------------------            Cargo Data      --------------------

	public $cargo_type;

	public $cargo_volume_type;

	public $cargo_weight;

	#----------------------      Recipient Data    ----------------------

	public $recipient_city;

	public $recipient_area;

	public $recipient_area_regions;

	public $recipient_address_name;

	public $recipient_house;

	public $recipient_flat;

	public $recipient_name;

	public $recipient_phone;

	public $datetime;

	public $invoice_description;

	#--------------------          Response Data     --------------------

	public $sender_response;

	public $sender_err;

	#--------------------          POST Data         --------------------

	public function setPosts()
	{	

		#-----------------------    Sender POST Data Is Here ------------------------

		if ( isset( $_POST['invoice_sender_name'] ) ) { $this->sender_names = $_POST['invoice_sender_name']; }
		if ( isset( $_POST['invoice_sender_city'] ) ) { $this->sender_city = $_POST['invoice_sender_city']; }
		if ( isset( $_POST['invoice_sender_phone'] ) ) { $this->sender_phone = $_POST['invoice_sender_phone']; }
		if ( isset( $_POST['invoice_sender_contact'] ) ) { $this->sender_contact = $_POST['invoice_sender_contact']; }
		if ( isset( $_POST['sender_contact_phone'] ) ) { $this->sender_contact_phone = $_POST['sender_contact_phone']; }
		if ( isset( $_POST['invoice_sender_street'] ) ) { $this->sender_street = $_POST['invoice_sender_street']; }
		if ( isset( $_POST['invoice_sender_building'] ) ) { $this->sender_building = $_POST['invoice_sender_building']; }
		if ( isset( $_POST['sender_flat'] ) ) { $this->sender_flat = $_POST['sender_flat']; }

		#-----------------------    Recipient POST Data Is Here ------------------------

		if ( isset( $_POST['invoice_recipient_name'] ) ) { $this->recipient_name = $_POST['invoice_recipient_name']; }
		if ( isset( $_POST['invoice_recipient_city'] ) ) { $this->recipient_city = $_POST['invoice_recipient_city']; }
		if ( isset( $_POST['invoice_recipient_building'] ) ) { $this->recipient_house = $_POST['invoice_recipient_building']; }
		if ( isset( $_POST['invoice_recipient_region'] ) ) { $this->recipient_area_regions = $_POST['invoice_recipient_region']; }
		if ( isset( $_POST['invoice_recipient_warehouse'] ) ) { $this->recipient_address_name = $_POST['invoice_recipient_warehouse']; }
		if ( isset( $_POST['invoice_recipient_datetime'] ) ) { $this->datetime = $_POST['invoice_recipient_datetime']; }
		if ( isset( $_POST['invoice_recipient_phone'] ) ) { $this->recipient_phone = $_POST['invoice_recipient_phone']; }
		if ( isset( $_POST['invoice_description'] ) ) { $this->invoice_description = $_POST['invoice_description']; }

		#-----------------------    Cargo POST Data Is Here ------------------------

		if ( isset( $_POST['invoice_cargo_type'] ) ) { $this->cargo_type = $_POST['invoice_cargo_type']; }
		if ( isset( $_POST['invoice_cargo_mass'] ) ) { $this->cargo_weight = $_POST['invoice_cargo_mass']; }

		return $this;
	}

	public function isEmpty()
	{
		
		if ( empty( $_POST['invoice_sender_name'] ) ) {
			$this->deleteData();
			exit('Будь ласка, заповніть усі поля.');
		} else if ( empty( $_POST['invoice_sender_city'] ) ) {
			$this->deleteData();
			exit;
		}

		return $this;

	}

	public function deleteData()
	{

		unset($this->sender_names);
		unset($this->sender_city);
		unset($this->sender_phone);
		unset($this->sender_contact);
		unset($this->sender_contact_phone);
		unset($this->sender_street);
		unset($this->sender_building);
		unset($this->sender_flat);
		unset($this->cargo_type);
		unset($this->cargo_weight);
		unset($this->recipient_city);
		unset($this->recipient_area);
		unset($this->recipient_area_regions);
		unset($this->recipient_address_name);
		unset($this->recipient_house);

	}

	#-------------------- Functions For API Requests --------------------

	public function createRequest( $url , $arr , $curl )
	{

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => True,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($arr),
			CURLOPT_HTTPHEADER => array("content-type: application/json",),
		));

		return $this && $curl;

	}

}