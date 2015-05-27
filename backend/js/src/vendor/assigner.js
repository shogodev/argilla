/**
 * IFrame для привязки контента
 *
 * @type {Object}
 */
var assigner = {
  /**
   * @param element
   * @param inputOptions
   *  - afterAjaxSend функция вызыватся после отправки данных по кнопке кнопки "Выбрать"
   *  - submitUrl url куда отпаравить данные по нажатию кнопки "Выбрать"
   *  - iframeUrl url страницв подгружаемой в iframe  
   *  - updateGridId идентификтор грида который нужно обновить
   *  - addButton показывать кнопку "Выбрать"
   *  - multiSelect множестивенный выбор, true по умолчанию
   *  - selectButtonClickHandler фукция обработчик нажиния на кнопку "Выбрать", function(elements, options){}
   *  - afterLoad фукция вызывается после загрузки контента в iframe
   *  - afterShow фукция вызывается после создантя попапа
   *  - afterClose фукция вызывается после закрытия попапа
   *  - width ширира попапа
   *  - height высота
   *  - left
   *  - top
   *  - marginTop
   *  - marginLeft
   */
  apply: function(element, inputOptions)
  {
    var iframeUrl = $(element).data('iframeurl') ? $(element).data('iframeurl') : inputOptions.iframeUrl;
    var submitUrl = $(element).data('ajaxurl');

    var options = {
      'selectButtonClickHandler' : assigner._selectButtonClickHandler,
      'submitUrl' : submitUrl
    };
    $.extend(options, inputOptions);

    assigner.open(iframeUrl, options);
  },

  /**
   * @param url
   * @param inputOptions
   */
  open: function(url, inputOptions)
  {
    var options = {
      iframeUrl : url,
      width : '',
      height : '',
      addButton : inputOptions && inputOptions.addButton,
      multiSelect : true
    };

    $.extend(options, inputOptions);

    assigner._cretePopup(options);

    $('#main-assign .popup_close').on('click', function(e) {
      e.preventDefault();
      assigner._destroy(options)
      assigner._updateGrid(options);
    });

    $('#main-assign .popup_add').on('click', function(e) {
      e.preventDefault();

      if( options.selectButtonClickHandler ) {
        options.selectButtonClickHandler($('iframe').contents().find('input[type=checkbox].select'), options);
      }

      assigner._destroy(options);
    });

    $('iframe').load(function()
    {
      if( options.afterLoad )
        options.afterLoad();

      if( options.multiSelect == false )
      {
        $('iframe').contents().on('click', '.items .select', function()
        {
          var elements = $('iframe').contents().find('.items .select');
          if( elements.length > 1 )
          {
            elements.prop('checked', false);
            $(this).prop('checked', true);
          }
        });
      }
    });

    if( options.afterShow )
      options.afterShow();
  },

  /**
   * закрывает страницу привязки
   */
  close: function()
  {
    $('#saver').remove();
    $('#main-assign').remove();
  },

  _destroy : function(options) {
    if( options.afterClose )
      options.afterClose();

    assigner.close();
  },

  _selectButtonClickHandler : function(elements, options) {
    var submitUrl = options.submitUrl;
    var afterAjaxSend = options.afterAjaxSend;
    var data = {};

    $(elements).each(function(){
      var id = $(this).attr('id').match(/pk_(\d+)/)[1];
      data[id] = $(this).prop('checked');
    });

    var self = this;
    $.post(submitUrl, {'elements' : data}, function(response) {
      if( self.afterAjaxSend )
        afterAjaxSend(response);
      assigner._updateGrid(options);
    }).fail(function(xhr){ajaxUpdateError(xhr)});
  },

  _getScreenSizes : function() {
      return [$(window).width(), $(window).height(), $(document).width(), $(document).height()];
  },
  
  _cretePopup : function(options) {

    var ie = $.browser.ie;
    var szs = assigner._getScreenSizes();

    $('body').append('<div id="saver"></div>');
    $('#saver').css({
      'height'     : (ie ? szs[3] + 'px' : '100%'),
      'width'      : (ie ? szs[2] + 'px' : '100%'),
      'position'   : (ie ? 'absolute' : 'fixed'),
      'opacity'    : '0.20',
      'top'        : 0,
      'left'       : 0,
      'z-index'    : 1040,
      'background' : '#000000'
    }).on('click', function(){assigner.close();});

    $('body').append('<div id="main-assign" style="display:none; background-color: white;" class="SUI_editor_popup"><div id="main-assign-inner" style="position:relative; background-color: white;"></div></div>')
    $('#main-assign-inner').append('<div id="main-assign-buttons-top" style="position: absolute; left: 0; top : 0; width: 100%; padding: 10px 0; text-align: right; border-top: 0; border-left: 0; border-right: 0;" class="SUI_editor_popup"></div>');
    $('#main-assign-inner').append('<div id="main-assign-buttons-bottom" style="position: absolute; left: 0; bottom : 0; width: 100%; padding: 10px 0; text-align: right; border-bottom: 0; border-left: 0; border-right: 0;" class="SUI_editor_popup"></div>');

    var buttonSelect = '<a href="#" class="btn btn-primary popup_add">Выбрать</a>';
    var buttonClose = '<a href="#" class="btn btn-danger popup_close">Закрыть</a>';

    if( options.addButton )
    {
      $('#main-assign-buttons-top').append(buttonSelect);
      $('#main-assign-buttons-bottom').append(buttonSelect);
    }

    $('#main-assign-buttons-top').append(buttonClose);
    $('#main-assign-buttons-bottom').append(buttonClose);

    $('#main-assign-inner').append('<iframe id="assign-frame"></iframe>');
    $('#assign-frame').attr('scrolling', 'auto');
    $('#assign-frame').attr('frameborder', '0');
    $('#assign-frame').attr('src', options.iframeUrl);
    $('#assign-frame').css({'width' : options.width ? options.width : (szs[0] - 24) + 'px', 'height' : options.height ? options.height : (szs[1] - 26 - 43 - 43) + 'px', 'margin' : '44px 0', 'z-index' : '11'});

    $('#main-assign').show();
    $('#main-assign').css({
      'width'       : options.width  ? options.width : (szs[0] - 24) + 'px',
      'height'      : options.height ? options.height: (szs[1] - 24) + 'px',
      'position'    : (ie ? 'absolute' : 'fixed'),
      'text-align'  : 'center',
      'top'         : options.top ? options.top : 10,
      'left'        : options.left ? options.left : 10,
      'margin-top'  : options.marginTop ? options.marginTop : 'auto',
      'margin-left' : options.marginLeft ? options.marginLeft : 'auto',
      'z-index'     : 2000
    });

    // Фиксим размеры при ресайзе окна
    $(window).on('resize', function() {
      if( document.getElementById('main-assign') )
      {
        var szs = assigner._getScreenSizes();

        if( !options.width )
        {
          $('#main-assign').css('width', (szs[0] - 24) + 'px');
          $('#assign-frame').css('width', (szs[0] - 24) + 'px');
        }

        if( !options.height )
        {
          $('#main-assign').css('height', (szs[1] - 24) + 'px');
          $('#assign-frame').css('height', (szs[1] - 26 - 43 - 43) + 'px');
        }
      }
    });
  },

  _updateGrid : function(options) {
    if( options.updateGridId )
      $.fn.yiiGridView.update(options.updateGridId);
  }
}