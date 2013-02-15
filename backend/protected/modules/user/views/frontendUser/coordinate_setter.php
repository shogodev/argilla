<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

<div style="margin-bottom: 10px;">
  <div style="float: left; margin-right: 10px">
    <label for="coordinates">Координаты:</label>
    <input type="text" id="coordinates" value="<?php echo $model->coordinates?>" style="width: 300px" />
  </div>

  <div>
    <label for="coordinates">Адрес:</label>
    <input type="text" id="address" value="<?php echo $model->getFullAddress()?>" style="width: 300px" />
    <a class="btn btn-alone update" id="getAddress" href="#" style="margin-bottom: 10px"></a>
  </div>
</div>

<div class="text_container">
  <div id="map_canvas" style="width:100%; height:400px"></div>
</div>

<script type="text/javascript">
  //<![CDATA
  jQuery(function($)
  {
    var btnApply = $('<a class="btn btn-primary" href="#" style="margin-right: 5px;">Выбрать</a>');
    $(btnApply).on('click', function(e){
      $(window.parent.document).find('#<?php echo $attribute?>').val($('#coordinates').val());
      window.parent.assigner.close();
      e.preventDefault();
    });

    $(window.parent.document).find('#main-assign-buttons-top').prepend(btnApply);
    $(window.parent.document).find('#main-assign-buttons-bottom').prepend($(btnApply).clone(true));
  });

  jQuery(function($)
  {
    var zoom        = 10;
    var canvas      = $('#map_canvas').get(0);
    var coordinates = $(window.parent.document).find('#<?php echo $attribute?>').val();
    var center      = coordinates ? coordinates.split(',') : [55.755786, 37.617633];

    var map;
    var placemark;

    var initMap = function()
    {
      map = new ymaps.Map(canvas, {
        center: center,
        zoom: zoom,
        type: 'yandex#map'
      });

      map.controls.add('zoomControl');
      map.events.add('mouseup', function(e){
        setPlaceMark(e.get('coordPosition'));
      });

      setPlaceMark(coordinates);
    };

    var initPlaceMark = function(coordinates)
    {
      placemark = new ymaps.Placemark(coordinates, {}, {'draggable' : true});
      map.geoObjects.add(placemark);
      placemark.events.add('dragend', function(e){
        setCoordinates(placemark.geometry.getCoordinates());
      });
    };

    var setPlaceMark = function(coordinates)
    {
      coordinates = typeof coordinates === 'string' ? coordinates.split(',') : coordinates;

      if( coordinates.length === 2 )
      {
        placemark ? placemark.geometry.setCoordinates(coordinates) : initPlaceMark(coordinates);
        setCoordinates(coordinates);
      }
    };

    var setCoordinates = function(coordinates)
    {
      coords = coordinates.toString();
      $('#coordinates').val(coords);
    };

    $('#getAddress').on('click', function()
    {
      var geocoder = ymaps.geocode($('#address').val());
      geocoder.then(
        function(result)
        {
          var geoCoords = result.geoObjects.get(0).geometry.getCoordinates();
          setPlaceMark(geoCoords);
          map.setCenter(geoCoords, 12);
        },
        function(error){alert('Не удалось определить координаты объекта');}
      );
    });

    $('#address').on('keypress', function(e){
      var code = e.charCode || e.keyCode;
      if( code === 13 )
        $('#getAddress').trigger('click');
    });

    ymaps.ready(initMap);
  });
  //]]>
</script>