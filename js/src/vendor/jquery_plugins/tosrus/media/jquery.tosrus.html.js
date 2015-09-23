/*	
 * jQuery Touch Optimized Sliders "R"Us
 * HTML media
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */

(function( $ ) {
	
	var _PLUGIN_ = 'tosrus',
		_MEDIA_	 = 'html';

	$[ _PLUGIN_ ].media[ _MEDIA_ ] = {

		//	Filter anchors
		filterAnchors: function( $anchor )
		{
			var href = $anchor.attr( 'href' );
			return ( href.slice( 0, 1 ) == '#' && $(href).is( 'div' ) )
		},

		//	Create Slides from anchors
		initAnchors: function( $slide, href )
		{
			$('<div class="' + $[ _PLUGIN_ ]._c( 'html' ) + '" />')
				.append( $(href) )
				.appendTo( $slide );

			$slide.removeClass( $[ _PLUGIN_ ]._c.loading )
				.trigger( $[ _PLUGIN_ ]._e.loaded );
		},

		//	Filter slides
		filterSlides: function( $slide )
		{
			return $slide.is( 'div' );
		},

		//	Create slides from existing content
		initSlides: function( $slide ) {}
	};
	
	$[ _PLUGIN_ ].defaults.media[ _MEDIA_ ] = {};
	
})( jQuery );