
			var placesAutocomplete = places({  
				appId: '.$algolia_api_id.',
				apiKey: '.$algolia_api_key.',
				container: document.getElementById(elementid_n + '_autocomplete'),
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

		