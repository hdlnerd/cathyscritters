<!-- Use $this->data to supply all the placeholders we need
     to fill out the contract.
		Remember to put extra_attendant in for public events
 -->

<?php
	$db =& JFactory::getDBO();

	// Data that comes from a pulldown or check-box is an ARRAY; data
	// from a field is a simple value.

	//print_r($this->data);
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

	$pictures_data    = $this->data['reservations_photo_raw'];
	$pictures =
		is_array($pictures_data) ? $pictures_data[0] : 0;
	$numpics          =
	$pictures ? $this->data['reservations_photo_quantity'] : 0;

	$concrete_data    = $this->data['reservations_concrete_checked_raw'];
	$concrete         =
		is_array($concrete_data) ? $concrete_data[0] : 0;
		
	$miles            = $this->data['distance_one_way'];
	$cityfee          = $this->data['reservations_cityfees'];

	$isbirthday       = $this->data['isbirthday'];
	$childname        = $this->data['childname'];
	$childgender      = $this->data['childgender'];
	$childage         = $this->data['childage'];

	$birthday_block  = "";
	if ($isbirthday) {
		$birthday_block  = "<b>Birthday info:</b> <br>";
		$birthday_block .= "Child's name:  $childname<br>";
		$birthday_block .= "$childgender, $childage years old<br>";
	}

	// event_date comes from a date-picker
	$event_date = substr($this->data['reservations_partydate'], 0, 10);
	$start_time = $this->data['reservations_partytime'];
	$timespan = "$start_time for $duration hours";

	error_log("Dump of database variables.");
	error_log("Package #:".$base_package);
	error_log("start = $start_time");
	error_log("duration = $duration");
	error_log("$numponies ponies");
	error_log("Pictures y/n: $pictures");
	error_log("Qty: $numpics pictures");
	error_log("Concrete=1, grass=0: $concrete");
	error_log("Miles: $miles");
	error_log("Plano fee: $cityfee"); 

	// This section lifted directly from user_ajax.php.  Need to collapse into a single function.
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

	//Look up price for photo souvenirs
	$query = "SELECT * FROM `cc1_critters_addons` WHERE `addon_name` = 'photo'";
	$db->setQuery($query);
	$retArr = $db->LoadRow();
	$addon_picture = $retArr[4];

	$ishomeaddress = $this->data['ishomeaddress'][0];
	//1 => is home; 2 => is elsewhere
	if ($ishomeaddress == 1){
		$billing_address_block = "<b>Billing address same as party location.</b><br>";

		$party_address_block = "$address1<br>";
		$party_address_block .= ($address2=='')?"":"$address2<br>";
		$party_address_block .= "$city, $state  $zipcode<br>";
	} else if ($ishomeaddress == 2) {
		$billing_address_block = "$address1<br>";
		$billing_address_block .= ($address2=='')?"":"$address2<br>";
		$billing_address_block .= "$city, $state  $zipcode<br>";

		$party_address_block = "$party_address1<br>";
		$party_address_block .= "$party_address2<br>";
		$party_address_block .= "$party_city, $party_state  $party_zipcode<br>";
	}
	
	$miles = $this->data['distance_one_way'];
	if ($miles > 40)
	{
		$travel_fee = $miles*2;
		$travel_fee_line = "Travel fee (\$2/mile, outside a 40-mile radius): \$$travel_fee.<br>";
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
		$concrete_setup_line = "Optional setup and cleanup on concrete: \$$concrete_fee<br>";
	} else {
		$concrete_fee = 0;
		$concrete_setup_line = "";
	}


	$photo_raw = $this->data['reservations_photo_raw'];
	if ($pictures) {
		$photos_price = 10 * $numpics;
		$photo_souvenir_line = "Souvenir photos chosen, $numpics pictures at \$$photos_price<br>";
	} else {
		$photos_price = 0;
		$photo_souvenir_line = "";
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

	$package_price =
		$base_event_price + 
		$photos_price +
		$concrete_fee +
		$travel_fee +
		$cityfee;

	error_log("base: $base_event_price");
	error_log("photos: $photos_price");
	error_log("concrete: $concrete_fee");
	error_log("travel: $travel_fee");
	error_log("city: $city_fee");

	$deposit_due = $package_price / 2;
	$remainder_due = $package_price - $deposit_due;

	//Look up email address from Joomla user database
	$user =& JFactory::getUser();
	$email = $user->email;

	$formatted_party_date = date("M d, Y", strtotime($event_date));

echo <<<EOD
<h3>Cathy's Critters has sent you this contract: </h3>
<TABLE WIDTH=745 BORDER=0 bgcolor="white" cellpadding="13" cellspacing="13">
	<TR>
		<TD>
			<center> $formatted_party_date </center>
			<div align=right>
				$surname <br>
				$timespan <br>
				$party_package at $base_event_price <br>
				$travel_fee_line
				$concrete_setup_line
				$photo_souvenir_line
				<br>
			</div>
			<center><h3>Cathy's Critters - Private Event Contract</h3></center>
			<font size=3>
				<b>$firname &nbsp;$surname </font> </b> <br>
			<font size=2>Event date:&nbsp;$event_date &nbsp;&nbsp;&nbsp; Event time:&nbsp;$timespan<br>
			<br>
			$birthday_block<br>
			<b>Party Locaton:</b><br>
			$party_address_block
			<br>
			$billing_address_block
			<br>
			<b>Price quotation</b><br>
			<b>Base Price:</b>&nbsp;&nbsp;$party_package at $base_event_price<br>
			$travel_fee_line
			$concrete_setup_line
			$photo_souvenir_line
			<br>
			<b>Total price:</b> \$$package_price<br>
			<b>Deposit Due:</b> \$$deposit_due<br>
			<b>Remainder due at event:</b> \$$remainder_due<br>
			<br>
			<b>Office Use Only</b><br>
			<b>Attendants</b><br>
			<br>
			<b>Special Codes</b><br>
			<br>
			<b>Deposit Received</b><br>
			<br>
			<b>Balance Due</b><br>
			<br>
			In most cases, payment of 50% deposit must be received to reserve the time and date you have chosen. This payment should not be made until verbal or email contact has been made with Cathy to verify that the time slot is available. This payment can be made by cash, personal check, or Paypal. <br>
			<br>
			$firname&nbsp;&nbsp;$surname&nbsp;&nbsp;<br>
			<b>Primary Phone:</b> $primary_phone <b>Alternate phone:</b> $alternate_phone <br>
			<b>Email:</b> $email<br>
			<b>Referred from:</b> $referral_source<br>
			<b>Concrete option:</b> $concrete_setup_line</font><br>
			<br>
			<br>
			<b>Comments, feedback, special instructions:</b><br>
			$comments<br>
		</TD>
	</TR>
</TABLE>
Contact us at: www.cathys-critters.com, reservations@cathys-critters.com, 972-562-0583 Thank you for your business.<br>
EOD;

?>