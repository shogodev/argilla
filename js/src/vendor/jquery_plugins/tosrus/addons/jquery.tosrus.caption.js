/*	
 *	jQuery Touch Optimized Sliders "R"Us
 *	Caption addon
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */
 
 (function( $ ) {
 
 	var _PLUGIN_ = 'tosrus',
		_ADDON_  = 'caption';

	var _addonInitiated = false,
		_c, _d, _e, _f, _g;

	$[ _PLUGIN_ ].prototype[ '_addon_' + _ADDON_ ] = function()
	{		
		if ( !_addonInitiated )
		{
			_c = $[ _PLUGIN_ ]._c;
			_d = $[ _PLUGIN_ ]._d;
			_e = $[ _PLUGIN_ ]._e;
			_f = $[ _PLUGIN_ ]._f;
			_g = $[ _PLUGIN_ ]._g;

			_c.add( 'caption uibg' );
			_d.add( 'caption' );

			_addonInitiated = true;
		}

		var that = this,
			capt = this.opts[ _ADDON_ ];


		if ( capt.add )
		{

			capt.attributes = capt.attributes || [];

			if ( typeof capt.target == 'string' )
			{
				capt.target = $(capt.target);
			}
			if ( capt.target instanceof $ )
			{
				this.nodes.$capt = capt.target;
			}
			else
			{
				this.nodes.$capt = $('<div class="' + _c.caption + '" />').appendTo( this.nodes.$wrpr );
				if ( !this.nodes.$uibg )
				{
					this.nodes.$uibg = $('<div class="' + _c.uibg + '" />').prependTo( this.nodes.$wrpr );
				}
			}
			for ( var c = 0, l = this.slides.visible; c < l; c++ )
			{
				$('<div class="' + _c.caption + '-' + c + '" />')
					.css( 'width', this.opts.slides.width + ( ( this.slides.widthPercentage ) ? '%' : 'px' ) )
					.appendTo( this.nodes.$capt );
			}

			this.nodes.$slides
				.each(
					function( index )
					{
						var $slide = $(this),
							$anchor = ( that.vars.fixed )
								? $slide.data( _d.anchor )
								: $slide.children();

						$slide.data( _d.caption, '' );
						for ( var c = 0, l = capt.attributes.length; c < l; c++ )
						{
							var caption = $anchor.attr( capt.attributes[ c ] );
							if ( caption && caption.length )
							{
								$slide.data( _d.caption, caption );
								break;
							}
						}
					}
				);

			this.nodes.$wrpr
				.on( _e.sliding,
					function( e, slide, direct )
					{
						var show = false;
						for ( var c = 0, l = that.slides.visible; c < l; c++ )
						{
							that.nodes.$capt
								.children()
								.eq( c )
								.html( that.nodes.$sldr.children().eq( that.slides.index + c ).data( _d.caption ) || '' );
						}						
					}
				);
		}
	};

	//	Defaults
	$[ _PLUGIN_ ].defaults[ _ADDON_ ] = {
		add			: false,
		target		: null,
		attributes	: [ 'title', 'alt', 'rel' ]
	};

	//	Add to plugin
	$[ _PLUGIN_ ].addons.push( _ADDON_ );
	$[ _PLUGIN_ ].ui.push( 'caption' );


})( jQuery );