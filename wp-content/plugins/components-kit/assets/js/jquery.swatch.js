(function ($) {
  $(document).ready(function () {
    var model = $(".color-selector-spa").data("model");

    function selectShellSwatch(select) {
      $('.shell .swatch.selected').removeClass('selected');
      select.addClass('selected');
      var shell = select.data('shell');

      $(".shells img").each(function () {
        var shells = $(this).data('shell');
        if (shells == shell) {
          $(this).show();
          //$(".shell h4").text(shells);
        } else {
          $(this).hide();
        }
      });
    }

    function selectСabinetsSwatch(select) {
      $('.cabinet .swatch.selected').removeClass('selected');
      select.addClass('selected');
      var cabinet = select.data('cabinet');

      $(".cabinets img").each(function () {
        var cabinets = $(this).data('cabinet');
        if (cabinets == cabinet) {
          $(this).show();
          //$(".cabinet h4").text(cabinets);
        } else {
          $(this).hide();
        }
      });

      return cabinet;
    }

    $(".shell .swatch").hover(
      function () {
        var shells = $(this).data('shell');
        //$(".shell h4").text(shells);
      },
      function () {
        var shellsSelect = $('.shell .swatch.selected').data('shell');
        //$(".shell h4").text(shellsSelect);
      }
    );

    $(".cabinet .swatch").hover(
      function () {
        var cabinets = $(this).data('cabinet');
        //$(".cabinet h4").text(cabinets);
      },
      function () {
        var cabinetsSelect = $('.cabinet .swatch.selected').data('cabinet');
        //$(".cabinet h4").text(cabinetsSelect);
      }
    );

    function initColorSelectorSpa() {
      $(".shell .swatch").removeClass('selected');
      $(".cabinet .swatch").removeClass('selected');
      $('.shell-colors').hide();

      $('.shell-colors').first().show();
      $('.shell-colors').first().addClass('d-flex flex-column');
      selectShellSwatch($(".shell .swatch").first());
      selectСabinetsSwatch($(".cabinet .swatch").first());
    }

    initColorSelectorSpa();

    // on click for shells
    $(".shell .swatch").on("click", function (e) {
      e.preventDefault();
      selectShellSwatch($(this));
    });

    // on click for cabinets
    $(".cabinet .swatch").on("click", function (e) {
      e.preventDefault();
      var cabinet = selectСabinetsSwatch($(this));

      var shellСolorsCurrents = null;

      $(".shell-colors").each(function () {
        var shellfor = $(this).data('shellfor');
        if($(this).hasClass('d-flex')) {
          $(this).removeClass('d-flex flex-column');
        }
        if (shellfor === cabinet) {
          $(this).show();
          $(this).addClass('d-flex flex-column');
          shellСolorsCurrents = $(this);
        } else {
          $(this).hide();
        }
      });

      var check = false;

      shellСolorsCurrents.find('.swatch').each(function (i, item) {
        var shellNew = $(this).data('shell');
        var shellCurent = $('.shell .swatch.selected').data('shell');
        if (shellCurent == shellNew) {
          $('.shell .swatch.selected').removeClass('selected');
          $(this).addClass('selected');
          check = true;
        }
      })

      if (!check) {
        selectShellSwatch(shellСolorsCurrents.find('.swatch').first());
      }
    });
  });
})(jQuery);