(function ($) {
	$(document).ready(function () {
		$BV.configure('global', {
			submissionContainerUrl: bazaarvoice.url
		});
		$BV.ui("rr", "show_reviews", {
			productId: bazaarvoice.subject_id,
		});
	});
})(jQuery);