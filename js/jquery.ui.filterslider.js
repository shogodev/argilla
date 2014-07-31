$.widget('argilla.filterSlider', {

  options : {
    ajaxAction : 'getAmount',
    ajaxUrl    : null,
    ajaxMethod : null,
    ranges     : [0, 100000, 0, 100000, 100],
    controls   : {
      minInput       : '#filter-price-min',
      maxInput       : '#filter-price-max',
      tooltip        : '#filter-price-tooltip',
      tooltipButton  : '#filter-tooltip-button',
      tooltipCounter : '#filter-tooltip-counter',
      filterButton   : '#filter-submit'
    },
    keyPressDelay : 600,
    tooltipDelay : 3000,
    timers : {}
  },

  _create: function() {

    var options = this.options;
    var widget = this;

    for(var i in options.controls)
      if( options.controls.hasOwnProperty(i) )
        options.controls[i] = $(options.controls[i]);

    this.element.slider({
      range: true, step: (this.options.ranges[4] === undefined ? 1 : this.options.ranges[4]),
      min: this.options.ranges[0], max: this.options.ranges[1], values: [this.options.ranges[2], this.options.ranges[3]],
      slide: function(event, ui){$.proxy(widget._slide(ui), widget)},
      stop: function(event, ui){$.proxy(widget._stopSlide(ui), widget)}
    });

    options.controls.tooltipButton.on('click', function(e) {
      e.preventDefault();
      options.controls.filterButton.trigger('click');
    });

    options.controls.filterButton.on('click', function(e) {
      e.preventDefault();
      var input = widget.element.siblings('input');
      input.val(input.data('value')).trigger('change');
    });

    options.controls.minInput.on('change', function(e) {
      widget._setSliderValue();
    });

    options.controls.maxInput.on('change', function(e) {
      widget._setSliderValue();
    });

    options.controls.minInput.on('keyup', function(e) {
      widget._startTimer(
        'minInputKeyPress',
        function(){
          widget._setSliderValue();
        },
        widget.options.keyPressDelay
      );
    });

    options.controls.maxInput.on('keyup', function(e) {
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
    this.options.controls.minInput.val(ui.values[0]);
    this.options.controls.maxInput.val(ui.values[1]);
  },

  _stopSlide : function() {
    var widget = this;
    var minInput = widget.options.controls.minInput,
      maxInput = widget.options.controls.maxInput,
      hiddenInput = widget.element.siblings('input'),
      form = hiddenInput.closest('form');

    var value = parseInt(minInput.val()) + '-' + parseInt(maxInput.val());
    var data = form.serializeArray();
    var ajaxUrl = this.options.ajaxUrl ? this.options.ajaxUrl : form.attr('action');

    for(var i in data)
      if( data.hasOwnProperty(i) )
        if( data[i]['name'] == hiddenInput.attr('name') )
          data[i]['value'] = value;

    data.push({'name' : form.attr('name') + '[submit]', 'value' : 'amount'});
    hiddenInput.data('value', value);

    if( typeof widget.options.ajaxMethod === 'function' )
      widget.options.ajaxMethod(data);
    else
      $.post(ajaxUrl, data, function(response){$.proxy(widget._slideCallback(response), widget)}, 'json');
  },

  _setSliderValue : function() {
    this._stopTimer('minInputKeyPress');
    this._stopTimer('maxInputKeyPress');

    var minInput = this.options.controls.minInput,
      maxInput = this.options.controls.maxInput;

    if( minInput.val() == ''|| maxInput.val() == '' )
      return;

    if ( !isNaN(minInput.val()) && !isNaN(maxInput.val()) ) {
      if ( parseInt(minInput.val()) > parseInt(maxInput.val()) ) return;
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
    var tooltip = this.options.controls.tooltip;
    var toggle = response && response['amount'] > 0;

    this.options.controls.filterButton.toggle(toggle);
    this.options.controls.tooltipButton.toggle(toggle);
    this.options.controls.tooltipCounter.html(response['amount']);

    if( tooltip.length ) {
      var self = this;
      tooltip.stop(true, true).fadeIn(function () {
        self._startTimer(
          'tooltipTimer',
          function () {
            tooltip.fadeOut();
            self._stopTimer('tooltipTimer');
          },
          self.options.tooltipDelay
        );
      });
    }
  },

  _startTimer : function(timerIndex, callback, delay) {
    this._stopTimer(timerIndex);
    this.options.timers[timerIndex] = setTimeout(callback, delay);
  },

  _stopTimer : function(timerIndex) {
    if( this.options.timers[timerIndex] !== undefined ) {
      clearTimeout(this.options.timers[timerIndex]);
      delete this.options.timers[timerIndex];
    }
  },

  destroy: function() {
    $.Widget.prototype.destroy.call(this);
  }
});