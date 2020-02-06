<?php

$dom_ready_js='(function() {
			var placesAutocomplete = places({  
				appId: \''.$algolia_api_id.'\',
				apiKey: \''.$algolia_api_key.'\',
				container: document.querySelector(\'#'.$elementid_n.'_autocomplete\'),
				templates: {
				  value: function(suggestion) {
					return suggestion.name;
				  }
				}
				}).configure({
				type: \'address\'
				});
				placesAutocomplete.on(\'change\', function resultSelected(e) {
				document.querySelector(\'#'.$elementid_n.'_addr1\').value = e.suggestion.value || \'\';
				document.querySelector(\'#'.$elementid_n.'_city\').value = e.suggestion.city || \'\';
				document.querySelector(\'#'.$elementid_n.'_province\').value = e.suggestion.administrative || \'\';
				document.querySelector(\'#'.$elementid_n.'_zip\').value = e.suggestion.postcode || \'\';
				document.querySelector(\'#'.$elementid_n.'_country\').value = e.suggestion.country || \'\';
				document.querySelector(\'#'.$elementid_n.'_lat\').value = e.suggestion.latlng[\'lat\'] || \'\';
				document.querySelector(\'#'.$elementid_n.'_lon\').value = e.suggestion.latlng[\'lng\'] || \'\';
				});
		/* Algolia map container: */
		  var map = L.map(\'map-example-container_'.$elementid_n.'\', {
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
		
		  
		  map.setView(new L.LatLng(45, 0), 2);
		  map.addLayer(osmLayer);
		
		  placesAutocomplete.on(\'suggestions\', handleOnSuggestions);
		  placesAutocomplete.on(\'cursorchanged\', handleOnCursorchanged);
		  placesAutocomplete.on(\'change\', handleOnChange);
		  placesAutocomplete.on(\'clear\', handleOnClear);
		
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
			
		})();';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/* 
		$field_html = '
	<div class="fcfield_field_data_box fcfield_addressint_data">
	<div><div id="'.$elementid_n.'_messages" class="alert alert-warning fc-iblock addrint_messages" style="display:none;"></div></div>

	<table class="fc-form-tbl fcfullwidth fcinner fc-addressint-field-tbl"><tbody>
		<tr>
			<td colspan="2" class="fc-nopad-h-cell">
					'.$message_error.'  
					<label class="' . $add_on_class . ' fc-lbl-short addrint_autocomplete-lbl" for="'.$elementid_n.'_autocomplete" style="float: none;"><span class="icon-search"></span></label>
					<input id="'.$elementid_n.'_autocomplete" class="addrint_autocomplete" name="'.$fieldname_n.'[autocomplete]" type="text" autocomplete="off" />
					</select>
				</div>
			</td>
		<tr>
	' .


	($addr_edit_mode != 'plaintext' ? '' : '
		<tr>
			<td class="key"><label class="fc-prop-lbl addrint_addr_display-lbl" for="'.$elementid_n.'_addr_display">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_ADDRESS').'</label></td>
			<td><textarea class="fcfield_textval addrint_addr_display ' . (in_array('address', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_addr_display" name="'.$fieldname_n.'[addr_display]" rows="4" cols="24">'
			.($value['addr_display'] ? $value['addr_display'] :
				(
					( // Minimum fields present for creating an address
					!empty($value['addr1'])    || !empty($value['city']) || !empty($value['state']) ||
					!empty($value['province']) || !empty($value['zip'])
					) ?
					 ($value['name'] ? $value['name']."\n" : '')
					.($value['addr1'] ? $value['addr1'] . "\n" : '')
					.($value['addr2'] ? $value['addr2'] . "\n" : '')
					.($value['addr3'] ? $value['addr3'] . "\n" : '')
					.($value['city'] || $value['state'] ? ($value['city']  ? ' ' . $value['city']  : '') . ($value['state'] ? ' ' . $value['state'] : '') : '')
					.($value['province'] ? ' '  . $value['province'] : '')
					.($value['zip']      ? ', ' . $value['zip'] . ($value['zip_suffix'] ? ' '.$value['zip_suffix'] : '') . "\n" : '')
					.($value['country']  ? JText::_('PLG_FC_ADDRESSINT_CC_'.$value['country']) . "\n" : '')
				: ''
				)
			)
			.'</textarea>
			</td>
		</tr>
	') .


	($addr_edit_mode != 'formatted' ? '' : '
		<tr '.($use_name ? '' : 'style="display:none;"').' class="fc_gm_name_row">
			<td class="key"><label class="fc-prop-lbl addrint_name-lbl" for="'.$elementid_n.'_name" >'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_BUSINESS_LOCATION').'</label></td>
			<td><input type="text" class="fcfield_textval addrint_name ' . ($use_name && in_array('business_location', $required_props) ? ' required' : '') . $disabled_class . '" disabled="disable" id="'.$elementid_n.'_name" name="'.$fieldname_n.'[name]" value="'.htmlspecialchars($value['name'], ENT_COMPAT, 'UTF-8').'" size="50" maxlength="100" /></td>
		</tr>
		<tr class="fc_gm_addr_row">
			<td class="key"><label class="fc-prop-lbl addrint_addr1-lbl" for="'.$elementid_n.'_addr1">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_STREET_ADDRESS').'</label></td>
			<td>
				<textarea class="fcfield_textval addrint_addr1 ' . (in_array('street_address', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_addr1" name="'.$fieldname_n.'[addr1]" maxlength="400" cols="47" rows="2">'.$value['addr1'].'</textarea>'
				.($use_addr2 ? '<br/><textarea class="fcfield_textval addrint_addr2" id="'.$elementid_n.'_addr2" name="'.$fieldname_n.'[addr2]" maxlength="400" rows="2">'.$value['addr2'].'</textarea>' : '')
				.($use_addr3 ? '<br/><textarea class="fcfield_textval addrint_addr3" id="'.$elementid_n.'_addr3" name="'.$fieldname_n.'[addr3]" maxlength="400" rows="2">'.$value['addr3'].'</textarea>' : '')
				.'
			</td>
		</tr>
		<tr class="fc_gm_city_row">
			<td class="key"><label class="fc-prop-lbl fc_gm_city-lbl" for="'.$elementid_n.'_city">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_CITY').'</label></td>
			<td><input type="text" class="fcfield_textval fc_gm_city ' . (in_array('city', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_city" name="'.$fieldname_n.'[city]" value="'.htmlspecialchars($value['city'], ENT_COMPAT, 'UTF-8').'" size="50" maxlength="100" /></td>
		</tr>
		<tr '.($use_usstate ? '' : 'style="display:none;"').' class="fc_gm_usstate_row">
			<td class="key"><label class="fc-prop-lbl fc_gm_usstate-lbl" for="'.$elementid_n.'_state">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_US_STATE').'</label></td>
			<td>'.JHtml::_('select.genericlist', $list_states, $fieldname_n.'[state]', ' class="use_select2_lib fc_gm_usstate ' . ($use_usstate && in_array('us_state', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr, 'value', 'text', $value['state'], $elementid_n.'_state').'</td>
		</tr>
		<tr '.($use_province ? '' : 'style="display:none;"').' class="fc_gm_province_row">
			<td class="key"><label class="fc-prop-lbl fc_gm_province-lbl" for="'.$elementid_n.'_province">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_NON_US_STATE_PROVINCE').'</label></td>
			<td><input type="text" class="fcfield_textval fc_gm_province ' . ($use_province && in_array('non_us_state_province', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_province" name="'.$fieldname_n.'[province]" value="'.htmlspecialchars($value['province'], ENT_COMPAT, 'UTF-8').'" size="50" maxlength="100" /></td>
		</tr>
		<tr class="fc_gm_zip_row">
			<td class="key"><label class="fc-prop-lbl addrint_zip-lbl" for="'.$elementid_n.'_zip">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_ZIP_POSTAL_CODE').'</label></td>
			<td>
				<input type="text" class="fcfield_textval inlineval addrint_zip ' . (in_array('zip_postal_code', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_zip" name="'.$fieldname_n.'[zip]" value="'.htmlspecialchars($value['zip'], ENT_COMPAT, 'UTF-8').'" size="10" maxlength="10" />
			</td>
		</tr>

		<tr '.($use_country ? '' : 'style="display:none;"').' class="fc_gm_country_row">
			<td class="key"><label class="fc-prop-lbl fc_gm_country-lbl" for="'.$elementid_n.'_country">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_COUNTRY').'</label></td>
			<td><input type="text" id="'.$elementid_n.'_country" placeholder="Country Algolia" class="fcfield_textval fc_gm_country" size="50" maxlength="100" /></td>
		</tr>

	') .


	(!$edit_latlon ? '' : '
		<tr>
			<td class="key"><label class="fc-prop-lbl addrint_lat-lbl">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_LATITUDE').'</label></td>
			<td><input type="text" class="fcfield_textval addrint_lat ' . (in_array('latitude', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_lat" name="'.$fieldname_n.'[lat]" value="'.htmlspecialchars($value['lat'], ENT_COMPAT, 'UTF-8').'" size="50" maxlength="10" /></td>
		</tr>
		<tr>
			<td class="key"><label class="fc-prop-lbl addrint_lon-lbl">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_LONGITUDE').'</label></td>
			<td><input type="text" class="fcfield_textval addrint_lon ' . (in_array('longitude', $required_props) ? ' required' : '') . $disabled_class . '" ' . $disabled_attr . ' id="'.$elementid_n.'_lon" name="'.$fieldname_n.'[lon]" value="'.htmlspecialchars($value['lon'], ENT_COMPAT, 'UTF-8').'" size="50" maxlength="10" /></td>
		</tr>
	') .
	(!$use_custom_marker ? '' : '
		<tr class="fc_gm_custom_marker_row">
		<td class="key"><label class="fc-prop-lbl fc_gm_custom_marker-lbl" for="'.$elementid_n.'_custom_marker">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_CUSTOM_MARKER').'</label></td>
			<td>'.JHtml::_('select.genericlist', $custom_markers, $fieldname_n.'[custom_marker]',' class="use_select2_lib fc_gm_custom_marker" ', 'value', 'text', ($value['custom_marker'] ? $value['custom_marker'] : $custom_marker_default), $elementid_n.'_custom_marker').'
			</td>
		</tr>
	') .

	'
	</tbody></table>

	</div>

	<div id="'.$elementid_n.'_addressint_map" class="fcfield_field_preview_box fcfield_addressint_map" style="display: contents;">
		<div>
			<div class="'.$input_grp_class.' fc-xpended">
				<label class="' . $add_on_class . ' fc-lbl-short addrint_marker_tolerance-lbl">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_MARKER_TOLERANCE').'</label>
				<input type="text" class="fcfield_textval inlineval addrint_marker_tolerance" id="'.$elementid_n.'_marker_tolerance" name="'.$fieldname_n.'[marker_tolerance]" value="50" size="7" maxlength="7" />
			</div>
			&nbsp;
			<div class="'.$input_grp_class.' fc-xpended">
				<label class="' . $add_on_class . ' fc-lbl-short addrint_zoom-lbl">'.JText::_('PLG_FLEXICONTENT_FIELDS_ADDRESSINT_ZOOM_LEVEL').'</label>
				<span id="'.$elementid_n.'_zoom_label" class="' . $add_on_class . ' addrint_zoom_label">'.$value['zoom'].'</span>
			</div>
		</div>

	</div>




	<input type="hidden" id="'.$elementid_n.'_addr_formatted" name="'.$fieldname_n.'[addr_formatted]" value="'.htmlspecialchars($value['addr_formatted'], ENT_COMPAT, 'UTF-8').'" />
	<input type="hidden" id="'.$elementid_n.'_url" name="'.$fieldname_n.'[url]" value="'.htmlspecialchars($value['url'], ENT_COMPAT, 'UTF-8').'" />

	'; */