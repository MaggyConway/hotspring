(function ($) {
    'use strict';
    $(function () {
        $("#xtm-wpml-connector-xmt_automatically_flag").click(function () {
            if ($(this).attr('checked')) {
                $("#automatically").show('fast');
            } else {
                $("#automatically").hide('fast');
            }
        });
    });
})(jQuery);
