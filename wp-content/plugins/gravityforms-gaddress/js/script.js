(function($) {

  function fillInAddress(autoComplete,id,element) {
    var place = autoComplete.getPlace();
    clearAddressIfno(id,element);
    var componentForm = {
      street_number: 'short_name',
      route: 'long_name',
      locality: 'long_name',
      administrative_area_level_1: 'short_name',
      country: 'long_name',
      postal_code: 'short_name'
    };

    // Get each component of the address from the place details
    // and fill the corresponding field on the form.
    for (var i = 0; i < place.address_components.length; i++) {
      var addressType = place.address_components[i].types[0];

      switch(addressType) {
        case 'locality':
          $(element).parent().parent().parent().find('#input_' + id + '_3').val(place.address_components[i][componentForm[addressType]]);
          break;
        case 'administrative_area_level_1':
          $(element).parent().parent().parent().find('#input_' + id + '_4').val(place.address_components[i][componentForm[addressType]]);
          break;
        case 'country':
          $(element).parent().parent().parent().find('#input_' + id + '_6').val(place.address_components[i][componentForm[addressType]]);
          break;
        case 'postal_code':
          $(element).parent().parent().parent().find('#input_' + id + '_5').val(place.address_components[i][componentForm[addressType]]);
          break;
        case 'street_number':
          $(element).parent().parent().parent().find('#input_' + id + '_1').val(place.address_components[i][componentForm[addressType]]);
          break;
        case 'route':
          var fullAddress = $(element).parent().parent().parent().find('#input_' + id + '_1').val() + ' ' + place.address_components[i][componentForm[addressType]];
          $(element).parent().parent().parent().find('#input_' + id + '_1').val(fullAddress);
          break;

        default:
          break;
      }
    }
  }

  function initialize(id, zip_only) {
    // var autoCompleteFields = document.getElementsByClassName('gca_field_' + id);
    // var autoCompleteField = autoCompleteFields[0];
    var autoCompleteFields = {};
    autoCompleteFields.value = '';

    if(zip_only){
      var options = {
        componentRestrictions: {'country':['us','ca']},
        types: ['(regions)'] // (cities)
      };
    }else{
      var options = {
        componentRestrictions: {'country':['us','ca']},
        types: ['geocode'] // (cities)
      };
    }
    $( 'form .gca_field_' + id ).each(function( index ) {
      var $this = this;
      clearAddressIfno(id,$this);
      autoCompleteFields = new google.maps.places.Autocomplete(this, options);
      google.maps.event.addListener(autoCompleteFields, 'place_changed', function () {fillInAddress(this,id,$this);});
    });
  }

  function clearAddressIfno(id,element) {
    for (var i = 1; i < 6; i++) {
      try {
        $(element).parent().parent().parent().find('#input_' + id + '_' + i).val('');
        $(element).parent().parent().parent().find('#input_' + id + '_' + i).prop( "disabled", false );
      } catch(e) {
        console.log('Error: ' + e.name + ":" + e.message + "\n" + e.stack); // (3) <--
      }
    }
  }

  // var autocomplete, forms, autoCompleteField;
  $(document).ready(function () {
    if( typeof(gaddress_settings_zo) === 'undefined' ){
      return;
    }
    $.each(gaddress_settings_zo, function( i, gaddress_setting ) {
      $.each(gaddress_setting.fields, function( k, v ) {
        $('form #field_' + v.id).each(function() {
          $( this ).find( '.ginput_container .address_line_1' ).hide();
          $( this ).find( '.ginput_container .address_line_2' ).hide();
          $( this ).find( '.ginput_container .address_city' ).hide();
          $( this ).find( '.ginput_container .address_state' ).hide();
          $( this ).find( '.ginput_container .address_country' ).hide();
          $( this ).find( '.ginput_container .address_zip label' ).hide();
          var zip_code = $( this ).find( '.ginput_container .address_zip input' );
          zip_code.attr( 'placeholder', gaddress_setting.placeholder );
          zip_code.addClass( 'gca_field_' + v.id );
          initialize( v.id, gaddress_setting.zip_only );
        });
      });
    });
  });
})(jQuery);
