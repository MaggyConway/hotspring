(function($) {

  function fillInAddress_f(autoComplete, id, element) {
    var place = autoComplete.getPlace();
    clearAddressIfno_f(id, element);
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

      switch (addressType) {
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

  function initialize_f(id) {
    // var autoCompleteFields = document.getElementById('gca_field_' + id);
    var autoCompleteFields = {};
    autoCompleteFields.value = '';

    var options = {
      componentRestrictions: {'country': ['us', 'ca']},
      types: ['geocode'] // (cities)
    };
    $('form #gca_field_' + id).each(function (index) {
      var $this = this;
      clearAddressIfno_f(id, $this);
      autoCompleteFields = new google.maps.places.Autocomplete(this, options);
      google.maps.event.addListener(autoCompleteFields, 'place_changed', function () {
        fillInAddress_f(this, id, $this);
      });
    });
  }

  function clearAddressIfno_f(id, element) {
    for (var i = 1; i < 6; i++) {
      try {
        $(element).parent().parent().parent().find('#input_' + id + '_' + i).val('');
        $(element).parent().parent().parent().find('#input_' + id + '_' + i).prop("disabled", false);
      } catch (e) {
        console.log('Error: ' + e.name + ":" + e.message + "\n" + e.stack); // (3) <--
      }
    }
  }

  $(document).ready(function () {
    if (typeof (gaddress_settings) === 'undefined') {
      return;
    }
    $.each(gaddress_settings, function (i, gaddress_setting) {
      $.each(gaddress_setting.fields, function (k, v) {
        $('form #field_' + v.id).each(function () {
          $(this);
          $(this).find('.ginput_container').hide();
          var div = document.createElement('div');
          div.innerHTML = '<div class="ginput_container ginput_container_text"><input name="google_customer_address_input" tabindex="4" id="gca_field_' + v.id + '" type="text" value="" placeholder="' + gaddress_setting.placeholder + '" class="medium google_customer_address_input" ></div>';
          this.appendChild(div);
          initialize_f(v.id);
        });
      });
    });
  });
})(jQuery);