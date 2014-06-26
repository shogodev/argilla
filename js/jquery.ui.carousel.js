/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */

;$.widget('argilla.panelCarousel', {

  options : {
    controls : {
      container : '.carousel-container',
      buttonPrev: '.carousel-prev',
      buttonNext: '.carousel-next'
    },
    items : 4,
    scrollItems : 1
  },

  _create: function() {
    var widget = this;
    var options = widget.options;
    var controls = options.controls;

    controls.container = widget.element.find(controls.container);
    controls.container.jcarousel({
      'animation' : 'fast'
    });

    controls.buttonPrev = widget.element.find(controls.buttonPrev);
    controls.buttonNext = widget.element.find(controls.buttonNext);

    controls.buttonPrev.on('click', function(e) {
      e.preventDefault();
      controls.container.jcarousel('scroll', '-=' + options.scrollItems);
    });

    controls.buttonNext.on('click', function(e) {
      e.preventDefault();
      controls.container.jcarousel('scroll', '+=' + options.scrollItems);
    });

    controls.container.on('jcarousel:reloadend', function(){
      widget._indexing();
    });

    controls.container.on('jcarousel:scrollend', function(){
      widget._updateControls();
    });

    widget._indexing();
    widget._updateControls();
  },

  _updateControls : function() {
    var controls = this.options.controls;

    if( controls.container.jcarousel('items').length <= this.options.items )
    {
      controls.buttonPrev.hide();
      controls.buttonNext.hide();
    }
    else
    {
      var currentIndex = controls.container.jcarousel('target').data('index');
      if( currentIndex == 0 )
      {
        controls.buttonPrev.hide();
        controls.buttonNext.show();
      }
      else if( currentIndex >= controls.container.jcarousel('items').length - this.options.items )
      {
        controls.buttonPrev.show();
        controls.buttonNext.hide();
      }
      else
      {
        controls.buttonPrev.show();
        controls.buttonNext.show();
      }
    }
  },

  _indexing : function() {
    var counter = 0;
    this.options.controls.container.jcarousel('items').each(function(){
      $(this).data('index', counter++);
    });
  },

  update : function()
  {
    this.options.controls.container.jcarousel('reload');
    this._updateControls();
  },

  getSelector : function() {
    return this.options.controls.container.selector;
  },

  destroy: function() {
    $.Widget.prototype.destroy.call(this);
  }
});