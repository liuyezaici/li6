/**
 *   Unslider lite
 *   The size-conscious version of Unslider.
 *   version 2.0
 *   by @idiot and friends
 */
(function($) {
    //  Everything'll break without jQuery.
    //  Rather than throwing an error ourselves, let's
    //  let the browser handle it.
    $.fn.unslider = function(options) {
        //  No need for a special function or any methods,
        //  everything's going to be inline. Nasty but tiny.
        return this.each(function() {
            //  Our elements
            var $me = $(this);
            var $container = $me.children('ul');
            var $slides = $container.children('li');

            //  Our helper variables
            var total = $slides.length;
            var current = 0;

            var opts = $.extend(options, {
                speed: 500,
                arrows: {
                    prev: '«',
                    next: '»'
                },
                activeClass: 'unslider-active'
            });

            //  Set up our slider
            $container.css({ position: 'relative', width: (total * 100) + '%' });
            $slides.css('width', (100 / total) + '%');

            //  The thing that does all the work!
            //  Calculate where we're moving and move it there.
            var move = function(to) {
                if(to >= total) to = 0;
                if(to < 0) to = total - 1;

                //  Don't allow a negative number or one over our slide
                //  length (minus one cause of zero indexes)
                current = to;

                //  And move it
                $container.animate({marginLeft: -(100 * current) + '%'}, opts.speed);

                //  Update the right slide
                $slides.eq(to).addClass(opts.activeClass).siblings().removeClass(opts.activeClass);
            };

            if(opts.arrows) {
                $.each(opts.arrows, function(key, val) {
                    $('<a class="unslider-arrow ' + key + '" />').text(val).appendTo($me).on('click', function() {
                        move(key === 'prev' ? current - 1 : current + 1);
                    });
                });
            }
        });
    };

})(window.jQuery);