/*	
 * jQuery Touch Optimized Sliders "R"Us
 * Images media
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */

(function( $ ) {
	
	var _PLUGIN_ = 'tosrus',
		_MEDIA_	 = 'image';

	$[ _PLUGIN_ ].media[ _MEDIA_ ] = {

		//	Filter anchors
		filterAnchors: function( $anchor )
		{
			return ( $.inArray( $anchor.attr( 'href' ).toLowerCase().split( '.' ).pop().split( '?' )[ 0 ], [ 'jpg', 'jpe', 'jpeg', 'gif', 'png' ] ) > -1 );
		},
		
		//	Create Slides from anchors
		initAnchors: function( $slide, href )
		{
			$('<img border="0" />')
				.on( $[ _PLUGIN_ ]._e.load,
					function( e )
					{
						e.stopPropagation();
						$slide.removeClass( $[ _PLUGIN_ ]._c.loading )
							.trigger( $[ _PLUGIN_ ]._e.loaded );
					}
				)
				.appendTo( $slide )
				.attr( 'src', href );
		},

		//	Filter slides
		filterSlides: function( $slide )
		{
			return $slide.is( 'img' );
		},

		//	Create slides from existing content
		initSlides: function( $slide ) {}
	};
	
	$[ _PLUGIN_ ].defaults.media[ _MEDIA_ ] = {};
	
})( jQuery );