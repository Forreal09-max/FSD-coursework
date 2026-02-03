// Maria Fleet Client-side scripts

$(document).ready(function () {
    console.log("Maria Fleet System Ready");

    // Mobile Menu Toggle
    $('#mobile-toggle').on('click', function () {
        $(this).toggleClass('active');
        $('#nav-menu').toggleClass('active');
        $('body').toggleClass('no-scroll');
    });

    // Close mobile menu on link click
    $('#nav-menu a').on('click', function () {
        $('#mobile-toggle').removeClass('active');
        $('#nav-menu').removeClass('active');
        $('body').removeClass('no-scroll');
    });

    // Auto-hide alert messages
    setTimeout(function () {
        $('.flash-message').fadeOut('slow');
    }, 5000);

    // Header reveal on scroll
    let lastScroll = 0;
    $(window).scroll(function () {
        const currentScroll = $(window).scrollTop();
        if (currentScroll > 100) {
            $('header').css('padding', '1rem 0');
        } else {
            $('header').css('padding', '1.5rem 0');
        }
        lastScroll = currentScroll;
    });
});
