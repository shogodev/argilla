/*	
 *	jQuery Touch Optimized Sliders "R"Us
 *	Keys addon
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */
 
 (function( $ ) {
 
 	var _PLUGIN_ = 'tosrus',
		_ADDON_  = 'keys';

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

			_e.add( 'keyup' );

			_addonInitiated = true;
		}

		var that = this,
			keys = this.opts[ _ADDON_ ];

		if ( typeof keys == 'boolean' && keys )
		{
			keys = {
				prev	: true,
				next	: true,
				close	: true
			};
		}
		if ( $.isPlainObject( keys) )
		{
			for ( var k in $[ _PLUGIN_ ].constants[ _ADDON_ ] )
			{
				if ( typeof keys[ k ] == 'boolean' && keys[ k ] )
				{
					keys[ k ] = $[ _PLUGIN_ ].constants[ _ADDON_ ][ k ];
				}
			}

			if ( this.nodes.$slides.length < 2 )
			{
				keys.prev = false;
				keys.next = false;
			}

			$(document)
				.on( _e.keyup,
					function( e )
					{
						if ( that.vars.opened )
						{
							var fn = false;
							switch( e.keyCode )
							{
								case keys.prev:
									fn = _e.prev;
									break;
	
								case keys.next:
									fn = _e.next;
									break;
	
								case keys.close:
									fn = _e.close;
									break;
							}
							if ( fn )
							{
								e.preventDefault();
								e.stopPropagation();
								that.nodes.$wrpr.trigger( fn );
							}
						}
					}
				);
		}

	};

	//	Defaults
	$[ _PLUGIN_ ].defaults[ _ADDON_ ] = false;

	$[ _PLUGIN_ ].constants[ _ADDON_ ] = {
		prev	: 37,
		next	: 39,
		close	: 27
	};

	//	Add to plugin
	$[ _PLUGIN_ ].addons.push( _ADDON_ );


})( jQuery );