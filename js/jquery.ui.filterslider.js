$.widget('argilla.filterSlider', {

  controls : {},

  options : {
    ajaxAction : 'getAmount',
    ajaxUrl    : document.location.href,
    ranges     : [0, 100000, 0, 100000],
    controls   : {
      hiddenInput    : '#filter-price-input',
      minInput       : '#filter-price-min',
      maxInput       : '#filter-price-max',
      tooltip        : '#filter-price-tooltip',
      tooltipButton  : '#filter-tooltip-button',
      tooltipCounter : '#filter-tooltip-counter',
      filterButton   : '#filter-submit'
    },
    keyPressDelay : 600,
    tooltipDelay : 3000
  },

  timers : {},

  _create: function() {

    var options = this.options;
    var widget = this;

    for(var i in options.controls)
      if( options.controls.hasOwnProperty(i) )
        this.controls[i] = $(options.controls[i]);

    this.element.slider({
      range: true, step: 100,
      min: this.options.ranges[0], max: this.options.ranges[1], values: [this.options.ranges[2], this.options.ranges[3]],
      slide: function(event, ui){$.proxy(widget._slide(ui), widget)},
      stop: function(event, ui){$.proxy(widget._stopSlide(ui), widget)}
    });

    this.controls.tooltipButton.on('click', function(e) {
      e.preventDefault();
      var input = widget.controls.hiddenInput;
      input.val(input.data('value')).change();
    });

    this.controls.filterButton.on('click', function(e) {
      e.preventDefault();
      widget.controls.tooltipButton.click();
    });

    this.controls.minInput.on('change', function(e) {
      widget._setSliderValue();
    });

    this.controls.maxInput.on('change', function(e) {
      widget._setSliderValue();
    });

    this.controls.minInput.on('keyup', function(e) {
      widget._startTimer(
        'minInputKeyPress',
        function(){
          widget._setSliderValue();
        },
        widget.options.keyPressDelay
      );
    });

    this.controls.maxInput.on('keyup', function(e) {
      widget._startTimer(
        'maxInputKeyPress',
        function(){
          widget._setSliderValue();
        },
        widget.options.keyPressDelay
      );
    });
  },

  /**
   * @param ui
   * @private
   */
  _slide : function(ui) {
    this.controls.minInput.val(ui.values[0]);
    this.controls.maxInput.val(ui.values[1]);
  },

  _stopSlide : function() {
    var widget = this;
    var minInput = widget.controls.minInput,
      maxInput = widget.controls.maxInput,
      hiddenInput = widget.controls.hiddenInput;

    var value = parseInt(minInput.val()) + '-' + parseInt(maxInput.val()),
      data = {'action' : widget.options.ajaxAction, 'price' : value};

    hiddenInput.data('value', value);
    $.post(this.options.ajaxUrl, data, function(response){$.proxy(widget._slideCallback(response), widget)}, 'json');
  },

  _setSliderValue : function() {
    this._stopTimer('minInputKeyPress');
    this._stopTimer('maxInputKeyPress');

    var minInput = this.controls.minInput,
      maxInput = this.controls.maxInput;

    if( minInput.val() == ''|| maxInput.val() == '' )
      return;

    if ( !isNaN(minInput.val()) && !isNaN(maxInput.val()) ) {
      if ( minInput.val() > maxInput.val() ) return;
      this.element.slider('values', 0, minInput.val() );
      this.element.slider('values', 1, maxInput.val() );
      this._stopSlide();
    }
  },

  /**
   * @param response
   * @private
   */
  _slideCallback : function(response) {
    var tooltip = this.controls.tooltip;
    var toggle = response && response['amount'] > 0;

    this.controls.filterButton.toggle(toggle);
    this.controls.tooltipButton.toggle(toggle);
    this.controls.tooltipCounter.html(response['amount']);

    var self = this;
    tooltip.stop(true, true).fadeIn(function(){
      self._startTimer(
        'tooltipTimer',
        function() {
          tooltip.fadeOut();
          self._stopTimer('tooltipTimer');
        },
        self.options.tooltipDelay
      );
    });
  },

  _startTimer : function(timerIndex, callback, delay) {
    this._stopTimer(timerIndex);
    this.timers[timerIndex] = setTimeout(callback, delay);
  },
  _stopTimer : function(timerIndex) {
    if( this.timers[timerIndex] !== undefined )
    {
      clearTimeout(this.timers[timerIndex]);
      delete this.timers[timerIndex];
    }
  },

  destroy: function() {
    $.Widget.prototype.destroy.call(this);
  }
});