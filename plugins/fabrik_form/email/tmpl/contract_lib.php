<?php
// BEGIN cut
// Open database and common variable definitions
	$userid = $this->data['joom_user_id'];
	$firname = $this->data['firstname'];
	$surname = $this->data['lastname'];

	$address1 = $this->data['address1'];
	$address2 = $this->data['address2'];
	$city = $this->data['city'];
	$state = $this->data['state'];
	$zipcode = $this->data['zipcode'];
	$primary_phone = $this->data['phone1'];
	$alternate_phone = $this->data['phone2'];

	$party_address1 = $this->data['reservations_party_address_line1'];
	$party_address2 = $this->data['reservations_party_address_line2'];
	$party_city = $this->data['reservations_party_address_city'];
	$party_state = $this->data['reservations_party_address_state'];
	$party_zipcode = $this->data['reservations_party_address_zip'];

	$base_package     = $this->data['package_basetype'][0];
	$duration         = $this->data['package_duration'][0];
	$numponies        = $this->data['package_numponies'][0];

	$concrete_data    = $this->data['reservations_concrete_checked_raw'];
	$concrete         =
		is_array($concrete_data) ? $concrete_data[0] : 0;
		
	$miles            = $this->data['distance_one_way'];
	$cityfee          = $this->data['reservations_cityfees'];

	// event_date comes from a date-picker
	$event_date = substr($this->data['reservations_partydate'], 0, 10);
	// start time is a string
	$start_time = $this->data['reservations_partytime'];

	$start_time_str = date("g:i A", strtotime($start_time));
	$end_time_eval_str = $start_time . " + $duration hours";

	// Add hours * min/hour * sec/min
	$start_time_num = strtotime($start_time);
	$start_time_str = date("g:i A", $start_time_num);
	$end_time_num = $start_time_num + 60 * 60 * $duration;
	$end_time_str = date("g:i A", $end_time_num);

	$timespan = "<b>$start_time_str - $end_time_str</b>";

	//Look up base petting zoo price
	$query = "SELECT * FROM `cc1_critters_base_price` WHERE `id` = $base_package";
	$db->setQuery($query);
	$retArr = $db->LoadRow();

	// Assign fields from fetched database row
	$id = $retArr[0];
	$base_package_label = $retArr[3];
	$base_price = $retArr[4];
	$min_time = $retArr[6];
	$addl_time_price = $retArr[7];
	$max_time = $retArr[8];

	//Look up pony base price
	$query = "SELECT * FROM `cc1_critters_addons` WHERE `addon_name` = 'pony'";
	$db->setQuery($query);
	$retArr = $db->LoadRow();
	$addon_pony_price = $retArr[4];

	//Look up pony per hour
	$query = "SELECT * FROM `cc1_critters_addons` WHERE `addon_name` = 'ponyhour'";
	$db->setQuery($query);
	$retArr = $db->LoadRow();
	$addon_pony_hour = $retArr[4];

	$ishomeaddress = $this->data['ishomeaddress'][0];
	//1 => is home; 2 => is elsewhere
	if ($ishomeaddress == 1){
		$billing_address_block = "<b>Billing address same as party location.</b><br>";

		$party_address_block = "<span style='color: #ff0000;'>";
		$party_address_block .= "<b>$address1<br>";
		$party_address_block .= ($address2=='')?"":"$address2<br>";
		$party_address_block .= "$city, $state  $zipcode<br></b>";
		$party_address_block .= "</span>";
	} else if ($ishomeaddress == 2) {
		$billing_address_block = "Billing address:<br>";
		$billing_address_block = "<b>$address1<br>";
		$billing_address_block .= ($address2=='')?"":"$address2<br>";
		$billing_address_block .= "$city, $state  $zipcode<br></b>";

		$party_address_block = "<span style='color: #ff0000;'>";
		$party_address_block .= "<b>$party_address1<br>";
		$party_address_block .= ($party_address2=='')?"":"$party_address2<br>";
		$party_address_block .= "$party_city, $party_state  $party_zipcode<br></b>";
		$party_address_block .= "</span>";
	}
	
	$miles = $this->data['distance_one_way'];
	if ($miles > 40)
	{
		$travel_fee = $miles*1.5;
		$travel_fee_line = "Travel fee (\$1.50/mile, outside a 40-mile radius): \$$travel_fee.<br>";
	} else {
		$travel_fee = 0;
		$travel_fee_line = "";
	}

	$city_fee = $this->data['reservations_cityfees'];
	if ($city_fee == 0) {
		$city_fee_line = "";
	} else {
		$city_fee_line = "Additional safety precautions and/or permits required for your city: \$$city_fee.<br>";
	}

	if ($concrete) {
		$concrete_fee = 50;
		$concrete_setup_line = "Optional setup and cleanup on concrete: <b>\$$concrete_fee</b><br>";
	} else {
		$concrete_fee = 0;
		$concrete_setup_line = "";
	}

	// Text describing the type of party and duration
	$party_package = $base_package_label . (($numponies==0)?"":" plus $numponies ponies") . " for $duration hours";

	$referral_source = $this->data['reservations_referral_source'];
	$comments = $this->data['reservations_comments'];

	// Price of base package plus pony rides, not including other line items
	$base_event_price =
		$base_price + 
		($duration - 1.5) *
		2 *
		$addl_time_price  +
		$numponies * $addon_pony_price  +
		($duration-1.5)*$addon_pony_hour*$numponies;

	// Enable for debugging only.
	//error_log("Dump of database variables.");
	//error_log("Package #:".$base_package);
	//error_log("start = $start_time");
	//error_log("duration = $duration");
	//error_log("$numponies ponies");
	//error_log("Pictures y/n: $pictures");
	//error_log("Qty: $numpics pictures");
	//error_log("Concrete=1, grass=0: $concrete");
	//error_log("Miles: $miles");
	//error_log("Plano fee: $cityfee"); 
	//error_log("base: $base_event_price");
	//error_log("photos: $photos_price");
	//error_log("concrete: $concrete_fee");
	//error_log("travel: $travel_fee");
	//error_log("city: $city_fee");

	//Look up email address from Joomla user database
	$user =& JFactory::getUser();
	$email = $user->email;

	$formatted_party_date = "<b>" . date("l, F d, Y", strtotime($event_date)) . "</b>";

// END cut
?>
