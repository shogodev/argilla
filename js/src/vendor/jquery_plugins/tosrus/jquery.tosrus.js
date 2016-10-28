/*
 *	jQuery Touch Optimized Sliders "R"Us 2.5.0
 *
 *	Copyright (c) Fred Heusschen
 *	www.frebsite.nl
 *
 *	Plugin website:
 *	tosrus.frebsite.nl
 *
 *	Licensed under the MIT license.
 *	http://en.wikipedia.org/wiki/MIT_License
 */



(function( $ ) {

	var _PLUGIN_	= 'tosrus',
		_ABBR_		= 'tos',
		_VERSION_	= '2.5.0';


	//	Plugin already excists
	if ( $[ _PLUGIN_ ] )
	{
		return;
	}


	//	Global variables
	var _c = {}, _d = {}, _e = {}, _f = {}, _g = {};


	/*
		Class
	*/
	$[ _PLUGIN_ ] = function( $node, opts, conf )
	{
		this.$node	= $node;
		this.opts	= opts;
		this.conf	= conf;

		this.vars	= {};
		this.nodes	= {};
		this.slides	= {};

		this._init();

		return this;
	};
	$[ _PLUGIN_ ].prototype = {

		//	Initialize the plugin
		_init: function()
		{
			var that = this;

			this._complementOptions();
			this.vars.fixed = ( this.opts.wrapper.target == 'window' );

			//	Add markup
			this.nodes.$wrpr = $('<div class="' + _c.wrapper + '" />');
			this.nodes.$sldr = $('<div class="' + _c.slider + '" />').appendTo( this.nodes.$wrpr );

			this.nodes.$wrpr
				.addClass( this.vars.fixed ? _c.fixed : _c.inline )
				.addClass( _c( 'fx-' + this.opts.effect ) )
				.addClass( _c( this.opts.slides.scale ) )
				.addClass( this.opts.wrapper.classes );

			//	Bind events
			this.nodes.$wrpr

				//	Custom events
				.on( _e.open + ' ' + _e.close + ' ' + _e.prev + ' ' + _e.next + ' ' + _e.slideTo,
					function( e )
					{
						var args = Array.prototype.slice.call( arguments );
						var e = args.shift(),
							t = e.type;

						e.stopPropagation();

						if ( typeof that[ t ] == 'function' )
						{
							that[ t ].apply( that, args );
						}
					}
				)

				//	Callback events
				.on( _e.opening + ' ' + _e.closing + ' ' + _e.sliding + ' ' + _e.loading + ' ' + _e.loaded,
					function( e )
					{
						e.stopPropagation();
					}
				)

				//	Toggle UI
				.on( _e.click,
					function( e )
					{
						e.stopPropagation();

						switch ( that.opts.wrapper.onClick )
						{
							case 'toggleUI':
								that.nodes.$wrpr.toggleClass( _c.hover );
								break;

							case 'close':
								if ( !$(e.target).is( 'img' ) )
								{
									that.close();
								}
								break;
						}
					}
				);

/*
			//	Prevent pinching if opened
			if ( $.fn.hammer && $[ _PLUGIN_ ].support.touch )
			{
				this.nodes.$wrpr
					.hammer()
					.on( _e.pinch,
						function( e )
						{
							if ( _g.$body.hasClass( _c.opened ) )
							{
								e.gesture.preventDefault();
								e.stopPropagation();
							}
						}
					);
			}
*/

			//	Nodes
			this.nodes.$anchors = this._initAnchors();
			this.nodes.$slides  = this._initSlides();

			//	Slides
			this.slides.total	= this.nodes.$slides.length;
			this.slides.visible	= this.opts.slides.visible;
			this.slides.index	= 0;

			//	Vars
			this.vars.opened	= true;


			//	Init addons
			for ( var a = 0; a < $[ _PLUGIN_ ].addons.length; a++ )
			{
				if ( $.isFunction( this[ '_addon_' + $[ _PLUGIN_ ].addons[ a ] ] ) )
				{
					this[ '_addon_' + $[ _PLUGIN_ ].addons[ a ] ]();
				}
			}
			for ( var u = 0; u < $[ _PLUGIN_ ].ui.length; u++ )
			{
				if ( this.nodes.$wrpr.find( '.' + _c[ $[ _PLUGIN_ ].ui[ u ] ] ).length )
				{
					this.nodes.$wrpr.addClass( _c( 'has-' + $[ _PLUGIN_ ].ui[ u ] ) );
				}
			}


			//	Prevent closing when clicking on UI elements
			if ( this.opts.wrapper.onClick == 'close' )
			{
				this.nodes.$uibg || $()
					.add( this.nodes.$capt || $() )
					.add( this.nodes.$pagr || $() )
					.on( _e.click,
						function( e )
						{
							e.stopPropagation();
						}
					);
			}


			//	Start
			if ( this.vars.fixed )
			{
				this.nodes.$wrpr.appendTo( _g.$body );
				this.close( true );
			}
			else
			{
				this.nodes.$wrpr.appendTo( this.opts.wrapper.target );

				if ( this.opts.show )
				{
					this.vars.opened = false;
					this.open( 0, true );
				}
				else
				{
					this.close( true );
				}
			}
		},


		//	Open method, opens the gallery and slides to the designated slide
		open: function( index, direct )
		{
			var that = this;

			if ( !this.vars.opened )
			{
				if ( this.vars.fixed )
				{
					_g.scrollPosition = _g.$wndw.scrollTop();
					_g.$body.addClass( _c.opened );
					_f.setViewportScale();
				}

				if ( direct )
				{
					this.nodes.$wrpr
						.addClass( _c.opening )
						.trigger( _e.opening, [ index, direct ] );
				}
				else
				{
					setTimeout(
						function()
						{
							that.nodes.$wrpr
								.addClass( _c.opening )
								.trigger( _e.opening, [ index, direct ] );
						}, 5
					);
				}

				this.nodes.$wrpr
					.addClass( _c.hover )
					.addClass( _c.opened );
			}

			this.vars.opened = true;
			this._loadContents();

			//	Slide to given slide
			if ( $.isNumeric( index ) )
			{
				direct = ( direct || !this.vars.opened );
				this.slideTo( index, direct );
			}
		},


		//	Close method, closes the gallery
		close: function( direct )
		{
			if ( this.vars.opened )
			{
				if ( this.vars.fixed )
				{
					_g.$body.removeClass( _c.opened );
				}

				if ( direct )
				{
					this.nodes.$wrpr.removeClass( _c.opened );
				}
				else
				{
					_f.transitionend( this.nodes.$wrpr,
						function()
						{
							$(this).removeClass( _c.opened );
						}, this.conf.transitionDuration
					);
				}

				//	Close + Callback event
				this.nodes.$wrpr
					.removeClass( _c.hover )
					.removeClass( _c.opening )
					.trigger( _e.closing, [ this.slides.index, direct ] );
			}
			this.vars.opened = false;
		},


		//	Prev method, slides to the previous set of slides
		prev: function( slides, direct )
		{
			if ( !$.isNumeric( slides ) )
			{
				slides = this.opts.slides.slide;
			}
			this.slideTo( this.slides.index - slides, direct );

		},


		//	Next method, slides to the next set of slides
		next: function( slides, direct )
		{
			if ( !$.isNumeric( slides ) )
			{
				slides = this.opts.slides.slide;
			}
			this.slideTo( this.slides.index + slides, direct );
		},


		//	SlideTo method, slides to the designated slide
		slideTo: function( index, direct )
		{
			if ( !this.vars.opened )
			{
				return false;
			}
			if ( !$.isNumeric( index ) )
			{
				return false;
			}

			var doSlide = true;

			//	Less then first
			if ( index < 0 )
			{
				var atStart = ( this.slides.index == 0 );

				//	Infinite
				if ( this.opts.infinite )
				{
					if ( atStart )
					{
						index = this.slides.total - this.slides.visible;
					}
					else
					{
						index = 0;
					}
				}
				//	Non-infinite
				else
				{
					index = 0;
					if ( atStart )
					{
						doSlide = false;
					}
				}
			}

			//	More then last
			if ( index + this.slides.visible > this.slides.total )
			{
				var atEnd = ( this.slides.index + this.slides.visible >= this.slides.total );

				//	Infinite
				if ( this.opts.infinite )
				{
					if ( atEnd )
					{
						index = 0;
					}
					else
					{
						index = this.slides.total - this.slides.visible;
					}
				}
				//	Non-infinite
				else
				{
					index = this.slides.total - this.slides.visible;
					if ( atEnd )
					{
						doSlide = false;
					}
				}
			}

			this.slides.index = index;
			this._loadContents();

			if ( doSlide )
			{
				var left = 0 - ( this.slides.index * this.opts.slides.width ) + this.opts.slides.offset;
				if ( this.slides.widthPercentage )
				{
					left += '%';
				}

				if ( direct )
				{
					this.nodes.$sldr.addClass( _c.noanimation );
					_f.transitionend( this.nodes.$sldr,
						function()
						{
							$(this).removeClass( _c.noanimation );
						}, 5
					);
				}

				//	Transition
				for ( var e in $[ _PLUGIN_ ].effects )
				{
					if ( e == this.opts.effect )
					{
						$[ _PLUGIN_ ].effects[ e ].call( this, left, direct );
						break;
					}
				}

				//	Callback event
				this.nodes.$wrpr.trigger( _e.sliding, [ index, direct ] );
			}
		},

		_initAnchors: function()
		{
			var that = this,
				$a = $();

			if ( this.$node.is( 'a' ) )
			{
				for ( var m in $[ _PLUGIN_ ].media )
				{
					$a = $a.add(
						this.$node.filter(
							function()
							{
								if ( that.opts.media[ m ] && that.opts.media[ m ].filterAnchors )
								{
									var result = that.opts.media[ m ].filterAnchors.call( that, $(this) );
									if ( typeof result == 'boolean' )
									{
										return result;
									}
								}
								return $[ _PLUGIN_ ].media[ m ].filterAnchors.call( that, $(this) );
							}
						)
					);
				}
			}
			return $a;
		},
		_initSlides: function()
		{
			this[ this.$node.is( 'a' ) ? '_initSlidesFromAnchors' : '_initSlidesFromContent' ]();
			return this.nodes.$sldr.children().css( 'width', this.opts.slides.width + ( this.slides.widthPercentage ? '%' : 'px' ) );
		},
		_initSlidesFromAnchors: function()
		{
			var that = this;

			this.nodes.$anchors
				.each(
					function( index )
					{
						var $anchor = $(this);

						//	Create the slide
						var $slide = $('<div class="' + _c.slide + ' ' + _c.loading + '" />')
							.data( _d.anchor, $anchor )
							.appendTo( that.nodes.$sldr );

						//	Clicking an achor opens the slide
						$anchor
							.data( _d.slide, $slide )
							.on( _e.click,
								function( e )
								{
									e.preventDefault();
									that.open( index );
								}
							);
					}
				);
		},
		_initSlidesFromContent: function()
		{
			var that = this;

			this.$node
				.children()
				.each(
					function()
					{
						var $slide = $(this);

						$('<div class="' + _c.slide + '" />')
							.append( $slide )
							.appendTo( that.nodes.$sldr );

						//	Init slide content
						for ( var m in $[ _PLUGIN_ ].media )
						{
							var result = null;
							if ( that.opts.media[ m ] && that.opts.media[ m ].filterSlides )
							{
								result = that.opts.media[ m ].filterSlides.call( that, $slide );
							}
							if ( typeof result != 'boolean' )
							{
								result = $[ _PLUGIN_ ].media[ m ].filterSlides.call( that, $slide );
							}
							if ( result )
							{
								$[ _PLUGIN_ ].media[ m ].initSlides.call( that, $slide );
								$slide.parent().addClass( _c( m ) );
								break;
							}
						}
					}
			);
		},

		_loadContents: function()
		{
			var that = this;

			switch ( this.opts.slides.load )
			{
				//	Load all
				case 'all':
					this._loadContent( 0, this.slides.total );
					break;

				//	Load current
				case 'visible':
					this._loadContent( this.slides.index, this.slides.index + this.slides.visible );
					break;

				//	Load current + prev + next
				case 'near-visible':
				default:
					this._loadContent( this.slides.index, this.slides.index + this.slides.visible );
					setTimeout(
						function()
						{
							that._loadContent( that.slides.index - that.slides.visible, that.slides.index );								//	prev
							that._loadContent( that.slides.index + that.slides.visible, that.slides.index + ( that.slides.visible * 2 ) );	//	next
						}, this.conf.transitionDuration
					);
					break;
			}
		},
		_loadContent: function( start, end )
		{
			var that = this;

			this.nodes.$slides
				.slice( start, end )
				.each(
					function()
					{
						var $slide		= $(this),
							contenttype = false;

						if ( $slide.children().length == 0 )
						{
							var $anchor = $slide.data( _d.anchor ),
								content = $anchor.attr( 'href' );

							//	Search for slide content
							for ( var m in $[ _PLUGIN_ ].media )
							{
								var result = null;
								if ( that.opts.media[ m ] && that.opts.media[ m ].filterAnchors )
								{
									result = that.opts.media[ m ].filterAnchors.call( that, $anchor );
								}
								if ( typeof result != 'boolean' )
								{
									result = $[ _PLUGIN_ ].media[ m ].filterAnchors.call( that, $anchor );
								}

								if ( result )
								{
									$[ _PLUGIN_ ].media[ m ].initAnchors.call( that, $slide, content );
									$slide.addClass( _c( m ) );
									break;
								}
							}

							//	Callback event
							$slide.trigger( _e.loading, [ $slide.data( _d.anchor ) ] );
						}
					}
			);
		},

		_complementOptions: function()
		{
			//	Wrapper
			if ( typeof this.opts.wrapper.target == 'undefined' )
			{
				this.opts.wrapper.target = ( this.$node.is( 'a' ) ) ? 'window' : this.$node;
			}
			if ( this.opts.wrapper.target != 'window' )
			{
				if ( typeof this.opts.wrapper.target == 'string' )
				{
					this.opts.wrapper.target = $(this.opts.wrapper.target);
				}
			}

			//	Show
			this.opts.show = _f.complBoolean(  this.opts.show, this.opts.wrapper.target != 'window' );

			//	Slides
			if ( $.isNumeric( this.opts.slides.width ) )
			{
				this.slides.widthPercentage	= false;
				this.opts.slides.visible 	= _f.complNumber( this.opts.slides.visible, 1 );
			}
			else
			{
				var percWidth = ( _f.isPercentage( this.opts.slides.width ) ) ? _f.getPercentage( this.opts.slides.width ) : false;

				this.slides.widthPercentage	= true;
				this.opts.slides.visible 	= _f.complNumber( this.opts.slides.visible, ( percWidth ) ? Math.floor( 100 / percWidth ) : 1 );
				this.opts.slides.width 		= ( percWidth ) ? percWidth : Math.ceil( 100 * 100 / this.opts.slides.visible ) / 100;
			}
			this.opts.slides.slide		=   _f.complNumber( this.opts.slides.slide, this.opts.slides.visible );
			this.opts.slides.offset 	= ( _f.isPercentage( this.opts.slides.offset ) ) ? _f.getPercentage( this.opts.slides.offset ) : _f.complNumber( this.opts.slides.offset, 0 );
		},

		_uniqueID: function()
		{
			if ( !this.__uniqueID )
			{
				this.__uniqueID = 0;
			}
			this.__uniqueID++;
			return _c( 'uid-' + this.__uniqueID );
		}
	};


	/*
		jQuery Plugin
	*/
	$.fn[ _PLUGIN_ ] = function( opts, optsD, optsT, conf )
	{
		//	First time plugin is fired
		if ( !_g.$wndw )
		{
			initPlugin();
		}

		//	Extend options
		opts = $.extend( true, {}, $[ _PLUGIN_ ].defaults, opts );
		opts = $.extend( true, {}, opts, $[ _PLUGIN_ ].support.touch ? optsT : optsD );

		//	Extend configuration
		conf = $.extend( true, {}, $[ _PLUGIN_ ].configuration, conf );

		var clss = new $[ _PLUGIN_ ]( this, opts, conf );

		this.data( _PLUGIN_, clss );
		return clss.nodes.$wrpr;
	};


	/*
		SUPPORT
	*/
	$[ _PLUGIN_ ].support = {
		touch: 'ontouchstart' in window.document || navigator.msMaxTouchPoints
	};


	/*
		Options
	*/
	$[ _PLUGIN_ ].defaults = {
//		show		: null,				//	true for inline slider, false for popup lightbox
		infinite	: false,
		effect		: 'slide',
		wrapper	: {
//			target	: null,				//	"window" for lightbox popup
			classes	: '',
			onClick	: 'toggleUI'		//	"toggleUI", "close" or null
		},
		slides	: {
//			slide	: null,				//	slides.visible
//			width	: null,				//	auto, max 100%
			offset	: 0,
			scale	: 'fit',			//	"fit" or "fill" (for images only)
			load	: 'near-visible',	//	"all", "visible" or "near-visible"
			visible	: 1
		},
		media	: {}
	};

	$[ _PLUGIN_ ].configuration = {
		transitionDuration: 400
	};

	$[ _PLUGIN_ ].constants = {};


	/*
		DEBUG
	*/
	$[ _PLUGIN_ ].debug = function( msg ) {};
	$[ _PLUGIN_ ].deprecated = function( depr, repl )
	{
		if ( typeof console != 'undefined' && typeof console.warn != 'undefined' )
		{
			console.warn( _PLUGIN_ + ': ' + depr + ' is deprecated, use ' + repl + ' instead.' );
		}
	};


	/*
		EFFECTS
	*/
	$[ _PLUGIN_ ].effects = {
		'slide': function( left )
		{
			this.nodes.$sldr.css( 'left', left );
		},
		'fade': function( left )
		{
			_f.transitionend( this.nodes.$sldr,
				function()
				{
					$(this)
						.css( 'left', left )
						.css( 'opacity', 1 );
				}, this.conf.transitionDuration
			);
			this.nodes.$sldr.css( 'opacity', 0 );
		}
	};


	$[ _PLUGIN_ ].version 	= _VERSION_;
	$[ _PLUGIN_ ].media		= {};
	$[ _PLUGIN_ ].addons 	= [];
	$[ _PLUGIN_ ].ui		= [];


	/*
		Private functions
	*/
	function initPlugin()
	{

		//	Classnames, Datanames, Eventnames
		_c = function( c ) { return _ABBR_ + '-' + c; };
		_d = function( d ) { return _ABBR_ + '-' + d; };
		_e = function( e ) { return e + '.' + _ABBR_; };

		$.each( [ _c, _d, _e ],
			function( i, o )
			{
				o.add = function( c )
				{
					c = c.split( ' ' );
					for ( var d in c )
					{
						o[ c[ d ] ] = o( c[ d ] );
					}
				};
			}
		);

		//	Classnames
		_c.add( 'touch desktop scale-1 scale-2 scale-3 wrapper opened opening fixed inline hover slider slide loading noanimation fastanimation' );

		//	Datanames
		_d.add( 'slide anchor' );

		//	Eventnames
		_e.add( 'open opening close closing prev next slideTo sliding click pinch scroll resize orientationchange load loading loaded transitionend webkitTransitionEnd' );

		//	Functions
		_f = {
			complObject: function( option, defaultVal )
			{
				if ( !$.isPlainObject( option ) )
				{
					option = defaultVal;
				}
				return option;
			},
			complBoolean: function( option, defaultVal )
			{
				if ( typeof option != 'boolean' )
				{
					option = defaultVal;
				}
				return option;
			},
			complNumber: function( option, defaultVal )
			{
				if ( !$.isNumeric( option ) )
				{
					option = defaultVal;
				}
				return option;
			},
			complString: function( option, defaultVal )
			{
				if ( typeof option != 'string' )
				{
					option = defaultVal;
				}
				return option;
			},
			isPercentage: function( value )
			{
				return ( typeof value == 'string' && value.slice( -1 ) == '%' );
				{
					value = parseInt( value.slice( 0, -1 ) );
				}
				return !isNaN( value );
			},
			getPercentage: function( value )
			{
				return parseInt( value.slice( 0, -1 ) );
			},
			resizeRatio: function( $i, $o, maxWidth, maxHeight, ratio )
			{
				if ( $o.is( ':visible' ) )
				{
					var _w = $o.width(),
						_h = $o.height();

					if ( maxWidth && _w > maxWidth )
					{
						_w = maxWidth;
					}
					if ( maxHeight && _h > maxHeight )
					{
						_h = maxHeight;
					}

					if ( _w / _h < ratio )
					{
						_h = _w / ratio;
					}
					else
					{
						_w = _h * ratio;
					}
					$i.width( _w ).height( _h );
				}
			},
			transitionend: function( $e, fn, duration )
	        {
				var _ended = false,
					_fn = function()
					{
						if ( !_ended )
						{
							fn.call( $e[ 0 ] );
						}
						_ended = true;
					};

				$e.one( _e.transitionend, _fn );
				$e.one( _e.webkitTransitionEnd, _fn );
				setTimeout( _fn, duration * 1.1 );
	        },
	        setViewportScale: function()
	        {
	        	if ( _g.viewportScale )
				{
					var scale = _g.viewportScale.getScale();
					if ( typeof scale != 'undefined' )
					{
						scale = 1 / scale;
						_g.$body
							.removeClass( _c[ 'scale-1' ] )
							.removeClass( _c[ 'scale-2' ] )
							.removeClass( _c[ 'scale-3' ] )
							.addClass( _c[ 'scale-' + Math.max( Math.min( Math.round( scale ), 3 ), 1 ) ] );
					}
				}
	        }
		};

		// Global variables
		_g = {
			$wndw	: $(window),
			$html	: $('html'),
			$body	: $('body'),

			scrollPosition			: 0,
			viewportScale			: null,
			viewportScaleInterval	: null
		};


		//	Touch or desktop
		_g.$body.addClass( $[ _PLUGIN_ ].support.touch ? _c.touch : _c.desktop )

		//	Prevent scroling if opened
		_g.$wndw
			.on( _e.scroll,
				function( e )
				{
					if ( _g.$body.hasClass( _c.opened ) )
					{
						window.scrollTo( 0, _g.scrollPosition );
						e.preventDefault();
						e.stopPropagation();
						e.stopImmediatePropagation();
					}
				}
			);

		//	Invert viewport-scale
		if ( !_g.viewportScale && $[ _PLUGIN_ ].support.touch && typeof FlameViewportScale != 'undefined' )
		{
			_g.viewportScale = new FlameViewportScale();
			_f.setViewportScale();
			_g.$wndw
				.on( _e.orientationchange + ' ' + _e.resize,
					function( e )
					{
						if ( _g.viewportScaleInterval )
						{
							clearTimeout( _g.viewportScaleInterval );
							_g.viewportScaleInterval = null;
						}
						_g.viewportScaleInterval = setTimeout(
							function()
							{
								_f.setViewportScale();
							}, 500
						);
					}
				);
		}


		//	Add to plugin
		$[ _PLUGIN_ ]._c = _c;
		$[ _PLUGIN_ ]._d = _d;
		$[ _PLUGIN_ ]._e = _e;
		$[ _PLUGIN_ ]._f = _f;
		$[ _PLUGIN_ ]._g = _g;
	};

})( jQuery );