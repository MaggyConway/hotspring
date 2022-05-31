export default ($) => {
  // search & dealer panels
  $('.search_icon').on('click', function (e) {
    e.preventDefault();
    $('.desktop-search-panel').toggleClass('open');

    $('.mobile-menu').removeClass('opened');
    $('.hamburger').removeClass('is-active');
  });

  $('header .additional-menu a[data-mobile-title="Dealer"]').on('click', function (e) {
    e.preventDefault();
    $('.desktop-dealer-panel').toggleClass('open');

    $('.mobile-menu').removeClass('opened');
    $('.hamburger').removeClass('is-active');
  });

  $('.desktop-search-panel__close, .desktop-dealer-panel__close').on('click', function (e) {
    e.preventDefault();
    $(this).parent().parent().removeClass('open');
  });

  $('.hamburger').on('click', function (e) {
    e.preventDefault();
    $('.mobile-menu').toggleClass('opened');
    $(this).toggleClass('is-active');

    $('.desktop-search-panel').removeClass('open');
    $('.desktop-dealer-panel').removeClass('open');
  });

  $('.mobile-menu .dark-blur').on('click', function (e) {
    e.preventDefault();
    $('.mobile-menu').removeClass('opened');
    $('.hamburger').removeClass('is-active');
  });
}
