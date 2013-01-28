function getSelectedRadio(buttonGroup) {
	//console.log("Entering getSelectedRadio ");
	//console.log(buttonGroup);
	//console.log(buttonGroup.elements[0]);
	//console.log(buttonGroup.elements[1]);
   // returns the array number of the selected radio button or -1 if no button is selected
   if (buttonGroup.elements[0]) { // if the button group is an array (one button is not an array)
			//console.log("Found button array");
      for (var i=0; i<buttonGroup.elements.length; i++) {
					//console.log("Checking button at i=",i);
         if (buttonGroup.elements[i].checked) {
						//console.log("Found a set button at i=",i);
            return i
         }
      }
   } else {
			//console.log("Found single button");
      if (buttonGroup.checked) {
				//console.log("It's checked");
				return 0;
			} else {
				//console.log("It doesn't seem to be checked");
			} // if the one button is checked, return zero
   }
   // if we get to this point, no radio button is selected
   return -1;
} // Ends the "getSelectedRadio" function

function calcPlanoCharges (check_zip) {
	var plano_zips =["75023", "75024", "75025", "75026", "75074",
	                 "75075", "75086", "75093", "75094"]
	console.log("Checking ZIP code");
	console.log(plano_zips.length);
	console.log("Yes ="+plano_zips.contains("75023"));
	console.log("No ="+plano_zips.contains("75407"));

  if (plano_zips.contains(check_zip)) {
		console.log("Found a Plano ZIP" + check_zip);
		cityfee = 100;
		$('cc1_critters_reservations___reservations_isplano').getParent('fieldset').slide('in');
		$('cc1_critters_reservations___reservations_cityfees').value = 100;
	} else {
		console.log("Found a non-Plano ZIP" + check_zip);
		cityfee = 0;
		$('cc1_critters_reservations___reservations_isplano').getParent('fieldset').slide('out');
		$('cc1_critters_reservations___reservations_cityfees').value = 0;
	}
	return cityfee
}

// This function updates the running total party price and does
// javascript animations as the user checks and unchecks options.
function updatePrice () {
	//
	//When adding variables to the pricing formula, add a variable, gleaned from the form here...
	//
	var key       = 'Fmjtd%7Cluua2lu72h%2C25%3Do5-hyrs0';
	var base_package = $('cc1_critters_reservations___package_basetype').getValue();
	var numponies    = $('cc1_critters_reservations___package_numponies').getValue();
	var duration     = $('cc1_critters_reservations___package_duration').getValue();
	var pictures     = $('cc1_critters_reservations___reservations_photo').getElements('input')[0].checked;
	var numpics      = $('cc1_critters_reservations___reservations_photo_quantity').getValue();
	var concrete     = $('cc1_critters_reservations___reservations_concrete_checked').getElements('input')[0].checked;
	var travel       = 0;
	//alert(String($('cc1_critters_reservations___ishomeaddress').getElements('input')[1].checked));
  var party_elsewhere = $('cc1_critters_reservations___ishomeaddress').getElements('input')[1].checked;

	if (pictures) {
		$('cc1_critters_reservations___reservations_photo_quantity').show();
	} else {
		$('cc1_critters_reservations___reservations_photo_quantity').hide();
	}

  if (party_elsewhere) {
		check_addr1 = $('cc1_critters_reservations___reservations_party_address_line1').value;
		check_city  = $('cc1_critters_reservations___reservations_party_address_city').value;
		check_state = $('cc1_critters_reservations___reservations_party_address_state').value;
		check_zip   = $('cc1_critters_reservations___reservations_party_address_zip').value;
  } else {
		check_addr1 = $('cc1_critters_reservations___address1').value;
		check_city  = $('cc1_critters_reservations___city').value;
		check_state = $('cc1_critters_reservations___state').value;
		check_zip   = $('cc1_critters_reservations___zipcode').value;
  }
	var cityfee      = calcPlanoCharges(check_zip);
	var from_addr = encodeURIComponent('7422 County Road 466, Princeton TX 75407');

	var to_addr = encodeURIComponent(check_addr1 + ',' + check_city + ',' + check_state + ',' + check_zip);

	var one_way_mileage = 0;
	var toll_charge_estimate = 0;

	//Working copy-and-paste example:
	//http://platform.beta.mapquest.com/directions/v1/route?key=Fmjtd%7Cluua2lu72h%2C25%3Do5-hyrs0&from=75407&to=75248&ambiguities=ignore

	// Switched from beta to production version of Mapquest API
	//var url_mq='http://platform.beta.mapquest.com/directions/v1/route?key='+key;
	var url_mq='http://www.mapquestapi.com/directions/v1/route?key='+key;
	data_mq = 'from='+from_addr+'&to='+to_addr+'&ambiguities=ignore';
	console.log("Data to mapquest: "+data_mq);
	//alert("Data to mapquest: "+data_mq);

	// This is super-ugly.  I haven't figured out the "correct" way to do chained requests, so
	// I embedded one inside the other.  The outer request is the one to query Mapquest for the
	// driving distance.  Inside the "onSuccess" function, I pass the distance along with all
	// the other variables to calcPZBasePrice.  This guarantees that the Ajax routines get
	// called in the right order.  My problem was that because of the latency, the calcPrice
	// routine kept running *before* the Mapquest routine, meaning it didn't have all the data
	// it needed.
	// I'm sure there's a smarter way, but this will work for now :-)
	newreq = new Request.JSONP(
		{url: url_mq, method: 'post',
			onSuccess: function(r){
				travel = Math.round(r.route.distance);
				toll_charge_estimate = r.route.hasTollRoad?5:0;
				$('cc1_critters_reservations___distance_one_way').value = travel;
				$('cc1_critters_reservations___toll-roads').value = toll_charge_estimate;

				var url_pz='index.php?option=com_fabrik&format=raw&task=plugin.userAjax&method=calcPZBasePrice';
	
				data_pz='base_package='+base_package+'&duration='+duration+'&numponies='+numponies+'&pictures='+pictures+'&numpics='+numpics+'&concrete='+concrete+'&travel='+travel+'&cityfee='+cityfee;
	
				console.log("Data to calcPZBasePrice= "+url_pz+data_pz);
				new Request(
					{url: url_pz, method: 'post', 
						onComplete:
						function(r){
							$('cc1_critters_reservations___reservations_base_price').value = r;
							$('cc1_critters_reservations___reservations_deposit_due').value = r/2;
						}
					}
				).send(data_pz);
			},
			onComplete: function(r){ },
			onFailure: function(r){
				alert("Mapquest request failed. Bad URL?"+url_mq);
			}
		}
	);

	newreq.send(data_mq);
}
