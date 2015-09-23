/*	
 *	jQuery Touch Optimized Sliders "R"Us
 *	Buttons addon
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 */
 
 (function( $ ) {
 
 	var _PLUGIN_ = 'tosrus',
		_ADDON_  = 'buttons';

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

			_c.add( 'prev next close disabled' );

			_addonInitiated = true;
		}

		var that = this,
			btns = this.opts[ _ADDON_ ];

		this.nodes.$prev = null;
		this.nodes.$next = null;
		this.nodes.$clse = null;

		if ( typeof btns == 'boolean' || ( typeof btns == 'string' && btns == 'inline' ) )
		{
			btns = {
				prev: btns,
				next: btns
			};
		}
		if ( typeof btns.close == 'undefined' )
		{
 			btns.close = this.vars.fixed;
		}
		if ( this.nodes.$slides.length < 2 )
		{
			btns.prev = false;
			btns.next = false;
		}

		$.each(
			{
				'prev'	: 'prev',
				'next'	: 'next',
				'close'	: 'clse'
			},
			function( btn, value )
			{
				if ( btns[ btn ] )
				{
					//	Inline buttons
					if ( typeof btns[ btn ] == 'string' && btns[ btn ] == 'inline' )
					{
						if ( that.vars.fixed && btn != 'close' )
						{
							that.nodes.$slides
								.on( _e.loading,
									function( e, $anchor )
									{
										var $btn = createButton( btn, ' ' + _c.inline )[ btn == 'prev' ? 'prependTo' : 'appendTo' ]( this );
										bindEvent( that.nodes.$wrpr, $btn, btn, 1 );
										
										if ( !that.opts.infinite )
										{
											if (( btn == 'prev' && $(this).is( ':first-child' ) ) ||
												( btn == 'next' && $(this).is( ':last-child' ) ) )
											{
												$btn.addClass( _c.disabled );
											}
										}
									}
								);
						}
					}

					//	External buttons
					else
					{
						if ( typeof btns[ btn ] == 'string' )
						{
							btns[ btn ] = $(btns[ btn ]);
						}
						that.nodes[ '$' + value ] = ( btns[ btn ] instanceof $ )
							? btns[ btn ]
							: createButton( btn, '' ).appendTo( that.nodes.$wrpr );

						bindEvent( that.nodes.$wrpr, that.nodes[ '$' + value ], btn, null );
					}
				}
			}
		);

		if ( !this.opts.infinite )
		{
			this.updateButtons();
			this.nodes.$wrpr
				.on( _e.sliding,
					function( e, slide, direct )
					{
						that.updateButtons();
					}
				);
		}
	};

	function createButton( dir, cls )
	{
		return $('<a class="' + _c[ dir ] + '' + cls + '" href="#"><span></span></a>');
	}
	function bindEvent( $wrpr, $btn, dir, slides )
	{
		$btn
			.on( _e.click,
				function( e )
				{
					e.preventDefault();
					e.stopPropagation();
					$wrpr.trigger( _e[ dir ], [ slides ] );
				}
			);
	}

	$[ _PLUGIN_ ].prototype.updateButtons = function()
	{
		if ( this.nodes.$prev )
		{
			this.nodes.$prev[ ( ( this.slides.index < 1 ) ? 'add' : 'remove' ) + 'Class' ]( _c.disabled );
		}
		if ( this.nodes.$next )
		{
			this.nodes.$next[ ( ( this.slides.index >= this.slides.total - this.slides.visible ) ? 'add' : 'remove' ) + 'Class' ]( _c.disabled );
		}
	};

	//	Defaults
	$[ _PLUGIN_ ].defaults[ _ADDON_ ] = {
		prev	: !$[ _PLUGIN_ ].support.touch,
		next	: !$[ _PLUGIN_ ].support.touch
	};

	//	Add to plugin
	$[ _PLUGIN_ ].addons.push( _ADDON_ );
	$[ _PLUGIN_ ].ui.push( 'prev' );
	$[ _PLUGIN_ ].ui.push( 'next' );
	$[ _PLUGIN_ ].ui.push( 'close' );


})( jQuery );