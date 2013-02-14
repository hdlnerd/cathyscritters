<!-- Use $this->data to supply all the placeholders we need
     to fill out the contract.
		Remember to put extra_attendant in for public events
 -->

<?php
	$db =& JFactory::getDBO();
	require_once("contract_lib.php");

	$pictures_data    = $this->data['reservations_photo_raw'];
	$pictures =
		is_array($pictures_data) ? $pictures_data[0] : 0;
	$numpics          =
	$pictures ? $this->data['reservations_photo_quantity'] : 0;

	$isbirthday       = $this->data['isbirthday'];
	$childname        = $this->data['childname'];
	$childgender      = $this->data['childgender'];
	$childage         = $this->data['childage'];

	$birthday_block  = "";
	if ($isbirthday) {
		$birthday_block  = "Birthday info: <br>";
		$birthday_block .= "Child's name:  <b>$childname</b><br>";
		$birthday_block .= "Gender, age: <b>$childgender, $childage years old</b><br>";
	}

	//Look up price for photo souvenirs
	$query = "SELECT * FROM `cc1_critters_addons` WHERE `addon_name` = 'photo'";
	$db->setQuery($query);
	$retArr = $db->LoadRow();
	$addon_picture = $retArr[4];


	$photo_raw = $this->data['reservations_photo_raw'];
	if ($pictures) {
		$photos_price = 10 * $numpics;
		$photo_souvenir_line = "Souvenir photos chosen, <b>$numpics pictures at \$$photos_price</b><br>";
	} else {
		$photos_price = 0;
		$photo_souvenir_line = "";
	}

	$package_price =
		$base_event_price + 
		$photos_price +
		$concrete_fee +
		$travel_fee +
		$cityfee;

	$deposit_due = $package_price / 2;
	$remainder_due = $package_price - $deposit_due;

echo <<<EOD
<h3>Cathy's Critters has sent you this contract: </h3>
<TABLE WIDTH=745 BORDER=0 bgcolor="white" cellpadding="13" cellspacing="13">
	<TR>
		<TD>
			<center> $formatted_party_date </center>
			<div align=right>
			<b>
				$surname <br>
				$timespan <br>
				$party_package at \$$base_event_price <br>
				$travel_fee_line
				$concrete_setup_line
				$photo_souvenir_line
				<br>
			</b>
			</div>
			<center><h3>Cathy's Critters - Private Event Contract</h3></center>
			<font size=3>
				<b>$firname &nbsp;$surname </b></font> <br>
			<font size=2>Event date:&nbsp;$formatted_party_date &nbsp;&nbsp;&nbsp; Event time:&nbsp;$timespan<br>
			<br>
			$birthday_block<br>
			Party Locaton:<br>
			$party_address_block
			<br>
			$billing_address_block
			<br>
			Price quotation<br>
			Base Price:&nbsp;&nbsp;<b>$party_package at \$$base_event_price</b><br>
			$travel_fee_line
			$concrete_setup_line
			$photo_souvenir_line
			<br>
			Total price: <b>\$$package_price</b><br>
			Deposit Due: <b>\$$deposit_due</b><br>
			Remainder due at event: <b>\$$remainder_due</b><br>
			<br>
			Office Use Only<br>
			Attendants<br>
			<br>
			Special Codes<br>
			<br>
			Deposit Received<br>
			<br>
			Balance Due<br>
			<br>
			In most cases, payment of 50% deposit must be received to reserve the time and date you have chosen. This payment should not be made until verbal or email contact has been made with Cathy to verify that the time slot is available. This payment can be made by cash, personal check, or Paypal. <br>
			<br>
			<b>$firname&nbsp;&nbsp;$surname&nbsp;&nbsp;</b><br>
			Primary Phone: <b>$primary_phone</b> Alternate phone: <b>$alternate_phone</b> <br>
			Email: <b>$email</b><br>
			Referred from: <b>$referral_source</b><br>
			Concrete option: <b>$concrete_setup_line</b></font><br>
			<br>
			<br>
			Comments, feedback, special instructions:<br>
			<b>$comments</b><br>
		</TD>
	</TR>
</TABLE>
Contact us at: www.cathyscritters.com, reservations@cathys-critters.com, 972-562-0583 Thank you for your business.<br>
EOD;

?>
