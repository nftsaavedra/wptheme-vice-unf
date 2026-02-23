(function($) {
    'use strict';
	
	// Button Split
    if ( document.body.classList.contains('btn--effect-two') || document.body.classList.contains('btn--effect-three') ) {
        document.querySelectorAll('.btn--effect-two .dt-btn .dt-btn-text, .btn--effect-three .dt-btn .dt-btn-text').forEach(button => button.innerHTML = `<span>` + button.textContent.trim().split('').join(`</span><span>`) + '</span>');
    }
	
    //Hide PreLoading
	function site_preloader() {
		if($('.dt_preloader').length){
			$('.dt_preloader').delay(1000).fadeOut(500);
		}
	}

    if ($(".dt_preloader-close").length) {
        $(".dt_preloader-close").on("click", function(){
            $('.dt_preloader').delay(200).fadeOut(500);
        });
    }

    //set animation timing
    var animationDelay = 2500,
        //loading bar effect
        barAnimationDelay = 3800,
        barWaiting = barAnimationDelay - 3000, //3000 is the duration of the transition on the loading bar - set in the scss/css file
        //letters effect
        lettersDelay = 50,
        //type effect
        typeLettersDelay = 150,
        selectionDuration = 500,
        typeAnimationDelay = selectionDuration + 800,
        //clip effect 
        revealDuration = 600,
        revealAnimationDelay = 1500;

    function initHeadline() {
        //insert <i> element for each letter of a changing word
        singleLetters($('.dt_heading.dt_heading_2').find('b'));
        singleLetters($('.dt_heading.dt_heading_3').find('b'));
        singleLetters($('.dt_heading.dt_heading_8').find('b'));
        singleLetters($('.dt_heading.dt_heading_9').find('b'));
        //initialise headline animation
        animateHeadline($('.dt_heading'));
    }

    function singleLetters($words) {
        $words.each(function() {
            var word = $(this),
                letters = word.text().split(''),
                selected = word.hasClass('is_on');
            for (var i in letters) {
                if (word.parents('.dt_heading_3').length > 0) letters[i] = '<em>' + letters[i] + '</em>';
                letters[i] = (selected) ? '<i class="in">' + letters[i] + '</i>' : '<i>' + letters[i] + '</i>';
            }
            var newLetters = letters.join('');
            word.html(newLetters).css('opacity', 1);
        });
    }

    function animateHeadline($headlines) {
        var duration = animationDelay;
        $headlines.each(function() {
            var headline = $(this);

            if (headline.hasClass('dt_heading_4')) {
                duration = barAnimationDelay;
                setTimeout(function() {
                    headline.find('.dt_heading_inner').addClass('is-loading')
                }, barWaiting);
            } else if (headline.hasClass('dt_heading_6')) {
                var spanWrapper = headline.find('.dt_heading_inner'),
                    newWidth = spanWrapper.width() + 10
                spanWrapper.css('width', newWidth);
            } else if (!headline.hasClass('dt_heading_2')) {
                //assign to .dt_heading_inner the width of its longest word
                var words = headline.find('.dt_heading_inner b'),
                    width = 0;
                words.each(function() {
                    var wordWidth = $(this).width();
                    if (wordWidth > width) width = wordWidth;
                });
                headline.find('.dt_heading_inner').css('width', width);
            };

            //trigger animation
            setTimeout(function() {
                hideWord(headline.find('.is_on').eq(0))
            }, duration);
        });
    }

    function hideWord($word) {
        var nextWord = takeNext($word);

        if ($word.parents('.dt_heading').hasClass('dt_heading_2')) {
            var parentSpan = $word.parent('.dt_heading_inner');
            parentSpan.addClass('selected').removeClass('waiting');
            setTimeout(function() {
                parentSpan.removeClass('selected');
                $word.removeClass('is_on').addClass('is_off').children('i').removeClass('in').addClass('out');
            }, selectionDuration);
            setTimeout(function() {
                showWord(nextWord, typeLettersDelay)
            }, typeAnimationDelay);

        } else if ($word.parents('.dt_heading').hasClass('dt_heading_2') || $word.parents('.dt_heading').hasClass('dt_heading_3') || $word.parents('.dt_heading').hasClass('dt_heading_8') || $word.parents('.dt_heading').hasClass('dt_heading_9')) {
            var bool = ($word.children('i').length >= nextWord.children('i').length) ? true : false;
            hideLetter($word.find('i').eq(0), $word, bool, lettersDelay);
            showLetter(nextWord.find('i').eq(0), nextWord, bool, lettersDelay);

        } else if ($word.parents('.dt_heading').hasClass('dt_heading_6')) {
            $word.parents('.dt_heading_inner').animate({
                width: '2px'
            }, revealDuration, function() {
                switchWord($word, nextWord);
                showWord(nextWord);
            });

        } else if ($word.parents('.dt_heading').hasClass('dt_heading_4')) {
            $word.parents('.dt_heading_inner').removeClass('is-loading');
            switchWord($word, nextWord);
            setTimeout(function() {
                hideWord(nextWord)
            }, barAnimationDelay);
            setTimeout(function() {
                $word.parents('.dt_heading_inner').addClass('is-loading')
            }, barWaiting);

        } else {
            switchWord($word, nextWord);
            setTimeout(function() {
                hideWord(nextWord)
            }, animationDelay);
        }
    }

    function showWord($word, $duration) {
        if ($word.parents('.dt_heading').hasClass('dt_heading_2')) {
            showLetter($word.find('i').eq(0), $word, false, $duration);
            $word.addClass('is_on').removeClass('is_off');

        } else if ($word.parents('.dt_heading').hasClass('dt_heading_6')) {
            $word.parents('.dt_heading_inner').animate({
                'width': $word.width() + 10
            }, revealDuration, function() {
                setTimeout(function() {
                    hideWord($word)
                }, revealAnimationDelay);
            });
        }
    }

    function hideLetter($letter, $word, $bool, $duration) {
        $letter.removeClass('in').addClass('out');

        if (!$letter.is(':last-child')) {
            setTimeout(function() {
                hideLetter($letter.next(), $word, $bool, $duration);
            }, $duration);
        } else if ($bool) {
            setTimeout(function() {
                hideWord(takeNext($word))
            }, animationDelay);
        }

        if ($letter.is(':last-child') && $('html').hasClass('no-csstransitions')) {
            var nextWord = takeNext($word);
            switchWord($word, nextWord);
        }
    }

    function showLetter($letter, $word, $bool, $duration) {
        $letter.addClass('in').removeClass('out');

        if (!$letter.is(':last-child')) {
            setTimeout(function() {
                showLetter($letter.next(), $word, $bool, $duration);
            }, $duration);
        } else {
            if ($word.parents('.dt_heading').hasClass('dt_heading_2')) {
                setTimeout(function() {
                    $word.parents('.dt_heading_inner').addClass('waiting');
                }, 200);
            }
            if (!$bool) {
                setTimeout(function() {
                    hideWord($word)
                }, animationDelay)
            }
        }
    }

    function takeNext($word) {
        return (!$word.is(':last-child')) ? $word.next() : $word.parent().children().eq(0);
    }

    function switchWord($oldWord, $newWord) {
        $oldWord.removeClass('is_on').addClass('is_off');
        $newWord.removeClass('is_off').addClass('is_on');
    }
    
    let dtOwlCarousels = $(".dt_owl_carousel");
	if (dtOwlCarousels.length) {
		dtOwlCarousels.each(function () {
			let elm = $(this);
			let options = elm.data("owl-options");
			dtOwlCarousels = elm.owlCarousel(
				"object" === typeof options ? options : JSON.parse(options)
			);
		});
	}

    if ( $(".dt_slider").hasClass("dt_slider--thumbnav") ) {
        function owlAsSliderMainThumb() {
            $('.owl-item').removeClass('prev next');
            var activeSlide = $('.dt_slider .owl-item.active');
            activeSlide.next('.owl-item').addClass('next');
            activeSlide.prev('.owl-item').addClass('prev');
            var nextSlideImg = $('.dt_slider .owl-item.next').find('.dt_slider-item>img').attr('src');
            var prevSlideImg = $('.dt_slider .owl-item.prev').find('.dt_slider-item>img').attr('src');
            $('.dt_slider .owl-nav .owl-prev .imgholder').css({
                backgroundImage: 'url(' + prevSlideImg + ')'
            });
            $('.dt_slider .owl-nav .owl-next .imgholder').css({
                backgroundImage: 'url(' + nextSlideImg + ')'
            });
        }
        owlAsSliderMainThumb();
        $('.dt_slider .dt_owl_carousel').on('translated.owl.carousel', function() {
            owlAsSliderMainThumb();
        });
    }

    /* ScrollAnimations */
	var scrollAnim = $('[data-animation]:not([data-animation-text]), [data-animation-box]');
	scrollAnim.scrollAnimations();

    // Top Up
    if ($('.dt_uptop').length) {
        var progressPath = document.querySelector('.dt_uptop path');
        var pathLength = progressPath.getTotalLength();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
        var updateProgress = function() {
            var scroll = $(window).scrollTop();
            var height = $(document).height() - $(window).height();
            var progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;
        }
        updateProgress();
        $(window).scroll(updateProgress);
        var offset = 50;
        var duration = 550;
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > offset) {
                $('.dt_uptop').addClass('active');
            } else {
                $('.dt_uptop').removeClass('active');
            }
        });
        $('.dt_uptop').on('click', function(event) {
            event.preventDefault();

            jQuery('html, body').animate({
                scrollTop: 0
            }, duration);
            return false;
        });
    }

    // Lightbox
    if($('.dt_lightbox_img').length) {
		$('.dt_lightbox_img').fancybox({
			openEffect  : 'fade',
			closeEffect : 'fade',
			helpers : {
				media : {}
			}
		});
	}

    //Fact Counter + Text Count
	if($('.dt_count_box').length){
		$('.dt_count_box').appear(function(){
	
			var $t = $(this),
				n = $t.find(".dt_count_text").attr("data-stop"),
				r = parseInt($t.find(".dt_count_text").attr("data-speed"), 10);
				
			if (!$t.hasClass("counted")) {
				$t.addClass("counted");
				$({
					countNum: $t.find(".dt_count_text").text()
				}).animate({
					countNum: n
				}, {
					duration: r,
					easing: "linear",
					step: function() {
						$t.find(".dt_count_text").text(Math.floor(this.countNum));
					},
					complete: function() {
						$t.find(".dt_count_text").text(this.countNum);
					}
				});
			}
			
		},{accY: 0});
	}

    if($('.paroller').length){
		$('.dt_image_block:not(.style2, .style3) .paroller .image').paroller({
			  factor: 0.1,            // multiplier for scrolling speed and offset, +- values for direction control  
			  factorLg: 0.1,          // multiplier for scrolling speed and offset if window width is less than 1200px, +- values for direction control  
			  type: 'foreground',     // background, foreground  
			  direction: 'vertical' // vertical, horizontal  
		});
	}

	if($('.paroller-2').length){
		$('.dt_image_block:not(.style2, .style3) .paroller-2 .image').paroller({
			  factor: -0.1,            // multiplier for scrolling speed and offset, +- values for direction control  
			  factorLg: -0.1,          // multiplier for scrolling speed and offset if window width is less than 1200px, +- values for direction control  
			  type: 'foreground',     // background, foreground  
			  direction: 'vertical' // vertical, horizontal  
		});
	}

    //Parallax Scene for Icons
	if($('.parallax-scene-1').length){
		var scene = $('.parallax-scene-1').get(0);
		var parallaxInstance = new Parallax(scene);
    }

	// Sportlight DOM load event
    if ( $('.dt_spotlight').length ) {
        window.addEventListener("DOMContentLoaded", () => {
            const spotlight = document.querySelector('.dt_spotlight');
            let spotlightSize = 'transparent 10px, rgba(3, 4, 21, 0.78) 650px)';
            window.addEventListener('mousemove', e => updateSpotlight(e));
            window.addEventListener('mousedown', e => {
                spotlightSize = 'transparent 10px, rgba(3, 4, 21, 0.78) 500px)';
                updateSpotlight(e);
            });
            window.addEventListener('mouseup', e => {
                spotlightSize = 'transparent 10px, rgba(3, 4, 21, 0.78) 650px)';
                updateSpotlight(e);
            });
            function updateSpotlight(e) {
                if (spotlight) {
                    spotlight.style.backgroundImage = `radial-gradient(circle at ${e.pageX / window.innerWidth * 100}% ${e.pageY / window.innerHeight * 15}%, ${spotlightSize}`;
                }
            }
        });
    }

    
    /* ==========================================================================
    When document is loaded, do
    ========================================================================== */
	
	$(window).on('load', function() {
		site_preloader();
        initHeadline();
	});
    
})(jQuery);