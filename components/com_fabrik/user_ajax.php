<?php

// Note to self:
// The class is for Ajax calls only, not general-purpose scripts.
// Those are at the bottom of the file outside the class.

/* MOS Intruder Alerts */
defined('_JEXEC' ) or die('Restricted access');

class userAjax {
	function userExists() {
		$db = FabrikWorker::getDbo();
		$retStr = '';
		$myUsername = JRequest::getVar('username', '');
		$query = "SELECT name from #__users WHERE username = '$myUsername' LIMIT 1";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if ($thisName = $results[0]->name) {
			$retStr = "The username $myUsername is already in use by $thisName";
		}
		echo $retStr;
	}

	function fetchNameFromUser() {
			require_once 'FirePHPCore/FirePHP.class.php';
			$firephp = FirePHP::getInstance(true);

			ob_start();

		$retStr = '';
			$firephp->log($retStr);

		$db = FabrikWorker::getDbo(true);  // Connect to default database
		$tablename = 'cc1_critters_profile';
		$keyfield  = 'user_id';
		$keyvalue  = JRequest::getVar('user_id');

		$firephp->log($tablename);
		$firephp->log($keyfield);
		$firephp->log($keyvalue);

		$query = "SELECT * from $tablename WHERE $keyfield = $keyvalue LIMIT 1";
			$firephp->log($query);
		$db->setQuery($query);
		$results = $db->loadObjectList();
			$firephp->log($db);
			$firephp->log($results);
		$retStr = $results[0]->user_first_name;
		echo $retStr;
	}

	// base_package is the numeric ID field that indexes the critters_base_price table.
	// duration is a number from 1-8 in 0.5 increments that tells how long the customer wants the party
	// numponies is how many ponies are to be added to the petting zoo, from 1-5
	function calcPZBasePrice () {

		// Left in as a reminder:  Here's the syntax to stick a message in the PHP error log,
		// which resides in public_html/my_error_log
		//error_log("Goddamn!  It's an error!", 0);

		$db =& JFactory::getDBO();

		$base_package = JRequest::getVar('base_package');
		$duration     = JRequest::getVar('duration');
		$numponies    = JRequest::getVar('numponies');
		$pictures     = JRequest::getVar('pictures');
		$numpics      = JRequest::getVar('numpics');
		$concrete     = JRequest::getVar('concrete');
		$travel       = JRequest::getVar('travel');
		$cityfee      = JRequest::getVar('cityfee');

		//Look up base petting zoo price
		$tablename = 'cc1_critters_base_price';
		$query = "SELECT * FROM $tablename WHERE `id` = $base_package";
		$db->setQuery($query);
		$retArr = $db->LoadRow();

		$id = $retArr[0];
		$base_price = $retArr[4];
		$min_time = $retArr[6];
		$addl_time_price = $retArr[7];
		$max_time = $retArr[8];

		//Look up pony base price
		$tablename = 'cc1_critters_addons';
		$query = "SELECT * FROM $tablename WHERE `addon_name` = 'pony'";
		$db->setQuery($query);
		$retArr = $db->LoadRow();
		$addon_pony_price = $retArr[4];

		//Look up pony per hour
		$tablename = 'cc1_critters_addons';
		$query = "SELECT * FROM $tablename WHERE `addon_name` = 'ponyhour'";
		$db->setQuery($query);
		$retArr = $db->LoadRow();
		$addon_pony_hour = $retArr[4];

		//Look up price for photo souvenirs
		$tablename = 'cc1_critters_addons';
		$query = "SELECT * FROM $tablename WHERE `addon_name` = 'photo'";
		$db->setQuery($query);
		$retArr = $db->LoadRow();
		$addon_picture = $retArr[4];


		// Bug alert - concrete is added if selected, but we need to make sure
		// we don't add it if the user has picked a party with no petting zoo,
		// which hides the concrete checkbox so he can't uncheck it.
		if ($duration == 1) {
			// Special 1-hour price is just base price minus $50.
			$package_price =
				$base_price  - 50 +
				$numponies * $addon_pony_price  +
				$duration*$addon_pony_hour*$numponies +
				(($pictures=='true')?($numpics*$addon_picture):0) +
				(($concrete=='true')?50:0) +
				(($travel > 40) ? ($travel*1.5) : 0) +
				$cityfee;
		} else {
			$package_price =
				$base_price  + 
				($duration - 1.5) * 2 * $addl_time_price  +
				$numponies * $addon_pony_price  +
				($duration-1.5)*$addon_pony_hour*$numponies +
				(($pictures=='true')?($numpics*$addon_picture):0) +
				(($concrete=='true')?50:0) +
				(($travel > 40) ? ($travel*1.5) : 0) +
				$cityfee;
		}
		error_log("--------------");
		error_log("Computing price.", 0);
		error_log("base = ".$base_price);
		error_log("duration = ".$duration);
		error_log("half hour price = ".$addl_time_price);
		error_log("num ponies = ".$numponies);
		error_log("travel fee = ".$travel);
		error_log("city fee = ".$cityfee);
		error_log("concrete fee = ".$concrete);
		error_log("picture = ".$pictures);
		error_log("num pictures = ".$numpics);
		error_log("price per picture = ".$addon_picture);
		error_log("total price = ".$package_price);
		error_log("--------------");

    echo $package_price;
	}

	// base_package is the numeric ID field that indexes the critters_base_price table.
	// We will return an array of tuplets giving all the valid party durations.
	// For Little Rancher, for example, return value r should be:
	// r[0]->duration_num = 1.5
	// r[0]->duration_str = '1:30'
	// r[1]->duration_num = 2
	// r[1]->duration_str = '2:00'
	// r[2]->duration_num = 2.5
	// r[2]->duration_str = '2:30'
	// etc...
	function getValidDurations () {

		// Left in as a reminder:  Here's the syntax to stick a message in the PHP error log,
		// which resides in public_html/my_error_log
		error_log("Reporting from inside getValidDurations", 0);

		$db =& JFactory::getDBO();

		$base_package = JRequest::getVar('base_package');
		$coupon_code  = JRequest::getVar('coupon_code');

		error_log("Base package = $base_package");
		error_log("Coupon code  = $coupon_code");

		if ($coupon_code) {
			$min_time_permitted = 1;
		} else {
			$query_min = "SELECT `min_time` FROM `cc1_critters_base_price` WHERE `id` = $base_package";
			$db->setQuery($query_min);
			$retArr = $db->loadRow();
			$min_time_permitted = $retArr[0];
			// Can user print_r to print variables of pretty much any type to the log...
			//error_log($retArr);
		}

		$query_max = "SELECT `max_time` FROM `cc1_critters_base_price` WHERE `id` = $base_package";
		$db->setQuery($query_max);
		$retArr = $db->loadRow();
		$max_time_permitted = $retArr[0];

		$query = "SELECT duration_num, duration_str from `cc1_critters_party_durations` WHERE
				`duration_num` >= $min_time_permitted AND `duration_num` <= $max_time_permitted";

		$db->setQuery($query);
		$retArr = $db->loadRowList();

		//error_log("Returning " . json_encode($retArr));

		echo json_encode($retArr);
	}
}

// Pass this function a column name from the cc1_critters_profile
// table, and it should return the data from that column of the
// current user.  This function should only get called for a
// registered user, not a guest.
function fetchUserProfileValue( $fieldname ) {

		error_log('Fetching Profile Field '.$fieldname);
		$userid = JFactory::getUser()->id;
		error_log('User ID '.$userid);

		$db =& JFactory::getDBO();

		$tablename = 'cc1_profiles';
		$query = "SELECT `$fieldname` FROM $tablename WHERE `user_id` = $userid;";
		error_log('Query: '.$query);
		$db->setQuery($query);
		$retStr = $db->loadResult();

		error_log('Returning with '.$retStr);
		return $retStr;

}

function fetchJUserProfileValue( $fieldname ) {

		$userid = JFactory::getUser()->id;
		$db =& JFactory::getDBO();

		$tablename = 'cc1_users';
		$query = "SELECT `$fieldname` FROM $tablename WHERE `id` = $userid;";
		$db->setQuery($query);

		if (!isset($db)) {
			error_log('db is not set.');
		} 

		$retStr = $db->loadResult();
		if (!isset($retStr)) {
			error_log('retStr is not set.');
		} 
		return $retStr;
}

function payment_method_is($method) {
	$x=jRequest::getVar('cc1_critters_reservations___reservations_deposit_method');
	error_log("in PAYMENT_METHOD_IS: returning $x");
	return ($x==$method);
}

?>
