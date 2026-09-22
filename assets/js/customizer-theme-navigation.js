( function ( api ) {
	'use strict';

	var childPanels = [
		'medicare_theme_options',
		'medicare_homepage_options',
		'medicare_about_page_options',
		'medicare_contact_page_options'
	];

	function panelElement( panelId ) {
		return document.getElementById( 'accordion-panel-' + panelId );
	}

	function syncPanelVisibility() {
		var expandedPanel = null;

		childPanels.some( function ( panelId ) {
			var element = panelElement( panelId );
			var trigger = element && element.querySelector( '.accordion-trigger' );
			if ( trigger && 'true' === trigger.getAttribute( 'aria-expanded' ) ) {
				expandedPanel = panelId;
				return true;
			}
			return false;
		} );

		childPanels.forEach( function ( panelId ) {
			var element = panelElement( panelId );
			if ( ! element ) {
				return;
			}

			element.style.setProperty( 'display', expandedPanel === panelId ? 'list-item' : 'none', 'important' );
		} );

		var identityPanel = panelElement( 'medicare_theme_identity' );
		if ( identityPanel ) {
			identityPanel.style.setProperty( 'display', expandedPanel ? 'none' : 'list-item', 'important' );
		}
	}

	function openPanel( event ) {
		var panelId = event.currentTarget.getAttribute( 'data-medicare-customizer-panel' );
		var element = panelId ? panelElement( panelId ) : null;
		var trigger = element && element.querySelector( '.accordion-trigger' );

		if ( ! trigger ) {
			return;
		}

		event.preventDefault();
		element.style.setProperty( 'display', 'list-item', 'important' );
		trigger.click();
		window.setTimeout( syncPanelVisibility, 50 );
	}

	function expandNavigationSection() {
		var identityPanel = panelElement( 'medicare_theme_identity' );
		var panelTrigger = identityPanel && identityPanel.querySelector( '.accordion-trigger' );
		var sectionTrigger = identityPanel && identityPanel.querySelector( '#accordion-section-medicare_theme_navigation .accordion-trigger' );

		if ( panelTrigger && sectionTrigger && 'true' === panelTrigger.getAttribute( 'aria-expanded' ) && 'true' !== sectionTrigger.getAttribute( 'aria-expanded' ) ) {
			sectionTrigger.click();
		}
	}

	function initializeNavigation() {
		document.querySelectorAll( '[data-medicare-customizer-panel]' ).forEach( function ( link ) {
			link.addEventListener( 'click', openPanel );
		} );

		var controls = document.getElementById( 'customize-controls' );
		if ( controls && ! controls.getAttribute( 'data-medicare-navigation-observer' ) ) {
			var observer = new MutationObserver( syncPanelVisibility );
			observer.observe( controls, { attributes: true, childList: true, subtree: true, attributeFilter: [ 'aria-expanded' ] } );
			controls.setAttribute( 'data-medicare-navigation-observer', 'true' );
		}

		var identityPanel = panelElement( 'medicare_theme_identity' );
		var identityTrigger = identityPanel && identityPanel.querySelector( '.accordion-trigger' );
		if ( identityTrigger && ! identityTrigger.getAttribute( 'data-medicare-navigation-bound' ) ) {
			identityTrigger.addEventListener( 'click', function () {
				window.setTimeout( expandNavigationSection, 80 );
			} );
			identityTrigger.setAttribute( 'data-medicare-navigation-bound', 'true' );
		}

		syncPanelVisibility();
		expandNavigationSection();
	}

	api.bind( 'ready', initializeNavigation );
	initializeNavigation();
	window.setTimeout( initializeNavigation, 500 );
}( wp.customize ) );
