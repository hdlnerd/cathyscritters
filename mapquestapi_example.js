var SAMPLE_POST = HOST_URL + '/directions/v1/route?outFormat=json&from=40.037661,-76.305977&to=39.962532,-76.728099&callback=renderNarrative';


function showBasicURL() {
    var safe = SAMPLE_POST;
    document.getElementById('basicSampleUrl').innerHTML = safe.replace(/</g, '&lt;').replace(/>/g, '&gt;');
};

function doClick() {
	document.getElementById('narrative').innerHTML = 'Pending...';
    var script = document.createElement('script');
    script.type = 'text/javascript';
    showBasicURL();
    var newURL = SAMPLE_POST.replace('YOUR_KEY_HERE', APP_KEY);
    script.src = newURL;
    document.body.appendChild(script);
};

function renderNarrative(response) {
    var legs = response.route.legs;
    var html = '';
    var i = 0;
    var j = 0;
    var trek;
    var maneuver;
    
    if (response.route.distance) {
        html += "<p>Your trip is <b> " + response.route.distance.toFixed(2) + "</b> miles.</p>";
    }
    html += '<table><tr><th colspan=2>Narrative</th>'
    html += '<th colspan=1>Distance</th></tr><tbody>'

    var unit = response.route.options.unit;
    if (unit) {
        if (unit == 'K') {
            unit = 'km';
        } else if (unit == 'M') {
            unit = 'miles';
        }
    }
    for (; i < legs.length; i++) {
        for (j = 0; j < legs[i].maneuvers.length; j++) {
        	  var last = legs[i].maneuvers.length - 1;
            maneuver = legs[i].maneuvers[j];
            
            html += '<tr>';
            html += '<td>&nbsp;';
            if (maneuver.iconUrl) {
                html += '<img src="' + maneuver.iconUrl + '">  '; 
            } 
            for (k = 0; k < maneuver.signs.length; k++) {
                var sign = maneuver.signs[k];
                if (sign && sign.url) {
                    html += '<img src="' + sign.url + '">  '; 
                }
            }
            
            html += '</td>'
            //added following because we're only using lat/lngs currently
            if (j == last){
            	html += '<td>' + maneuver.narrative.replace(maneuver.narrative, "Welcome to your destination.")
            }
            else {
              html += '<td>' + maneuver.narrative 
            }
            if (unit && maneuver.distance) {
              maneuver.distance = 
                html += '<td>  (' + maneuver.distance.toFixed(2) + ' ' + unit + ')'
                html += '</td>';
            }    
            else {
              html += '<td>  &nbsp; '
              html += '</td>';
            }
            html += '</tr>';
         }
    }
    html += '</tbody></table>';
    document.getElementById('narrative').style.display = "";
    document.getElementById('narrative').innerHTML = html;
