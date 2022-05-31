(function ($) {
  $(document).ready(function () {
    $BV.configure("global", {
      submissionContainerUrl: bazaarvoice.url
    });
    $BV.ui("rr", "show_reviews", {
      productId: bazaarvoice.subject_id,
      doShowContent: function() {
        showReviews();
      },
      onEvent: function(json) {
        switch (json.eventSource) {
          case 'Display':
            var elem = document.querySelector('#BVRRSummaryContainer');
            var style = document.createElement('style');
            style.innerHTML = '#reviews-section .BVRRCustomWriteReviewLink{display: none;}';
            style.innerHTML += '#reviews-section .BVRRLabel.BVRRRatingNormalLabel {display: none !important;}';
            style.innerHTML += '#reviews-section .BVRRPrimarySummary .BVRRRatingContainerStar .BVRRRatingNormal {width: 185px;}';
            style.innerHTML += '#reviews-section .BVRRPrimarySummary .BVRRRatingSummaryStyle2 {max-width: 350px;}';
            style.innerHTML += '#reviews-section .BVRRPrimaryRatingSummary .BVRRRatingSummaryLinkRead {left: 0;}';
            var ref = document.querySelector('script');
            ref.parentNode.insertBefore(style, ref);
            break;
        }
      }
    });
  });
  function showReviews() {
    var offset = getReviewsOffset();
    $('html, body').animate({
      scrollTop: offset
    }, {
      duration : 800,
      step : function(now, tween) {
        var newOffset = getReviewsOffset();
        if (tween.end !== newOffset) {
          tween.end = newOffset;
        }
      }
    });
    return false;
  }
  function getReviewsOffset() {
    var offset = 0;
    if ($('#bv-section-container').length) {
      offset += parseInt($('#bv-section-container').offset().top);
    }
    if ($('header .fixed-top').css("position") == 'fixed') {
      offset -= parseInt($('header .fixed-top').height());
    }
    return offset;
  }
})(jQuery);
