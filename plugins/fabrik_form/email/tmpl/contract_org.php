<!-- Use $this->data to supply all the placeholders we need
     to fill out the contract.
		Remember to put extra_attendant in for public events
 -->

<?php
	$db =& JFactory::getDBO();
	require_once("contract_lib.php");

	$orgname = $this->data['organization'];
	$title   = $this->data['org_title'];


	$package_price =
		$base_event_price + 
		//$photos_price +
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
			<center>$formatted_party_date</center>
			<div align=right>
				<b>$surname, $orgname</b> <br>
				<b>$timespan </b> <br>
				<b>$party_package</b> at <b>\$$base_event_price</b> <br>
				$travel_fee_line
				$concrete_setup_line
				<br>
			</div>
			<center><h3>Cathy's Critters - Organization Event Contract</h3></center>
			<font size=3>
				<b>$orgname</b> <br>
				<b>$firname $surname, $title</b> <br>
			<font size=2>Event date:&nbsp;$formatted_party_date &nbsp;&nbsp;&nbsp; Event time:&nbsp;$timespan<br>
			$billing_address_block
			<br>
			<br>
			Party Locaton:<br>
			$party_address_block
			<br>
			<br>
			Price quotation<br>
			Base Price:&nbsp;&nbsp;<b>$party_package at \$$base_event_price</b><br>
			$travel_fee_line
			$concrete_setup_line
			Total price <b>\$$package_price</b><br>
			Deposit Due: <b>\$$deposit_due</b><br>
			Remainder due at event <b>\$$remainder_due</b><br>
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
