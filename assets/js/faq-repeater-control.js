( function ( api ) {
	'use strict';

	function normalizeItems( value ) {
		if ( ! Array.isArray( value ) ) {
			return [];
		}

		return value.map( function ( item ) {
			return {
				question: item && item.question ? String( item.question ) : '',
				answer: item && item.answer ? String( item.answer ) : '',
			};
		} );
	}

	function makeLabel( text ) {
		var label = document.createElement( 'label' );
		label.className = 'medicare-faq-repeater__field';
		var title = document.createElement( 'span' );
		title.className = 'medicare-faq-repeater__field-label';
		title.textContent = text;
		label.appendChild( title );
		return label;
	}

	function initControl( control ) {
		var container = control.container[ 0 ];
		if ( ! container ) {
			return;
		}

		var repeater = container.querySelector( '.medicare-faq-repeater' );
		if ( ! repeater ) {
			return;
		}

		var list = repeater.querySelector( '.medicare-faq-repeater__items' );
		var addButton = repeater.querySelector( '.medicare-faq-repeater__add' );
		var setting = control.setting || null;
		var initialValue = setting && 'function' === typeof setting ? setting() : null;
		if ( ! Array.isArray( initialValue ) ) {
			try {
				initialValue = JSON.parse( repeater.getAttribute( 'data-items' ) || '[]' );
			} catch ( error ) {
				initialValue = [];
			}
		}
		var items = normalizeItems( initialValue );

		function saveItems() {
			if ( setting && 'function' === typeof setting.set ) {
				setting.set( normalizeItems( items ) );
			}
		}

		function render() {
			list.innerHTML = '';

			if ( ! items.length ) {
				var empty = document.createElement( 'p' );
				empty.className = 'medicare-faq-repeater__empty';
				empty.textContent = 'No FAQ items yet. Add one below.';
				list.appendChild( empty );
			}

			items.forEach( function ( item, index ) {
				var card = document.createElement( 'div' );
				card.className = 'medicare-faq-repeater__item';

				var header = document.createElement( 'div' );
				header.className = 'medicare-faq-repeater__item-header';
				var itemTitle = document.createElement( 'strong' );
				itemTitle.textContent = 'FAQ ' + ( index + 1 );
				var removeButton = document.createElement( 'button' );
				removeButton.type = 'button';
				removeButton.className = 'button-link-delete medicare-faq-repeater__remove';
				removeButton.textContent = 'Remove';
				removeButton.addEventListener( 'click', function () {
					items.splice( index, 1 );
					saveItems();
					render();
				} );
				header.appendChild( itemTitle );
				header.appendChild( removeButton );
				card.appendChild( header );

				var questionLabel = makeLabel( 'Question' );
				var question = document.createElement( 'input' );
				question.type = 'text';
				question.className = 'widefat';
				question.value = item.question;
				question.addEventListener( 'change', function () {
					items[ index ].question = question.value;
					saveItems();
				} );
				questionLabel.appendChild( question );
				card.appendChild( questionLabel );

				var answerLabel = makeLabel( 'Answer' );
				var answer = document.createElement( 'textarea' );
				answer.className = 'widefat';
				answer.rows = 4;
				answer.value = item.answer;
				answer.addEventListener( 'change', function () {
					items[ index ].answer = answer.value;
					saveItems();
				} );
				answerLabel.appendChild( answer );
				card.appendChild( answerLabel );

				list.appendChild( card );
			} );
		}

		addButton.addEventListener( 'click', function () {
			items.push( { question: '', answer: '' } );
			saveItems();
			render();
			var newQuestion = list.lastElementChild && list.lastElementChild.querySelector( 'input' );
			if ( newQuestion ) {
				newQuestion.focus();
			}
		} );

		render();
	}

	api.control( 'medicare_booking_faq_items', initControl );
}( wp.customize ) );

( function ( api ) {
	'use strict';

	function normalizeTestimonials( value ) {
		if ( ! Array.isArray( value ) ) {
			return [];
		}

		return value.map( function ( item ) {
			return {
				name: item && item.name ? String( item.name ) : '',
				date: item && item.date ? String( item.date ) : '',
				source: item && item.source ? String( item.source ) : 'Google',
				rating: item && item.rating ? Math.max( 1, Math.min( 5, parseInt( item.rating, 10 ) || 5 ) ) : 5,
				verified: !! ( item && item.verified ),
				body: item && item.body ? String( item.body ) : '',
			};
		} );
	}

	function makeTestimonialLabel( text ) {
		var label = document.createElement( 'label' );
		label.className = 'medicare-testimonials-repeater__field';
		var title = document.createElement( 'span' );
		title.className = 'medicare-testimonials-repeater__field-label';
		title.textContent = text;
		label.appendChild( title );
		return label;
	}

	function initTestimonialsControl( control ) {
		var container = control.container[ 0 ];
		if ( ! container ) {
			return;
		}

		var repeater = container.querySelector( '.medicare-testimonials-repeater' );
		if ( ! repeater ) {
			return;
		}

		var list = repeater.querySelector( '.medicare-testimonials-repeater__items' );
		var addButton = repeater.querySelector( '.medicare-testimonials-repeater__add' );
		var setting = control.setting || null;
		var initialValue = setting && 'function' === typeof setting ? setting() : null;
		if ( ! Array.isArray( initialValue ) ) {
			try {
				initialValue = JSON.parse( repeater.getAttribute( 'data-items' ) || '[]' );
			} catch ( error ) {
				initialValue = [];
			}
		}
		var testimonials = normalizeTestimonials( initialValue );

		function saveTestimonials() {
			if ( setting && 'function' === typeof setting.set ) {
				setting.set( normalizeTestimonials( testimonials ) );
			}
		}

		function bindField( field, index, key ) {
			field.addEventListener( 'change', function () {
				testimonials[ index ][ key ] = field.type === 'checkbox' ? field.checked : field.value;
				saveTestimonials();
			} );
		}

		function render() {
			list.innerHTML = '';

			if ( ! testimonials.length ) {
				var empty = document.createElement( 'p' );
				empty.className = 'medicare-testimonials-repeater__empty';
				empty.textContent = 'No reviews yet. Add one below.';
				list.appendChild( empty );
			}

			testimonials.forEach( function ( testimonial, index ) {
				var card = document.createElement( 'div' );
				card.className = 'medicare-testimonials-repeater__item';

				var header = document.createElement( 'div' );
				header.className = 'medicare-testimonials-repeater__item-header';
				var itemTitle = document.createElement( 'strong' );
				itemTitle.textContent = 'Review ' + ( index + 1 );
				var removeButton = document.createElement( 'button' );
				removeButton.type = 'button';
				removeButton.className = 'button-link-delete medicare-testimonials-repeater__remove';
				removeButton.textContent = 'Remove';
				removeButton.addEventListener( 'click', function () {
					testimonials.splice( index, 1 );
					saveTestimonials();
					render();
				} );
				header.appendChild( itemTitle );
				header.appendChild( removeButton );
				card.appendChild( header );

				[ [ 'Reviewer name', 'name' ], [ 'Date label', 'date' ], [ 'Review source', 'source' ] ].forEach( function ( field ) {
					var fieldLabel = makeTestimonialLabel( field[ 0 ] );
					var input = document.createElement( 'input' );
					input.type = 'text';
					input.className = 'widefat';
					input.value = testimonial[ field[ 1 ] ];
					bindField( input, index, field[ 1 ] );
					fieldLabel.appendChild( input );
					card.appendChild( fieldLabel );
				} );

				var ratingLabel = makeTestimonialLabel( 'Star rating (1-5)' );
				var rating = document.createElement( 'input' );
				rating.type = 'number';
				rating.min = '1';
				rating.max = '5';
				rating.step = '1';
				rating.className = 'small-text';
				rating.value = testimonial.rating;
				bindField( rating, index, 'rating' );
				ratingLabel.appendChild( rating );
				card.appendChild( ratingLabel );

				var bodyLabel = makeTestimonialLabel( 'Review text' );
				var body = document.createElement( 'textarea' );
				body.className = 'widefat';
				body.rows = 4;
				body.value = testimonial.body;
				bindField( body, index, 'body' );
				bodyLabel.appendChild( body );
				card.appendChild( bodyLabel );

				var verifiedLabel = document.createElement( 'label' );
				verifiedLabel.className = 'medicare-testimonials-repeater__checkbox';
				var verified = document.createElement( 'input' );
				verified.type = 'checkbox';
				verified.checked = testimonial.verified;
				bindField( verified, index, 'verified' );
				verifiedLabel.appendChild( verified );
				verifiedLabel.appendChild( document.createTextNode( ' Show verified badge' ) );
				card.appendChild( verifiedLabel );

				list.appendChild( card );
			} );
		}

		addButton.addEventListener( 'click', function () {
			testimonials.push( { name: '', date: '', source: 'Google', rating: 5, verified: false, body: '' } );
			saveTestimonials();
			render();
			var newName = list.lastElementChild && list.lastElementChild.querySelector( 'input[type="text"]' );
			if ( newName ) {
				newName.focus();
			}
		} );

		render();
	}

	api.control( 'medicare_locksmith_testimonial_items', initTestimonialsControl );
}( wp.customize ) );

( function ( api ) {
	'use strict';

	function normalizeRows( value ) {
		if ( ! Array.isArray( value ) ) {
			return [];
		}

		return value.map( function ( row ) {
			return {
				service: row && row.service ? String( row.service ) : '',
				typical: row && row.typical ? String( row.typical ) : '',
				project: row && row.project ? String( row.project ) : '',
			};
		} );
	}

	function makePricingLabel( text ) {
		var label = document.createElement( 'label' );
		label.className = 'medicare-pricing-repeater__field';
		var title = document.createElement( 'span' );
		title.className = 'medicare-pricing-repeater__field-label';
		title.textContent = text;
		label.appendChild( title );
		return label;
	}

	function initPricingControl( control ) {
		var container = control.container[ 0 ];
		if ( ! container ) {
			return;
		}

		var repeater = container.querySelector( '.medicare-pricing-repeater' );
		if ( ! repeater ) {
			return;
		}

		var list = repeater.querySelector( '.medicare-pricing-repeater__items' );
		var addButton = repeater.querySelector( '.medicare-pricing-repeater__add' );
		var setting = control.setting || null;
		var initialValue = setting && 'function' === typeof setting ? setting() : null;
		if ( ! Array.isArray( initialValue ) ) {
			try {
				initialValue = JSON.parse( repeater.getAttribute( 'data-items' ) || '[]' );
			} catch ( error ) {
				initialValue = [];
			}
		}
		var rows = normalizeRows( initialValue );

		function saveRows() {
			if ( setting && 'function' === typeof setting.set ) {
				setting.set( normalizeRows( rows ) );
			}
		}

		function render() {
			list.innerHTML = '';

			if ( ! rows.length ) {
				var empty = document.createElement( 'p' );
				empty.className = 'medicare-pricing-repeater__empty';
				empty.textContent = 'No pricing rows yet. Add one below.';
				list.appendChild( empty );
			}

			rows.forEach( function ( row, index ) {
				var card = document.createElement( 'div' );
				card.className = 'medicare-pricing-repeater__item';

				var header = document.createElement( 'div' );
				header.className = 'medicare-pricing-repeater__item-header';
				var itemTitle = document.createElement( 'strong' );
				itemTitle.textContent = 'Row ' + ( index + 1 );
				var removeButton = document.createElement( 'button' );
				removeButton.type = 'button';
				removeButton.className = 'button-link-delete medicare-pricing-repeater__remove';
				removeButton.textContent = 'Remove';
				removeButton.addEventListener( 'click', function () {
					rows.splice( index, 1 );
					saveRows();
					render();
				} );
				header.appendChild( itemTitle );
				header.appendChild( removeButton );
				card.appendChild( header );

				[
					[ 'Service name', 'service' ],
					[ 'Typical cost', 'typical' ],
					[ 'Average project cost', 'project' ],
				].forEach( function ( field ) {
					var fieldLabel = makePricingLabel( field[ 0 ] );
					var input = document.createElement( 'input' );
					input.type = 'text';
					input.className = 'widefat';
					input.value = row[ field[ 1 ] ];
					input.addEventListener( 'change', function () {
						rows[ index ][ field[ 1 ] ] = input.value;
						saveRows();
					} );
					fieldLabel.appendChild( input );
					card.appendChild( fieldLabel );
				} );

				list.appendChild( card );
			} );
		}

		addButton.addEventListener( 'click', function () {
			rows.push( { service: '', typical: '', project: '' } );
			saveRows();
			render();
			var newService = list.lastElementChild && list.lastElementChild.querySelector( 'input' );
			if ( newService ) {
				newService.focus();
			}
		} );

		render();
	}

	api.control( 'medicare_locksmith_pricing_rows', initPricingControl );
}( wp.customize ) );
