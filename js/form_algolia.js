// Algolia script
var fcfield_addrint = {};
	
	fcfield_addrint.autoComplete = [];
	fcfield_addrint.gmapslistener = [];
	fcfield_addrint.google_maps = [];
	
	fcfield_addrint.allowed_countries = [];
	fcfield_addrint.single_country = [];
	fcfield_addrint.map_zoom = [];
	fcfield_addrint.map_type = [];
    fcfield_addrint.LatLon = [];
    
    fcfield_addrint.initAutoComplete = function(elementid_n, config_name)
	{
		var ac_input = document.getElementById(elementid_n + '_autocomplete');
		var ac_type    = jQuery('#' + elementid_n + '_ac_type').val();
		var ac_country = fcfield_addrint.single_country[config_name];

		var ac_options = {};
		if (ac_type)    ac_options.types = [ ac_type ];
		if (ac_country) ac_options.componentRestrictions = {country: ac_country};

		fcfield_addrint.autoComplete[elementid_n] = new google.maps.places.Autocomplete( ac_input, ac_options );

		fcfield_addrint.gmapslistener[elementid_n] = google.maps.event.addListener(fcfield_addrint.autoComplete[elementid_n], 'place_changed', function()
		{
			jQuery('#' + elementid_n + '_messages').html('').hide();
			fcfield_addrint.fillInAddress(elementid_n, false, config_name);
		});
		return true;
	}

// Algolia Autocomplete 


    var placesAutocomplete = places({  
        appId: '.$algolia_api_id.',
        apiKey: '.$algolia_api_key.',
        container: document.querySelector('#' + $elementid_n + '_autocomplete'),
        templates: {
          value: function(suggestion) {
            return suggestion.name;
          }
        }
        }).configure({
        type: 'address'
        });
        placesAutocomplete.on('change', function resultSelected(e) {
        document.querySelector('#' + $elementid_n + '_addr1').value = e.suggestion.value || '';
        document.querySelector('#' + $elementid_n + '_city').value = e.suggestion.city || '';
        document.querySelector('#' + $elementid_n + '_province').value = e.suggestion.administrative || '';
        document.querySelector('#' + $elementid_n + '_zip').value = e.suggestion.postcode || '';
        document.querySelector('#' + $elementid_n + '_country').value = e.suggestion.country || '';
        document.querySelector('#' + $elementid_n + '_lat').value = e.suggestion.latlng['lat'] || '';
        document.querySelector('#' + $elementid_n + '_lon').value = e.suggestion.latlng['lng'] || '';
        });
/* Algolia map container: */
  var map = L.map('map-example-container_' + $elementid_n , {
    scrollWheelZoom: true,
    zoomControl: true
  });

  var osmLayer = new L.TileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      minZoom: 1,
      maxZoom: 19,
      attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    }
  );

  var markers = [];

  
  map.setView(new L.LatLng(45, 0), 2);
  map.addLayer(osmLayer);

  placesAutocomplete.on('suggestions', handleOnSuggestions);
  placesAutocomplete.on('cursorchanged', handleOnCursorchanged);
  placesAutocomplete.on('change', handleOnChange);
  placesAutocomplete.on('clear', handleOnClear);

  function handleOnSuggestions(e) {
    markers.forEach(removeMarker);
    markers = [];

    if (e.suggestions.length === 0) {
      map.setView(new L.LatLng(45, 0), 2);
      return;
    }

    e.suggestions.forEach(addMarker);
    findBestZoom();
  }

  function handleOnChange(e) {
    markers
      .forEach(function(marker, markerIndex) {
        if (markerIndex === e.suggestionIndex) {
          markers = [marker];
          marker.setOpacity(1);
          findBestZoom();
        } else {
          removeMarker(marker);
        }
      });
  }

  function handleOnClear() {
    map.setView(new L.LatLng(45, 0), 1);
    markers.forEach(removeMarker);
  }

  function handleOnCursorchanged(e) {
    markers
      .forEach(function(marker, markerIndex) {
        if (markerIndex === e.suggestionIndex) {
          marker.setOpacity(1);
          marker.setZIndexOffset(1000);
        } else {
          marker.setZIndexOffset(0);
          marker.setOpacity(0.5);
        }
      });
  }

  function addMarker(suggestion) {
    var marker = L.marker(suggestion.latlng, {opacity: .4});
    marker.addTo(map);
    markers.push(marker);
  }

  function removeMarker(marker) {
    map.removeLayer(marker);
  }

  function findBestZoom() {
    var featureGroup = L.featureGroup(markers);
    map.fitBounds(featureGroup.getBounds().pad(0.5), {animate: false});
  }
    
;