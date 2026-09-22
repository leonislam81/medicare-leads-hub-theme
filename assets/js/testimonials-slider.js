( function () {
	'use strict';

	var sliders = document.querySelectorAll( '[data-testimonials-slider]' );

	if ( ! sliders.length ) {
		return;
	}

	Array.prototype.forEach.call( sliders, function ( slider ) {
		var viewport = slider.querySelector( '.locksmith-testimonials-slider__viewport' );
		var track = slider.querySelector( '.locksmith-testimonials-slider__track' );
		var cards = track ? track.querySelectorAll( '.locksmith-testimonials-fallback__card' ) : [];
		var previous = slider.querySelector( '.locksmith-testimonials-slider__arrow--prev' );
		var next = slider.querySelector( '.locksmith-testimonials-slider__arrow--next' );
		var dots = slider.querySelector( '.locksmith-testimonials-slider__dots' );
		var currentPage = 0;
		var visibleCards = 3;
		var pageCount = 1;

		if ( ! viewport || ! track || ! cards.length || ! previous || ! next || ! dots ) {
			return;
		}

		function getVisibleCards() {
			if ( window.innerWidth <= 600 ) {
				return 1;
			}

			if ( window.innerWidth <= 1024 ) {
				return 2;
			}

			return 3;
		}

		function renderDots() {
			dots.innerHTML = '';

			for ( var index = 0; index < pageCount; index += 1 ) {
				var dot = document.createElement( 'button' );
				dot.type = 'button';
				dot.className = 'locksmith-testimonials-slider__dot';
				dot.setAttribute( 'role', 'tab' );
				dot.setAttribute( 'aria-label', 'Show review slide ' + ( index + 1 ) );
				dot.setAttribute( 'aria-selected', index === currentPage ? 'true' : 'false' );
				dot.addEventListener( 'click', function ( event ) {
					currentPage = parseInt( event.currentTarget.getAttribute( 'data-slide' ), 10 ) || 0;
					update();
				} );
				dot.setAttribute( 'data-slide', index );
				if ( index === currentPage ) {
					dot.classList.add( 'is-active' );
				}
				dots.appendChild( dot );
			}

			dots.hidden = pageCount < 2;
		}

		function update() {
			visibleCards = getVisibleCards();
			pageCount = Math.max( 1, Math.ceil( cards.length / visibleCards ) );
			currentPage = Math.min( currentPage, pageCount - 1 );

			var styles = window.getComputedStyle( track );
			var gap = parseFloat( styles.columnGap || styles.gap ) || 24;
			var pageWidth = viewport.getBoundingClientRect().width + gap;
			track.style.transform = 'translate3d(' + ( -1 * currentPage * pageWidth ) + 'px, 0, 0)';
			previous.disabled = currentPage === 0;
			next.disabled = currentPage >= pageCount - 1;
			previous.setAttribute( 'aria-disabled', previous.disabled ? 'true' : 'false' );
			next.setAttribute( 'aria-disabled', next.disabled ? 'true' : 'false' );
			renderDots();
		}

		previous.addEventListener( 'click', function () {
			if ( currentPage > 0 ) {
				currentPage -= 1;
				update();
			}
		} );

		next.addEventListener( 'click', function () {
			if ( currentPage < pageCount - 1 ) {
				currentPage += 1;
				update();
			}
		} );

		window.addEventListener( 'resize', update );
		update();
	} );
}() );
