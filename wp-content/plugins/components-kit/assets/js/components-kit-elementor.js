( function( $ ) {
	/**
 	 * @param $scope The Widget wrapper element as a jQuery element
	 * @param $ The jQuery alias
	 */
	var WidgetSectionCarouselSlideHandler = function( $scope, $ ) {
        var num = 0;
        $($scope).find('.section-carousel-slider').each( function() {

          num++;
          var swiperElement = this;
          var swiperConfig = {
            pagination: {
              el: ".swiper-pagination",
              clickable: true
            },
            navigation: {
              nextEl: ".swiper-button-next",
              prevEl: ".swiper-button-prev",
            },
            effect: 'fade',
            fadeEffect: {
              crossFade: true
            },
          }
          if ( 'undefined' === typeof Swiper ) {
            var asyncSwiper = elementorFrontend.utils.swiper;
            new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
              console.log('async Instance', num);
            } );
          } else {
            new Swiper( swiperElement, swiperConfig );
          }
        });
        $(window).on('resize load', function() {
          if ($(window).width() <= '768') {
            // $('.image_xl').hide();
            // $('.image_x').show();
          } else {
            // $('.image_xl').show();
            // $('.image_x').hide();
          }
        });
	};

	var WidgetSectionCardsCarouselHandler = function( $scope, $ ) {
        var num = 0;
        var mySwiper = [];
        var swiperConfig = {
          loop: true,
          slidesPerView: 'auto',
          centeredSlides: true,
          a11y: true,
          keyboardControl: true,
          grabCursor: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true
          },
          paginationClickable: true,
        }

        var breakpoint = window.matchMedia( '(min-width:31.25em)' );

        $($scope).find('.cards-сarousel').each( function() {
          var swiperElement = this;
          num++;

            // breakpoint where swiper will be destroyed
            // and switches to a dual-column layout

            // keep track of swiper instances to destroy later
            // var mySwiper;

            var breakpointChecker = function() {

              // if larger viewport and multi-row layout needed
              if ( breakpoint.matches === true ) {

                // clean up old instances and inline styles when available
                if ( mySwiper[num] !== undefined ) mySwiper[num].destroy( true, true );

                // or/and do nothing
                return;

                // else if a small viewport and single column layout needed
              } else if ( breakpoint.matches === false ) {

                  // fire small viewport version of swiper
                  return enableSwiper();

              }
            };

            var enableSwiper = function() {

              if ( 'undefined' === typeof Swiper ) {
                var asyncSwiper = elementorFrontend.utils.swiper;
                new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
                  console.log('async Instance', num);
                  mySwiper[num] = newSwiperInstance;
                } );
              } else {
                new Swiper( swiperElement, swiperConfig );
                mySwiper[num] =  new Swiper(swiperElement, swiperConfig);
              }

            };

            // keep an eye on viewport size changes
            breakpoint.addListener(breakpointChecker);

            // kickstart
            breakpointChecker();

        });
	};

    var WidgetSectionCardsCarouselHandlerNew = function ( $scope, $ ) {
        var num = 0;

        $($scope).find('.swiper-container').each( function() {
            var count_slide = $('.swiper-wrapper').attr('data-slide');
            num++;
            var swiperElement= '.swiper-product';

            var swiperConfig = {
               slidesPerView: 1,
               slideClass: 'swiper-slide',
               wrapperClass: 'swiper-wrapper',
               observer: true,
               runCallbacksOnInit: true,
               spaceBetween: 24,
               speed: 500,
               updateOnImagesReady: true,
               autoHeight: true,
               pagination: {
                  el: '.swiper-object .swiper-pagination',
                  type: 'bullets',
                  clickable: true
               },
               navigation: {
                  nextEl: '.swiper-object .swiper-button-next',
                  prevEl: '.swiper-object .swiper-button-prev',
               },
               breakpoints: {
                  320: {
                     slidesPerView: 1,
                  },
                  768: {
                     slidesPerView: count_slide,
                  },
               },
            };

            if ( 'undefined' === typeof Swiper ) {
                var asyncSwiper = elementorFrontend.utils.swiper;
                new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
                    //console.log('async Instance', num);
                } );
            } else {
                new Swiper( swiperElement, swiperConfig );
            }
        });
    }

    var WidgetSectionShopByModelCarouselHandler = function( $scope, $ ) {
        var num = 0;

        $($scope).find('.swiper-container').each( function() {
            num++;
            swiperElement= '.swiper-product-shop';

            swiperConfig = {
                slidesPerView: 1,
                slideClass: 'swiper-slide',
                wrapperClass: 'swiper-wrapper',
                observer: true,
                runCallbacksOnInit: true,
                speed: 500,
                pagination: false,
                updateOnImagesReady: true,
                navigation: {
                    nextEl: '.swiper-product-shop+.swiper-buttons .swiper-button-next',
                    prevEl: '.swiper-product-shop+.swiper-buttons .swiper-button-prev',
                },
                breakpoints: {
                    720: {
                        slidesPerView: 2,
                    },
                    1025: {
                        slidesPerView: 3,
                    },
                },
            };

            if ( 'undefined' === typeof Swiper ) {
                var asyncSwiper = elementorFrontend.utils.swiper;
                new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
                    //console.log('async Instance', num);

                    $(window).on('resize load', function() {
                        var ind = 0;
                        var res = $('.results .count');

                        if ($(window).width() <= '720') {
                            res.text('1 of ');
                            newSwiperInstance.on('activeIndexChange', function () {
                                ind = newSwiperInstance.activeIndex;
                                res.text(ind+1+' of ');
                            });
                        } else {
                            res.text('');
                        }
                    });

                } );
            } else {
                new Swiper( swiperElement, swiperConfig );
            }
        });

        $('#swiperFilter select').on('change', function() {
            var filter = $('#swiperFilter select').find(":selected").val();
            setSlideFilter(filter);
        });
    }

    var WidgetSectionCardsMobileCarouselHandler = function( $scope, $ ) {
        var num = 0;
        var slide = $('.swiper_mobile .model-card-slide');

        var initSlider = function() {

            if(!slide.hasClass('swiper-slide')) {
                slide.addClass('swiper-slide');

                slide.wrapAll('<div class="swiper-container swiper-product" /><div class="swiper-wrapper" />');
                $('.swiper_mobile .swiper-wrapper').after('<div class="swiper-pagination"></div>');
                $('.swiper_mobile .swiper-container').after('<div class="swiper-navigation">\n' +
                    '        <div class="swiper-button-prev"></div>\n' +
                    '        <div class="swiper-button-next"></div>\n' +
                    '      </div>');
            }

            $($scope).find('.swiper-wrap').each( function() {
                num++;
                var swiperElement = '.swiper_mobile .swiper-wrap .swiper-product';

                var swiperConfig = {
                    slidesPerView: 1,
                    slideClass: 'swiper-slide',
                    wrapperClass: 'swiper-wrapper',
                    observer: true,
                    spaceBetween: 24,
                    runCallbacksOnInit: true,
                    speed: 500,
                    updateOnImagesReady: true,
                    pagination: {
                        el: '.swiper-pagination',
                        type: 'bullets',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.swiper-product+.swiper-navigation .swiper-button-next',
                        prevEl: '.swiper-product+.swiper-navigation .swiper-button-prev',
                    }
                };

                if ( 'undefined' === typeof Swiper ) {

                    var asyncSwiper1 = elementorFrontend.utils.swiper;
                    new asyncSwiper1( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
                        //console.log('async Instance', num);
                    } );
                } else {
                    new Swiper( swiperElement, swiperConfig );
                }
            });
        }

        $(window).on('resize load', function() {
            if ($(window).width() <= '575') {

                initSlider();
            } else {
                if(slide.hasClass('swiper-slide')) {
                    $('.swiper_mobile .swiper-wrap .swiper-product')[0].swiper.destroy( true, true );
                    slide.removeClass('swiper-slide');
                    slide.unwrap().unwrap();
                    $('.model-card-slide').attr('style', '');
                    $('.swiper_mobile .swiper-navigation').remove();
                    $('.swiper_mobile .swiper-pagination').remove();
                    $('.swiper_mobile .swiper-notification').remove();
                }
            }
        });

    };
    var WidgetSectionCollectionsGridCardsMobileCarouselHandler = function( $scope, $ ) {
      var num = 0;
      var slide = $('.swiper_mobile_models .model-card-slide');

      var initSlider = function() {

          if(!slide.hasClass('swiper-slide')) {
              slide.addClass('swiper-slide');
              slide.wrapAll('<div class="swiper-container swiper-product" /><div class="swiper-wrapper" />');
              $('.swiper_mobile_models .swiper-wrapper').after('<div class="swiper-pagination"></div>');
              $('.swiper_mobile_models .swiper-container').after('<div class="swiper-navigation">\n' +
                  '        <div class="swiper-button-prev"></div>\n' +
                  '        <div class="swiper-button-next"></div>\n' +
                  '      </div>');
          }

          $($scope).find('.swiper-wrap').each( function() {
              num++;
              var swiperElement = '.swiper_mobile_models .swiper-wrap .swiper-product';

              var swiperConfig = {
                  slidesPerView: 1,
                  slideClass: 'swiper-slide',
                  wrapperClass: 'swiper-wrapper',
                  observer: true,
                  spaceBetween: 24,
                  runCallbacksOnInit: true,
                  speed: 500,
                  updateOnImagesReady: true,
                  // pagination: {
                  //     el: '.swiper-pagination',
                  //     type: 'bullets',
                  // },
                  navigation: {
                      nextEl: '.swiper-product+.swiper-navigation .swiper-button-next',
                      prevEl: '.swiper-product+.swiper-navigation .swiper-button-prev',
                  }
              };

              if ( 'undefined' === typeof Swiper ) {

                  var asyncSwiper1 = elementorFrontend.utils.swiper;
                  new asyncSwiper1( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
                      //console.log('async Instance', num);
                  } );
              } else {
                  new Swiper( swiperElement, swiperConfig );
              }
          });
      }

      $(window).on('resize load', function() {
          if ($(window).width() <= '768') {

              initSlider();
          } else {
              if(slide.hasClass('swiper-slide')) {
                  $('.swiper_mobile_models .swiper-wrap .swiper-product')[0].swiper.destroy( true, true );
                  slide.removeClass('swiper-slide');
                  slide.unwrap().unwrap();
                  $('.model-card-slide').attr('style', '');
                  $('.swiper-navigation').remove();
              }
          }
      });

  };

    var WidgetSectionFullWidthGalleryHandler = function( $scope, $ ) {

        var thumbs_container = '.mySwiper';
        var slider_container = '.mySwiper2';

        var swiperThumbsConfig = {
            spaceBetween: 1,
            loop: true,
            centeredSlides: true,
            slidesPerView: "auto",
            touchRatio: 0.4,
            loopedSlides: 4,
            slideToClickedSlide: true,
            watchSlidesProgress: true,
            watchSlidesVisibility: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                320: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 5,
                },
            },
        };

        if ( 'undefined' === typeof Swiper ) {

            var asyncSwiper = elementorFrontend.utils.swiper;
            new asyncSwiper( thumbs_container, swiperThumbsConfig ).then( ( newSwiperThumbsInstance ) => {
                swiperThumbsSlider = newSwiperThumbsInstance;

                var swiperSliderConfig = {
                    loop: true,
                    loopedSlides: 4,
                    spaceBetween: 10,
                    grabCursor: true,
                    navigation: {
                      nextEl: ".swiper-button-next",
                      prevEl: ".swiper-button-prev",
                    },
                    autoScrollOffset: 1,
                };

                new asyncSwiper( slider_container, swiperSliderConfig ).then( ( newSwiperSliderInstance ) => {
                    swiperSlider = newSwiperSliderInstance;
                    swiperSlider.controller.control = newSwiperThumbsInstance;
                    newSwiperThumbsInstance.controller.control = swiperSlider;
                } );
            });

        } else {
            swiperThumbs = new Swiper( thumbs_container, swiperThumbsConfig );

            var swiperSliderConfig = {
                spaceBetween: 10,
                thumbs: {
                    swiper: swiperThumbs,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            };
            swiperSlider = new Swiper( slider_container, swiperSliderConfig );
        }
    }
    var WidgetSectionMediaCarouselHandler = function( $scope, $ ) {
      var num = 0;

      function slideerActive() {
        $($scope).find('.section-media-carousel').each( function() {
          num++;
          var swiperElement = this;
          var swiperConfig = {
            observer: true,
            observeParents: true,
            pagination: {
              el: ".media-tabs__content .swiper-pagination",
              clickable: true
            },
            navigation: {
              nextEl: ".media-tabs__content .swiper-button-next",
              prevEl: ".media-tabs__content .swiper-button-prev",
            },
          }

          if ( 'undefined' === typeof Swiper ) {
            var asyncSwiper = elementorFrontend.utils.swiper;
            new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
              console.log('async Instance', num);
            } );
          } else {
            new Swiper( swiperElement, swiperConfig );
          }

        });
      }
      slideerActive();

      $($scope).find('.media-tabs').each( function() {    
        var changeTabsClass = () => {
          if ($(window).width() <= '576') {
            let slider = $('.media-tabs__content');
            //th.parent('.media-tabs__tab').after(slider);
            var tab = $('.media-tabs .media-tabs__content .swiper-slide');
            tab.hide();
            //$('.media-tabs .media-tabs__header a').filter(':first').addClass('active');

            $('.media-tabs .media-tabs__header a').click(function(){
              let th = $(this);
              let ths = '.' + $(this).attr('data-tab-id');
              tab.hide().filter(ths).show();
              th.parent('.media-tabs__tab').after(slider);
              $('.media-tabs .media-tabs__header a').removeClass('active');
              $(th).addClass('active');
              console.log(ths);
              slideerActive();

              return false;
            });
          } else {
            var tab = $('.media-tabs .media-tabs__content .swiper-slide');
            tab.hide().filter('.data-tab-id-1').show();
            $('.media-tabs .media-tabs__header a').filter(':first').addClass('active');

            $('.media-tabs .media-tabs__header a').click(function(){
              let th = $(this);
              let ths = '.' + $(this).attr('data-tab-id');
              tab.hide().filter(ths).show();
              $('.media-tabs .media-tabs__header a').removeClass('active');
              $(this).addClass('active');
              console.log(ths);
              slideerActive();

              return false;
            });
          }
        }

        $(window).ready(changeTabsClass());
        $(window).on('resize', changeTabsClass());
      });


    };
    var WidgetHeroModelHandler = function( $scope, $ ) {

      var thumbs_container = '.mySwiper';
      var slider_container = '.swiper-hero-model';

      var swiperThumbsConfig = {
          spaceBetween: 10,
          slidesPerView: 5,
          freeMode: true,
          watchSlidesProgress: true,
          watchSlidesVisibility: true,
          breakpoints: {
              320: {
                  slidesPerView: 3,
              },
              768: {
                  slidesPerView: 5,
              },
          },
      };

      if ( 'undefined' === typeof Swiper ) {

          var asyncSwiper = elementorFrontend.utils.swiper;
          new asyncSwiper( thumbs_container, swiperThumbsConfig ).then( ( newSwiperThumbsInstance ) => {

              var swiperSliderConfig = {
                  spaceBetween: 10,
                  navigation: {
                      nextEl: ".swiper-button-next",
                      prevEl: ".swiper-button-prev",
                  },
                  thumbs: {
                      swiper: newSwiperThumbsInstance
                  }
              };

              new asyncSwiper( slider_container, swiperSliderConfig ).then( ( newSwiperSliderInstance ) => {
                  swiperSlider = newSwiperSliderInstance;
              } );
          });

      } else {
          swiperThumbs = new Swiper( thumbs_container, swiperThumbsConfig );

          var swiperSliderConfig = {
              spaceBetween: 10,
              thumbs: {
                  swiper: swiperThumbs,
              },
              navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
              },
          };
          swiperSlider = new Swiper( slider_container, swiperSliderConfig );
      }
      $(window).on('resize load', function() {
        if ($(window).width() <= '768') {
          let specifications = $('.hero-model .specifications'),
              cwiperContainer = $('.hero-model .swiper-container');

          cwiperContainer.append(specifications);
        }
      });
    }
    var WidgetSectionHotspotModelHandler = function( $scope, $ ) {
      var num = 0;

      $($scope).find('.section-hotspot-model').each( function() {
        num++;
        var swiperElement = this;
        var swiperConfig = {
          observer: true,
          observeParents: true,
          pagination: {
            el: ".swiper-pagination",
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
        }

        if ( 'undefined' === typeof Swiper ) {
          var asyncSwiper = elementorFrontend.utils.swiper;
          new asyncSwiper( swiperElement, swiperConfig ).then( ( newSwiperInstance ) => {
            console.log('async Instance', num);
          } );
        } else {
          new Swiper( swiperElement, swiperConfig );
        }

        $('.tab-container .tab-content').hide();
        $('.point-plus, .tab-head, .tab-btn-nav').on('click', function() {
          let tAttr = $(this).attr('data-tab-index'),
              tTab = $('.tab' + tAttr),
              cTab = $('.tab-container .tab').length;

          if ( tTab.hasClass('active') ) {
            $('.tab').removeClass('active');
            $('.tab-container .tab-content').slideUp();
            $('.tab-container .tab').not(tAttr).slideToggle();
            $('.tab .tab-content').slideUp();
            
            if ($('.massage-image .swiper-slide__massage-image_tab').length > 0) {
              $('.massage-image .swiper-slide__massage-image_tab').hide();
              $('.massage-image .swiper-slide__massage-image').show();
            }
          } else {
            $('.tab').removeClass('active');
            $('.tab-container .tab-content').slideUp();
            $('.tab-container .tab').not(tAttr).slideUp();
            if ($(this).hasClass('point-plus')) {
              tTab.show();
            }
            tTab.addClass('active');
            $('.tab' + tAttr + ' .tab-content').slideToggle();
            $(this).removeClass('active');

            if ($(this).hasClass('tab-btn-nav')) {
              tTab.slideToggle();
              return false;
            }
            if ($('.massage-image .swiper-slide__massage-image_tab').length > 0) {
              $('.massage-image .swiper-slide__massage-image').hide();
              $('.massage-image .swiper-slide__massage-image_tab').hide();
              $('.massage-image ' + tAttr).show();
            }
          }

        });

      });
      
    };

    /* $('.cost-widget').on('mouseover', function (e) {
        e.preventDefault();
        addPopover();
    }) */

    // init on desktop
    $('.swiper-filter .filter-slide.current a').click();

	// Make sure you run this code under Elementor.
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/section-carousel-slide.default', WidgetSectionCarouselSlideHandler );
        //elementorFrontend.hooks.addAction( 'frontend/element_ready/cards-сarousel.default', WidgetSectionCardsCarouselHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/cards-сarousel.default', WidgetSectionCardsCarouselHandlerNew );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/cards.default', WidgetSectionCardsMobileCarouselHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section-shop-by-model.default', WidgetSectionShopByModelCarouselHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section-full-width-gallery.default', WidgetSectionFullWidthGalleryHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section-model-cards.default', WidgetSectionCollectionsGridCardsMobileCarouselHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section-media-carousel.default', WidgetSectionMediaCarouselHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/hero-model.default', WidgetHeroModelHandler );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/section-hotspot-model.default', WidgetSectionHotspotModelHandler );
	} );

    function setSlideFilter(filter) {
        $('.swiper-product-shop .swiper-slide').css('display', 'none');
        $('.swiper-product-shop .swiper-slide' + (filter != '' ? '.' + filter : '')).css(
            'display',
            ''
        );
        $('.filter-slide').removeClass('current');
        var $parent = $('*[data-filter="' + filter + '"]').parent();
        $('.swiper-container-mobile .swiperFilterInfo span').text($parent.text());
        $parent.addClass('current');

        $('.swiper-product-shop')[0].swiper.update();
        $('.swiper-product-shop')[0].swiper.slideTo(0);

        // If less than 4 models are visible, add a class to center the list
        var visibleSlides = $('.swiper-product-shop .swiper-slide' + (filter != '' ? '.' + filter : '')).length;
        $(window).on('resize load', function() {
            if ($(window).width() <= '720') {
                $('.results .count').text('1 of ');
            } else {
                $('.results .count').text('');
            }
        });

        $('.results .number').text(visibleSlides);
        $('.swiper-product-shop > .swiper-wrapper').toggleClass('centered', (visibleSlides < 4));
    }

    // get desktop results
    $('.swiper-filter').on('click', 'a', function (e) {
        // e.preventDefault();
        const filter = $(this).attr('data-filter');
        setSlideFilter(filter);
        return false;
    })

    // get mobile results
    $('.swiper-container-mobile #swiperFilterResult').on('click', function (e) {
        e.preventDefault();
        var filter = $('.swiper-container-mobile #swiperFilter input:checked').val();
        setSlideFilter(filter);
    })

    function addPopover(){
      tippy('.cost-widget', {
        content: `
          <div id="popover-pop" data-template="true">
            <div class="popover-container">
              <span class="close-thik desktop-dealer-panel__close"></span>
              <div class="popover-header text-center">
                <h3>Pricing Guide</h3>
              </div>
              <div class="popover-top text-center">
                    <div class="popover-row">
                      <div class="popover-item text-center">
                        <div class="popover-description">Luxury</div>
                      </div>
                      <div class="popover-item">
                        <span class="cost-widget-popover" aria-expanded="false">
                          <span class="cost active-cost-part">$$$$$</span>
                        </span>
                      </div>
                      <div class="popover-item">
                        <div class="popover-cost">$20,000 and up</div>
                      </div>
                    </div>
                    <div class="popover-row">
                      <div class="popover-item text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" id="icon_arrow-up-down" data-name="icon/arrow-up-down" width="12" height="24.001" viewBox="0 0 12 24.001">
                          <path id="arrows-up-down-solid" d="M11.559,16.917a1.5,1.5,0,0,1,0,2.12l-4.5,4.5a1.5,1.5,0,0,1-2.121,0l-4.5-4.5a1.5,1.5,0,0,1,2.121-2.121L4.5,18.857V5.1L2.561,7.036A1.5,1.5,0,0,1,.44,4.914l4.5-4.5a1.5,1.5,0,0,1,2.121,0l4.5,4.5A1.5,1.5,0,0,1,9.439,7.036L7.5,5.1V18.857l1.94-1.94A1.5,1.5,0,0,1,11.559,16.917Z" transform="translate(0 0.025)" fill="#a9a9a9"/>
                        </svg>
                      </div>
                      <div class="popover-item">
                        <span class="cost-widget-popover" aria-expanded="false">
                          <span class="cost active-cost-part">$$$$</span>
                          <span class="cost passive-cost-part">$</span>
                        </span>
                      </div>
                      <div class="popover-item">
                        <div class="popover-cost">$16,000 - $19,999</div>
                      </div>
                    </div>
                    <div class="popover-row">
                      <div class="popover-item row-item text-center">
                        <div class="popover-description">Premium</div>
                      </div>
                      <div class="popover-item">
                        <span class="cost-widget-popover" aria-expanded="false">
                          <span class="cost active-cost-part">$$$</span>
                          <span class="cost passive-cost-part">$$</span>
                        </span>
                      </div>
                      <div class="popover-item">
                        <div class="popover-cost">$12,000 - $15,999</div>
                      </div>
                    </div>
                    <div class="popover-row">
                      <div class="popover-item text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" id="icon_arrow-up-down" data-name="icon/arrow-up-down" width="12" height="24.001" viewBox="0 0 12 24.001">
                          <path id="arrows-up-down-solid" d="M11.559,16.917a1.5,1.5,0,0,1,0,2.12l-4.5,4.5a1.5,1.5,0,0,1-2.121,0l-4.5-4.5a1.5,1.5,0,0,1,2.121-2.121L4.5,18.857V5.1L2.561,7.036A1.5,1.5,0,0,1,.44,4.914l4.5-4.5a1.5,1.5,0,0,1,2.121,0l4.5,4.5A1.5,1.5,0,0,1,9.439,7.036L7.5,5.1V18.857l1.94-1.94A1.5,1.5,0,0,1,11.559,16.917Z" transform="translate(0 0.025)" fill="#a9a9a9"/>
                        </svg>
                      </div>
                      <div class="popover-item">
                        <span class="cost-widget-popover" aria-expanded="false">
                          <span class="cost active-cost-part">$$</span>
                          <span class="cost passive-cost-part">$$$</span>
                        </span>
                      </div>
                      <div class="popover-item">
                        <div class="popover-cost">$8,000 - $11,999</div>
                      </div>
                    </div>
                    <div class="popover-row">
                      <div class="popover-item text-center">
                        <div class="popover-description">Value</div>
                      </div>
                      <div class="popover-item">
                        <span class="cost-widget-popover" aria-expanded="false">
                          <span class="cost active-cost-part">$</span>
                          <span class="cost passive-cost-part">$$$$</span>
                        </span>
                      </div>
                      <div class="popover-item">
                        <div class="popover-cost">up to $7,999</div>
                      </div>
                    </div>
              </div>
              <div class="popover-bottom text-left">
                <div class="row">
                  <div class="col">
                    <p>Dealers have sole discretion to set actual prices, which will vary based on options, accessories, installation costs, destination charges, finance charges, taxes and other local factors. Talk to your <a href="/hot-tub-dealers">local dealer</a> for your local price and to take advantage of ongoing promotions and offers.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `,
        allowHTML: true,
        arrow: false,
        maxWidth: '382px',
        animation: 'fade',
        duration: [250, 100],
        delay: 150,
        interactive: true,
        appendTo: document.body,
        theme: 'light',
        trigger: 'mouseenter', // 'click', 'manual'
        hideOnClick: true,
        onShown(instance) {
          jQuery(instance.popper).find('.close-thik').click(function(){
            instance.hide(0);
          });
        },
      });
    }
    function rePopover() {
      $('[data-tippy-root]').each(function() {
        if ($(this).is('#tippy-1') && $('[data-tippy-root]').length > 1) {
          $(this).remove();
        }
      });
      addPopover();
    }
    rePopover();
    $('.header').bind("DOMSubtreeModified",function(){
      rePopover();
     });
} )( jQuery );
