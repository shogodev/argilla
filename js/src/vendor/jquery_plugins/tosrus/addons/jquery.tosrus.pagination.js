/*	
 *	jQuery Touch Optimized Sliders "R"Us
 *	Pagination addon
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */

 (function( $ ) {
 
 	var _PLUGIN_ = 'tosrus',
		_ADDON_  = 'pagination';

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

			_c.add( 'pagination selected uibg bullets thumbnails' );

			_addonInitiated = true;
		}

		var that = this,
			pagr = this.opts[ _ADDON_ ];


		if ( this.nodes.$slides.length < 2 )
		{
			pagr.add = false;
		}

		if ( pagr.add )
		{
			if ( typeof pagr.target == 'string' )
			{
				pagr.target = $(pagr.target);
			}
			if ( pagr.target instanceof $ )
			{
				this.nodes.$pagr = pagr.target;
			}
			else
			{
				this.nodes.$pagr = $('<div class="' + _c.pagination + ' ' + _c[ pagr.type ] + '" />').appendTo( this.nodes.$wrpr );
				if ( !this.nodes.$uibg )
				{
					this.nodes.$uibg = $('<div class="' + _c.uibg + '" />').prependTo( this.nodes.$wrpr );
				}
			}

			if ( typeof pagr.anchorBuilder != 'function' )
			{
				switch( pagr.type )
				{
					case 'thumbnails':
						var pre 	= '<a href="#" style="background-image: url(\'',
							post	= '\');"></a>';

						if ( this.vars.fixed )
						{
							pagr.anchorBuilder = function( index )
							{
								return pre + $(this).data( _d.anchor ).attr( 'href' ) + post;
							};
						}
						else
						{
							pagr.anchorBuilder = function( index )
							{
								return pre + $(this).find( 'img' ).attr( 'src' ) + post;
							};
						}
						break;

					case 'bullets':
					default:
						pagr.anchorBuilder = function( index )
						{
							return '<a href="#"></a>';
						};
						break;
				}
			}

			this.nodes.$slides
				.each(
					function( index )
					{
						$(pagr.anchorBuilder.call( this, index + 1 ) )
							.appendTo( that.nodes.$pagr )
							.on( _e.click,
								function( e )
								{
									e.preventDefault();
									e.stopPropagation();

									that.nodes.$wrpr.trigger( _e.slideTo, [ index ] );
								}
							);
					}
				);
			
			this.updatePagination();
			this.nodes.$wrpr
				.on( _e.sliding,
					function( e, slide, direct )
					{
						that.updatePagination();
					}
				);
		}
	};
	
	$[ _PLUGIN_ ].prototype.updatePagination = function()
	{
		if ( this.nodes.$pagr )
		{
			this.nodes.$pagr
				.children()
				.removeClass( _c.selected )
				.eq( this.slides.index )
				.addClass( _c.selected );
		}
	};

	//	Defaults
	$[ _PLUGIN_ ].defaults[ _ADDON_ ] = {
		add				: false,
		type			: 'bullets',
		target			: null,
		anchorBuilder	: null
	};

	//	Add to plugin
	$[ _PLUGIN_ ].addons.push( _ADDON_ );
	$[ _PLUGIN_ ].ui.push( 'pagination' );
	$[ _PLUGIN_ ].ui.push( 'bullets' );
	$[ _PLUGIN_ ].ui.push( 'thumbnails' );


})( jQuery );