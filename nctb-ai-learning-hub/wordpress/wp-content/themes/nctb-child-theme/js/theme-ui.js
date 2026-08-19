/**
 * NCTB theme UI: language toggle, dark mode, and interactive product showcase tabs.
 *
 * @package NCTB\Theme
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

	function initShowcaseTabs() {
		var tabBtns = document.querySelectorAll( '.showcase-tabs-nav .tab-btn' );
		var panels = document.querySelectorAll( '.showcase-tab-panels .showcase-panel' );

		if ( ! tabBtns.length || ! panels.length ) {
			return;
		}

		tabBtns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var targetId = this.getAttribute( 'data-tab' );

				tabBtns.forEach( function ( b ) { b.classList.remove( 'active' ); } );
				panels.forEach( function ( p ) {
					p.style.display = 'none';
					p.classList.remove( 'active' );
				} );

				this.classList.add( 'active' );
				var activePanel = document.getElementById( targetId );
				if ( activePanel ) {
					activePanel.style.display = 'block';
					activePanel.classList.add( 'active' );
				}
			} );
		} );
	}

	function ready( fn ) {
		if ( 'loading' !== document.readyState ) { fn(); } else { document.addEventListener( 'DOMContentLoaded', fn ); }
	}

	ready( function () {
		applyLang( getLang() );
		applyTheme( getTheme() );
		initShowcaseTabs();

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
