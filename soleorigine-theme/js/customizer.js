/**
 * Customizer JavaScript for SoleOrigine theme
 *
 * Handles live preview of customizer settings
 *
 * @package SoleOrigine
 */

( function( $ ) {
    'use strict';

    // Site title
    wp.customize( 'blogname', function( value ) {
        value.bind( function( to ) {
            $( '.site-title a' ).text( to );
        } );
    } );

    // Site description
    wp.customize( 'blogdescription', function( value ) {
        value.bind( function( to ) {
            $( '.site-description' ).text( to );
        } );
    } );

    // Header text color
    wp.customize( 'header_textcolor', function( value ) {
        value.bind( function( to ) {
            if ( 'blank' === to ) {
                $( '.site-title, .site-description' ).css( {
                    'clip': 'rect(1px, 1px, 1px, 1px)',
                    'position': 'absolute'
                } );
            } else {
                $( '.site-title, .site-description' ).css( {
                    'clip': 'auto',
                    'position': 'relative'
                } );
                $( '.site-title a, .site-description' ).css( 'color', to );
            }
        } );
    } );

    // Hero section
    wp.customize( 'hero_title', function( value ) {
        value.bind( function( to ) {
            $( '.hero__title' ).text( to );
        } );
    } );

    wp.customize( 'hero_subtitle', function( value ) {
        value.bind( function( to ) {
            $( '.hero__subtitle' ).text( to );
        } );
    } );

    wp.customize( 'hero_background', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.hero__background' ).attr( 'src', to );
            }
        } );
    } );

    // About section
    wp.customize( 'about_title', function( value ) {
        value.bind( function( to ) {
            $( '.about-content__title' ).text( to );
        } );
    } );

    wp.customize( 'about_subtitle', function( value ) {
        value.bind( function( to ) {
            $( '.about-content__subtitle' ).text( to );
        } );
    } );

    wp.customize( 'about_text', function( value ) {
        value.bind( function( to ) {
            $( '.about-content__text' ).text( to );
        } );
    } );

    wp.customize( 'about_image', function( value ) {
        value.bind( function( to ) {
            if ( to ) {
                $( '.about-image img' ).attr( 'src', to );
            }
        } );
    } );

} )( jQuery );
