// Algolia fields
	if($mapapi_edit == 'algolia')
	{
		$field_html = '
		Algolia // TODO MIX value value with googlemap form + add long and lat
	<div id="algolia-form">
		<div class="form-group">
		<label for="form-address">Address*</label>
		<input type="search" class="form-address" id="form-address" placeholder="Where do you live?" />
		</div>
		<div class="form-group">
		<label for="form-address2">Address 2</label>
		<input type="text" class="form-control" id="form-address2" placeholder="Street number and name" />
		</div>
		<div class="form-group">
		<label for="form-city">City*</label>
		<input type="text" class="form-control" id="form-city" placeholder="City">
		</div>
		<div class="form-group">
		<label for="form-zip">ZIP code*</label>
		<input type="text" class="form-control" id="form-zip" placeholder="ZIP code">
		</div>
		<div class="form-group">
		<label for="form-lon">Longitude*</label>
		<input type="text" class="form-control" id="form-lon" placeholder="Long">
		</div>
		<div class="form-group">
		<label for="form-lat">Latitude*</label>
		<input type="text" class="form-control" id="form-lat" placeholder="Latitude">
		</div>
		<div class="form-group">
		<label for="form-marker">Custom icon</label>
		<select class="form-control" id="form-marker" placeholder="Marker">
			
		</select>
		</div>
		Fin algolia
	</div>
		
			TODO FIX POSITION map
			<div id="carte" class="map-preview" >
		<center><div id="map-example-container" class="addrint_algolia_canvas" '.($map_width || $map_height  ?  'style=";width:'.$map_width.'px; height:'.$map_height.'px;"' : '').'></div></center>
	</div>
		';

    }
    
// Algolia Autocomplete /////////////////////////////////////////////////////////
if ( $mapapi_edit == 'algolia'){ 
  $dom_ready_js='(function() {
			var placesAutocomplete = places({  
				appId: \''.$algolia_api_id.'\',
				apiKey: \''.$algolia_api_key.'\',
				container: document.querySelector(\'#form-address\'),
				templates: {
				  value: function(suggestion) {
					return suggestion.name;
				  }
				}
				}).configure({
				type: \'address\'
				});
				placesAutocomplete.on(\'change\', function resultSelected(e) {
				document.querySelector(\'#form-address2\').value = e.suggestion.administrative || \'\';
				document.querySelector(\'#form-city\').value = e.suggestion.city || \'\';
				document.querySelector(\'#form-zip\').value = e.suggestion.postcode || \'\';
				document.querySelector(\'#form-lat\').value = e.suggestion.latlng[\'lat\'] || \'\';
				document.querySelector(\'#form-lon\').value = e.suggestion.latlng[\'lng\'] || \'\';
				});
		
		  var map = L.map(\'map-example-container\', {
			scrollWheelZoom: true,
			zoomControl: true
		  });
		
		  var osmLayer = new L.TileLayer(
			\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {
			  minZoom: 1,
			  maxZoom: 19,
			  attribution: \'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors\'
			}
		  );
		
		  var markers = [];
		
		  map.setView(new L.LatLng(0, 0), 1);
		  map.addLayer(osmLayer);
		
		  placesAutocomplete.on(\'suggestions\', handleOnSuggestions);
		  placesAutocomplete.on(\'cursorchanged\', handleOnCursorchanged);
		  placesAutocomplete.on(\'change\', handleOnChange);
		  placesAutocomplete.on(\'clear\', handleOnClear);
		
		  function handleOnSuggestions(e) {
			markers.forEach(removeMarker);
			markers = [];
		
			if (e.suggestions.length === 0) {
			  map.setView(new L.LatLng(0, 0), 1);
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
			map.setView(new L.LatLng(0, 0), 1);
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
			
		})();';
}