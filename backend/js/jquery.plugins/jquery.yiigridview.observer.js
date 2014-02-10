$( function($) {

  if( $.fn.yiiGridView )
  {
    $.fn.yiiGridView.observers = {};

    /**
     * @param id
     * @param observer
     */
    $.fn.yiiGridView.addObserver = function(id, observer)
    {
      if( !$.fn.yiiGridView.observers[id] )
        $.fn.yiiGridView.observers[id] = [];

      $.fn.yiiGridView.observers[id].push(observer);
    };

    /**
     * @param id
     * @param data
     */
    $.fn.yiiGridView.notifyObservers = function(id, data)
    {
      if( !$.fn.yiiGridView.observers[id] )
        return;

      for(var i in $.fn.yiiGridView.observers[id])
      {
        if( $.fn.yiiGridView.observers[id].hasOwnProperty(i) )
        {
          if( typeof $.fn.yiiGridView.observers[id][i] === 'function'  )
            $.fn.yiiGridView.observers[id][i](id, data);
        }
      }
    };
  }
});