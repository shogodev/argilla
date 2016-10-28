/*	
 * jQuery Touch Optimized Sliders "R"Us
 * Vimeo media
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */

(function( $ ) {
	
	var _PLUGIN_ = 'tosrus',
		_MEDIA_	 = 'vimeo';

	var _mediaInitiated = false,
		_c, _d, _e, _f, _g;

	$[ _PLUGIN_ ].media[ _MEDIA_ ] = {

		//	Filter anchors
		filterAnchors: function( $anchor )
		{
			return ( $anchor.attr( 'href' ).toLowerCase().indexOf( 'vimeo.com/' ) > -1 );
		},
	
		//	Create Slides from anchors
		initAnchors: function( $slide, href )
		{
			var id = this._uniqueID();
			href = href.split( 'vimeo.com/' )[ 1 ].split( '?' )[ 0 ] + '?api=1&player_id=' + id;
			$('<iframe id="' + id + '" src="https://player.vimeo.com/video/' + href + '" frameborder="0" allowfullscreen />')
				.appendTo( $slide );

			initVideo.call( this, $slide );
		},

		//	Filter slides
		filterSlides: function( $slide )
		{
			if ( $slide.is( 'iframe' ) && $slide.attr( 'src' ) )
			{			
				return ( $slide.attr( 'src' ).toLowerCase().indexOf( 'vimeo.com/video/' ) > -1 );
			}
			return false;
		},

		//	Create slides from existing content
		initSlides: function( $slide )
		{
			initVideo.call( this, $slide );
		}
	};
	
	$[ _PLUGIN_ ].defaults.media[ _MEDIA_ ] = {};


	//	Functions
	function initVideo( $s )
	{
		if ( !_mediaInitiated )
		{
			_c = $[ _PLUGIN_ ]._c;
			_d = $[ _PLUGIN_ ]._d;
			_e = $[ _PLUGIN_ ]._e;
			_f = $[ _PLUGIN_ ]._f;
			_g = $[ _PLUGIN_ ]._g;

			_d.add( 'ratio maxWidth maxHeight' );

			_mediaInitiated = true;
		}

		var that = this;

		var $v = $s.children(),
			$a = $s.data( $[ _PLUGIN_ ]._d.anchor ) || $();

		var src = $v.attr( 'src' );

		var ratio 		= $a.data( _d.ratio ) 		|| this.opts[ _MEDIA_ ].ratio,
			maxWidth 	= $a.data( _d.maxWidth ) 	|| this.opts[ _MEDIA_ ].maxWidth,
			maxHeight	= $a.data( _d.maxHeight )	|| this.opts[ _MEDIA_ ].maxHeight;

		$s.removeClass( _c.loading )
			.trigger( _e.loaded )
			.on( _e.loading,
				function( e )
				{
					_f.resizeRatio( $v, $s, maxWidth, maxHeight, ratio );
				}
			);

		this.nodes.$wrpr
			.on( _e.sliding,
				function( e )
				{
				//	commandVideo( 'unload' );
					unloadVideo();
				}
			)
			.on( _e.opening,
			    function( e )
			    {
			        _f.resizeRatio( $v, $s, maxWidth, maxHeight, ratio );
			    }
			)
			.on( _e.closing,
				function( e )
				{
				//	commandVideo( 'pause' );
					unloadVideo();
				}
			);


		_g.$wndw
			.on( _e.resize,
				function( e )
				{
					_f.resizeRatio( $v, $s, maxWidth, maxHeight, ratio );
				}
			);

		function unloadVideo()
		{
			if ( $v.length )
			{
				$v.attr( 'src', '' );
				$v.attr( 'src', src );
			}
		}
		//	Can't get this to work anymore...
		function commandVideo( fn )
		{
			if ( $v.length )
			{
				$v[ 0 ].contentWindow.postMessage( '{ "method": "' + fn + '" }', '*' );
			}
		}
	}


	//	Defaults
	$[ _PLUGIN_ ].defaults[ _MEDIA_ ] = {
		ratio		: 16 / 9,
		maxWidth	: false,
		maxHeight	: false
	};

	
})( jQuery );