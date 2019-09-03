$('#device-location-building').typeahead({
    name: 'search',
    remote: {
        url: getAppVars().basePath+'api/google/place_autocomplete?input=%QUERY &language=en_BR&key=AIzaSyCimfWUVW5af1XGqYGLbtmgtzusGQ8ynW4',
        dataType: 'json',
        cache: false,
        filter: function (response) {
            //console.log(response.predictions);
            var suggest = [];

            $.each(response.predictions, function (place) {
                suggest.push({
                    value: this.description,
                    tokens: this.place_id
                            //courseCode: data[i].courseCode,
                            //courseName: data[i].courseName,
                            //template: '<p>{{courseCode}} - {{courseName}}</p>',
                });
            });

            //console.log(suggest)

            return suggest;
        }
    },
})
        .on('typeahead:selected', function (e, datum) {
			
			
			/**
			 * Module for displaying "Waiting for..." dialog using Bootstrap
			 *
			 * @author Eugene Maslovich <ehpc@em42.ru>
			 */

			var waitingDialog = waitingDialog || (function ($) {
				'use strict';

				// Creating modal dialog's DOM
				var $dialog = $(
					'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
					'<div class="modal-dialog modal-m">' +
					'<div class="modal-content">' +
						'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
						'<div class="modal-body">' +
							'<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
						'</div>' +
					'</div></div></div>');

				return {
					/**
					 * Opens our dialog
					 * @param message Custom message
					 * @param options Custom options:
					 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
					 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
					 */
					show: function (message, options) {
						// Assigning defaults
						if (typeof options === 'undefined') {
							options = {};
						}
						if (typeof message === 'undefined') {
							message = 'Waiting for Geolocation';
						}
						var settings = $.extend({
							dialogSize: 'sm',
							progressType: 'warning',
							onHide: null // This callback runs after the dialog was hidden
						}, options);

						// Configuring dialog
						$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
						$dialog.find('.progress-bar').attr('class', 'progress-bar');
						if (settings.progressType) {
							$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
						}
						$dialog.find('h3').text(message);
						// Adding callbacks
						if (typeof settings.onHide === 'function') {
							$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
								settings.onHide.call($dialog);
							});
						}
						// Opening dialog
						$dialog.modal();
					},
					/**
					 * Closes dialog
					 */
					hide: function () {
						$dialog.modal('hide');
					}
				};

			})(jQuery);
			waitingDialog.show('Waiting for Geolocation', {dialogSize: 'sm', progressType: 'primary'});
			var i;
			document.getElementById('btnlocation').disabled = true;
			$.ajax({
				  type: "POST",
				  url: getAppVars().basePath+'api/google/place_details?placeid=' + datum.tokens+'&key=AIzaSyCimfWUVW5af1XGqYGLbtmgtzusGQ8ynW4',
				  success: function (data){ 
					var building='',road='',city='',state='',country='',longitude='',latitude='', district='';
					
                                      
                    document.getElementById('device-location-building').value='';
					document.getElementById('device-location-road').value='';
					document.getElementById('device-location-district').value='';
					document.getElementById('device-location-city').value='';
					document.getElementById('device-location-state').value='';
					document.getElementById('device-location-country').value='';
					document.getElementById('device-location-longitude').value='';
					document.getElementById('device-location-latitude').value='';
					console.log("description=" ,data.result.address_components);
					for( i=0; i<(data.result.address_components.length); i++){
					
						if(data.result.address_components[i].types[0]=='route'){
							road=data.result.address_components[i].long_name;
							
						}else if(data.result.address_components[i].types[0]=='premise' || data.result.address_components[i].types[0]=='subpremise'){
							building=data.result.address_components[i].long_name;
							
						}else if(data.result.address_components[i].types[0]=='sublocality_level_1' || data.result.address_components[i].types[0]=='administrative_area_level_2'){
							district=data.result.address_components[i].long_name;
							
						}else if(data.result.address_components[i].types[0]=='locality' || data.result.address_components[i].types[0]=='sublocality'){
							city=data.result.address_components[i].long_name;
							
						}else if(data.result.address_components[i].types[0]=='administrative_area_level_1'){
							state=data.result.address_components[i].long_name;
							
						}else if(data.result.address_components[i].types[0]=='country'){
							country=data.result.address_components[i].long_name;							
						}
					}
					longitude=data.result.geometry.location.lat;
					latitude=data.result.geometry.location.lng;
					
					document.getElementById('device-location-building').value=building;
					document.getElementById('device-location-road').value=road;
					document.getElementById('device-location-district').value=district;
					document.getElementById('device-location-city').value=city;
					document.getElementById('device-location-state').value=state;
					document.getElementById('device-location-country').value=country;
					document.getElementById('device-location-longitude').value=longitude;
					document.getElementById('device-location-latitude').value=latitude;
					setTimeout(function () {waitingDialog.hide();}, 2000);
				  },
				  dataType: 'json'
			});
			
        });
function geoFindMe() {
	var appVars = getAppVars();
	var building='',road='',city='',state='',country='',longitude='',latitude='', district='';
	/**
			 * Module for displaying "Waiting for..." dialog using Bootstrap
			 *
			 * @author Eugene Maslovich <ehpc@em42.ru>
			 */

			var waitingDialog = waitingDialog || (function ($) {
				'use strict';

				// Creating modal dialog's DOM
				var $dialog = $(
					'<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" style="padding-top:15%; overflow-y:visible;">' +
					'<div class="modal-dialog modal-m">' +
					'<div class="modal-content">' +
						'<div class="modal-header"><h3 style="margin:0;"></h3></div>' +
						'<div class="modal-body">' +
							'<div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>' +
						'</div>' +
					'</div></div></div>');

				return {
					/**
					 * Opens our dialog
					 * @param message Custom message
					 * @param options Custom options:
					 * 				  options.dialogSize - bootstrap postfix for dialog size, e.g. "sm", "m";
					 * 				  options.progressType - bootstrap postfix for progress bar type, e.g. "success", "warning".
					 */
					show: function (message, options) {
						// Assigning defaults
						if (typeof options === 'undefined') {
							options = {};
						}
						if (typeof message === 'undefined') {
							message = 'Waiting for Geolocation';
						}
						var settings = $.extend({
							dialogSize: 'sm',
							progressType: '',
							onHide: null // This callback runs after the dialog was hidden
						}, options);

						// Configuring dialog
						$dialog.find('.modal-dialog').attr('class', 'modal-dialog').addClass('modal-' + settings.dialogSize);
						$dialog.find('.progress-bar').attr('class', 'progress-bar');
						if (settings.progressType) {
							$dialog.find('.progress-bar').addClass('progress-bar-' + settings.progressType);
						}
						$dialog.find('h3').text(message);
						// Adding callbacks
						if (typeof settings.onHide === 'function') {
							$dialog.off('hidden.bs.modal').on('hidden.bs.modal', function (e) {
								settings.onHide.call($dialog);
							});
						}
						// Opening dialog
						$dialog.modal();
					},
					/**
					 * Closes dialog
					 */
					hide: function () {
						$dialog.modal('hide');
					}
				};

			})(jQuery);
			waitingDialog.show('Waiting for Geolocation', {dialogSize: 'sm', progressType: 'primary'});
	function success(position) {
		latitude  = position.coords.latitude;
		longitude = position.coords.longitude;
		
		$.ajax({
			type: "POST",
			url: appVars.basePath+'api/google/place_longlan?latlng='+latitude+','+longitude+'&key=AIzaSyAqXkH1ZWerwlt--GcMH-stlrZ-tlY7vQI',
			success: function (data){ console.log("description=" ,data.results);
				
				document.getElementById('device-location-building').value='';
				document.getElementById('device-location-road').value='';
				document.getElementById('device-location-district').value='';
				document.getElementById('device-location-city').value='';
				document.getElementById('device-location-state').value='';
				document.getElementById('device-location-country').value='';
				document.getElementById('device-location-longitude').value='';
				document.getElementById('device-location-latitude').value='';
				
				longitude=data.results[0].geometry.location.lat;
				latitude=data.results[0].geometry.location.lng;
				
				for( i=0; i<(data.results[0].address_components.length); i++){
						if(data.results[0].address_components[i].types[0]=='route'){
							road=data.results[0].address_components[i].long_name;
							
						}else if(data.results[0].address_components[i].types[0]=='sublocality_level_1'){
							district=data.results[0].address_components[i].long_name;
							
						}else if(data.results[0].address_components[i].types[0]=='locality' || data.results[0].address_components[i].types[0]=='sublocality'){
							city=data.results[0].address_components[i].long_name;
							
						}else if(data.results[0].address_components[i].types[0]=='administrative_area_level_1'){
							state=data.results[0].address_components[i].long_name;
							
						}else if(data.results[0].address_components[i].types[0]=='country'){
							country=data.results[0].address_components[i].long_name;							
						}
					}
				//longitude=data.results[0].address_components.geometry.location.lat;
				//latitude=data.results[0].address_components.geometry.location.lng;
				
				document.getElementById('device-location-building').value=building;
				document.getElementById('device-location-road').value=road;
				document.getElementById('device-location-district').value=district;
				document.getElementById('device-location-city').value=city;
				document.getElementById('device-location-state').value=state;
				document.getElementById('device-location-country').value=country;
				document.getElementById('device-location-longitude').value=longitude;
				document.getElementById('device-location-latitude').value=latitude;	
				setTimeout(function () {waitingDialog.hide();}, 2000);
			},
			dataType: 'json'
		});
	};
	function error() {
		document.getElementById('longitude').value= "Unable to retrieve your location";
	};
	navigator.geolocation.getCurrentPosition(success, error);
	
}