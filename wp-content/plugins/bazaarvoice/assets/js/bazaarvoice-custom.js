(function ($) {
	$(document).ready(function () {
		$BV.configure('global', {
			submissionContainerUrl: bazaarvoice.url
		});
		$BV.ui("rr", "show_reviews", {
			productId: bazaarvoice.subject_id,
			doShowContent: function () {
				showReviews();
			},
			onEvent: function (json) {
				switch (json.eventSource) {
					// On initial display alter bv reviews widget
					case 'Display':
						if (json.attributes.numReviews === 0) {
							var elem = document.querySelector('.productSummaryContainer #BVRRSummaryContainer');
							if (typeOf(elem) !== 'undefined') {
								var newElemHTLM = '';
								newElemHTLM += '<p><img src="/wp-content/uploads/2018/10/bv_stars_translucent.png"></img></p>';
								newElemHTLM += '<p class="BVRRCustomWriteReviewLink"><a class="blue-button blue-button--gray-override" href="/hot-tub-owners/rate-your-spa">Be the first to write a review</a>';
								if (typeOf(bazaarvoice.parentProductReviewUrl) !== 'undefined') {
									if( bazaarvoice.parentProductName == 'Highlife®' || bazaarvoice.parentProductName == 'Limelight®' ) {
										newElemHTLM += '<a href="/request-brochure">Download Brochure</a> <a href="/get-pricing">Get a Personal Quote</a>';
									} else {
										newElemHTLM += '<a class="blue-button blue-button--gray-override" href="' + bazaarvoice.parentProductReviewUrl + '">See All ' + bazaarvoice.parentProductName + ' Reviews</a>';
									}
								} else {
									newElemHTLM += '<a class="blue-button blue-button--gray-override" href="#BVRRWidgetID" onclick="showReviews();">See All ' + bazaarvoice.parentProductName + ' Reviews</a>';
								}
								newElemHTLM += '</p>';

								elem.innerHTML = newElemHTLM;
							}
						} else {
							// Adjust read model review text
							var elem = document.querySelector('.productReviews .BVRRRatingSummaryLinks .BVRRNonZeroCount');
							if (typeOf(elem) !== 'undefined') {
								var elemHTML = elem.innerHTML;
								var newElemHTLM = elemHTML.replace('from', 'See All');
								elem.innerHTML = newElemHTLM;
							}

							// // Add button leading to collection (parent product) reviews
							// var elem = document.querySelector('.productReviews .BVRRCustomWriteReviewLink');
							// if (typeOf(elem) !== 'undefined' && typeOf(bazaarvoice.parentProductName) !== 'undefined') {
							// 	var elemHTML = elem.innerHTML;
							// 	var newElemHTLM;
							// 	if (typeOf(bazaarvoice.parentProductReviewUrl) !== 'undefined') {
							// 		newElemHTLM = elemHTML + '<a href="' + bazaarvoice.parentProductReviewUrl + '">See All ' + bazaarvoice.parentProductName + ' Reviews</a>';
							// 	} else {
							// 		newElemHTLM = elemHTML + '<a href="#BVRRWidgetID" onclick="showReviews();">See All ' + bazaarvoice.parentProductName + ' Reviews</a>';
							// 	}
							// 	elem.innerHTML = newElemHTLM;
							// }

							// Add button leading to collection (parent product) reviews
							var elem = document.querySelector('.productSummaryContainer .BVRRCustomWriteReviewLink');
							if (typeOf(elem) !== 'undefined' && typeOf(bazaarvoice.parentProductName) !== 'undefined') {
								var elemHTML = elem.innerHTML;
								var newElemHTLM;
								if (typeOf(bazaarvoice.parentProductReviewUrl) !== 'undefined') {
									if( bazaarvoice.parentProductName == 'Highlife®' || bazaarvoice.parentProductName == 'Limelight®' ) {
										newElemHTLM = '<a href="/request-brochure">Download Brochure</a> <a href="/get-pricing">Get a Personal Quote</a>';
									} else {
										newElemHTLM = elemHTML + '<a href="' + bazaarvoice.parentProductReviewUrl + '">See All ' + bazaarvoice.parentProductName + ' Reviews</a>';
									}
								} else {
									newElemHTLM = elemHTML + '<a href="#BVRRWidgetID" onclick="showReviews();">See All ' + bazaarvoice.parentProductName + ' Reviews</a>';
								}
								elem.innerHTML = newElemHTLM;
							}

						}
						break;
				}

			}
		});
	});

	function showReviews() {
		if($('.bv-button-wrap button.bv-button').hasClass("collapsed")) {
      $('.bv-button-wrap button.bv-button').click();
		}
		var offset = 0;
		if ($('#bv-section').length) {
			offset += parseInt($('#bv-section').offset().top);
		}
		if($('header .fixed-top').css("position") == 'fixed') {
      offset -= parseInt($('header .fixed-top').height());
		}

		$('html, body').animate({ scrollTop: offset }, 800);

		return false;
	}
})(jQuery);
