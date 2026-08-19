/**
 * NCTB theme UI: language toggle (English default ⇄ Bangla) and dark mode.
 *
 * Language toggle swaps the text of any element that provides both
 * data-en and data-bn attributes. English is the default.
 * Preferences persist in localStorage.
 */
( function () {
	'use strict';

	var LANG_KEY = 'nctbLang';
	var THEME_KEY = 'nctbTheme';

	function getLang() {
		try { return localStorage.getItem( LANG_KEY ) === 'bn' ? 'bn' : 'en'; } catch ( e ) { return 'en'; }
	}
	function getTheme() {
		try { return localStorage.getItem( THEME_KEY ) === 'dark' ? 'dark' : 'light'; } catch ( e ) { return 'light'; }
	}

	function applyLang( lang ) {
		var nodes = document.querySelectorAll( '[data-en][data-bn]' );
		nodes.forEach( function ( el ) {
			var val = 'bn' === lang ? el.getAttribute( 'data-bn' ) : el.getAttribute( 'data-en' );
			if ( null !== val ) {
				el.textContent = val;
			}
		} );
		document.documentElement.setAttribute( 'lang', 'bn' === lang ? 'bn' : 'en' );
		document.body.classList.toggle( 'nctb-lang-bn', 'bn' === lang );

		var label = document.getElementById( 'nctb-lang-label' );
		if ( label ) {
			// Show the language you can switch TO.
			label.textContent = 'bn' === lang ? 'English' : 'বাংলা';
		}
	}

	function applyTheme( theme ) {
		document.documentElement.setAttribute( 'data-theme', theme );
		var ico = document.querySelector( '#nctb-theme-toggle .ico' );
		if ( ico ) {
			ico.textContent = 'dark' === theme ? '☀️' : '🌙';
		}
	}

	function ready( fn ) {
		if ( 'loading' !== document.readyState ) { fn(); } else { document.addEventListener( 'DOMContentLoaded', fn ); }
	}

	ready( function () {
		applyLang( getLang() );
		applyTheme( getTheme() );

		var langBtn = document.getElementById( 'nctb-lang-toggle' );
		if ( langBtn ) {
			langBtn.addEventListener( 'click', function () {
				var next = 'bn' === getLang() ? 'en' : 'bn';
				try { localStorage.setItem( LANG_KEY, next ); } catch ( e ) {}
				applyLang( next );
			} );
		}

		var themeBtn = document.getElementById( 'nctb-theme-toggle' );
		if ( themeBtn ) {
			themeBtn.addEventListener( 'click', function () {
				var next = 'dark' === getTheme() ? 'light' : 'dark';
				try { localStorage.setItem( THEME_KEY, next ); } catch ( e ) {}
				applyTheme( next );
			} );
		}
	} );
}() );
