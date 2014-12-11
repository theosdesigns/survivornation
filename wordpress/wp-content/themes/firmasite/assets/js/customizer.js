/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	//Logo
	wp.customize( 'firmasite_settings[logo]', function( value ) {
		value.bind( function( newval ) {
			if(newval){
				var title = $("span.logo-text").html();
				$("a#logo-link").html("<img src=" + newval + " title='" + title + "' />");	
			} else {
				var title = $("a#logo img").attr("title");
				$("a#logo-link").html( '<span class="badge badge-info logo-text">' + title + '</span>' );	
			}
		} );
	} );

	// Layout
	wp.customize( 'firmasite_settings[layout]', function( value ) {
		value.bind( function( newval ) {		
			switch (newval) { 
			case 'content-sidebar': 
				$('div#page').removeClass( "site-only-content" );
				$('div#page').removeClass( "site-only-content-long" );
				$('div#primary').removeClass( "span12 pull-right" );
				$('div#primary').addClass( "span8" );
				$('div#secondary').removeClass( "hide" );
				break; //end content-sidebar

			case 'only-content': 
				$('div#page').addClass( "site-only-content" );
				$('div#page').removeClass( "site-only-content-long" );
				$('div#primary').removeClass( "span8 pull-right" );
				$('div#primary').addClass( "span12" );
				$('div#secondary').addClass( "hide" );
				break; //end only-content 
				
			case 'only-content-long': 
				$('div#page').removeClass( "site-only-content" );
				$('div#page').addClass( "site-only-content-long" );
				$('div#primary').removeClass( "span8 pull-right" );
				$('div#primary').addClass( "span12" );
				$('div#secondary').addClass( "hide" );
				break; //end only-content-long

			case 'sidebar-content': 
				$('div#page').removeClass( "site-only-content" );
				$('div#page').removeClass( "site-only-content-long" );
				$('div#primary').removeClass( "span12" );
				$('div#primary').addClass( "span8 pull-right" );
				$('div#secondary').removeClass( "hide" );
				break; //end sidebar-content

			}		
		} );
	} );
	
	// Alternative Menu Color
	wp.customize( 'firmasite_settings[alternative]', function( value ) {
		value.bind( function( newval ) {
			$('.site-navigation.navbar').toggleClass( "navbar-inverse" );	
		} );
	} );

	// Showcase Remove
	wp.customize( 'firmasite_settings[showcase-remove]', function( value ) {
		value.bind( function( newval ) {
			$('#firmasite-showcase').toggleClass( "hide" );	
		} );
	} );

	// Theme Style
	wp.customize( 'firmasite_settings[style]', function( value ) {
		value.bind( function( newval ) {
			// we got link href
			var href = $('link#bootstrap-css').attr('href');
			// we split it to parts
			var oldstyle = href.split('/');
			// we got style name part
			var oldstylename = oldstyle[oldstyle.length - 2];
			// we replaced old style name with new value			
			href = href.replace(oldstylename,newval);
			// we giving proper class to #page
			$('#page').removeClass( oldstylename + '-theme' );	
			$('#page').addClass(  newval + '-theme' );	

			// we replace style css.. styles_url comes with wp_localize_script
			$('link#bootstrap-css').attr('href', styles_url[newval]+'/bootstrap.min.css');

		} );
	} );

	
	//Google font
	wp.customize( 'firmasite_settings[font]', function( value ) {
		value.bind( function( newval ) {
			if(newval){
				$('head').append('<link href="//fonts.googleapis.com/css?family=' + newval + '&subset=latin,latin-ext" rel="stylesheet" type="text/css">');
				$('head').append('<style type="text/css"> body { font-family: '+ newval + ',sans-serif !important; } </style>');
			} else {
				$('head').append('<style type="text/css"> body { font-family: sans-serif !important; } </style>');		
			}

		} );
	} );
		
} )( jQuery );