/**
 * Created by Bobby on 5/16/15.
 */
(function ($) {
    wp.customize('featured_tag_font', function (value) {
        value.bind(function (to) {
            $('.featured-tag a').css('font-size', to + 'px');
        });
    });
    wp.customize('tag_font_color', function (value) {
        value.bind(function (to) {
            $('.featured-tag a').css('color', to);
        });
    });
    wp.customize('tag_border_color', function (value) {
        value.bind(function (to) {
            $('.featured-tag a').css('border-color', to);
        });
    });
    wp.customize('tag_color', function (value) {
        value.bind(function (to) {
            $('.featured-tag a').css('background-color', to);
        });
    });
    wp.customize('featured_tag_border_radius', function (value) {
        value.bind(function (to) {
            $('.featured-tag a').css('border-radius', to + 'px');
        });
    });
})(jQuery);