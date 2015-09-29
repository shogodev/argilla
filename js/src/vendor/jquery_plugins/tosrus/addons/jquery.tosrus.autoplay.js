/*	
 *	jQuery Touch Optimized Sliders "R"Us
 *	Autoplay addon
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */
 
 (function( $ ) {
 
 	var _PLUGIN_ = 'tosrus',
		_ADDON_  = 'autoplay';

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

			_e.add( 'mouseover mouseout' );

			_addonInitiated = true;
		}

		var that = this,
			auto = this.opts[ _ADDON_ ];

		if ( auto.play )
		{
		
			this.opts.infinite = true;

			this.nodes.$wrpr
				.on( _e.sliding,
					function( e )
					{
						that.autoplay();
					}
				);

			if ( auto.pauseOnHover )
			{
				this.nodes.$wrpr
					.on( _e.mouseover,
						function( e )
						{
							that.autostop();
						}
					)
					.on( _e.mouseout,
						function( e )
						{
							that.autoplay();
						}
					);
			}

			this.autoplay();
		}
	};
	
	$[ _PLUGIN_ ].prototype.autoplay = function()
	{
		var that = this;

		this.autostop();
		this.vars.autoplay = setTimeout(
			function()
			{
				that.next();
			}, this.opts[ _ADDON_ ].timeout
		);
	};
	$[ _PLUGIN_ ].prototype.autostop = function()
	{
		if ( this.vars.autoplay )
		{
			clearTimeout( this.vars.autoplay );
		}
	};



	//	Defaults
	$[ _PLUGIN_ ].defaults[ _ADDON_ ] = {
		play			: false,
		timeout			: 4000,
		pauseOnHover	: false
	};

	//	Add to plugin
	$[ _PLUGIN_ ].addons.push( _ADDON_ );


})( jQuery );