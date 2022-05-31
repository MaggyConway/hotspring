export default ($) => {
    $('body').append('<div class="back-to-top"></div>');

    const backToTop = $('.back-to-top');
    backToTop.on('click', () => {
        $('html,body').animate({ scrollTop: 0 }, 0);

        return false;
    });

    $(window).on('scroll', () => {
        const offset = $(window).scrollTop();
        const viewportHeight = $(window).height();

        if (offset > viewportHeight) {
            backToTop.addClass('active');
        } else {
            backToTop.removeClass('active');
        }
    });
}
