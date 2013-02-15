function YaMaps()
{
  this.data = {
    filials : []
  };

  this.map       = {};
  this.clusterer = {};
  this.markers   = [];

  this.icon = {
    href   : '/i/map/dealer_icon.gif',
    size   : [23, 30],
    offset : [-11, -30]
  };

  this.balloonOffset = [-5, 2];

  this.center = [55.858998, 55.331185];

  this.zoom = 4;

  this.balloonLayout = '<div class="map-popup"><div id="users-list" class="all">{content}</div></div>'
}

/**
 * Инициализируем карты с маркерами
 *
 * @param canvas
 * @param filials
 * @param zoom
 * @param center
 */
YaMaps.prototype.create = function(canvas, filials, zoom, center)
{
  var center = center ? center : this.center;

  this.data.filials = filials;

  var map = new ymaps.Map(canvas, {
    center : center,
    zoom   : zoom ? zoom : this.zoom,
    type   : 'yandex#map'
  });
  map.setCenter(center, zoom, { checkZoomRange: true });
  map.controls.add('zoomControl').add('typeSelector').add('mapTools');

  // Создаем ссылку на статическую карту для печати и вешаем ее дата-атрибутом на контейнер
  var stMapTypes = {'yandex#map' : 'map', 'yandex#satellite' : 'sat', 'yandex#hybrid' : 'sat,ski', 'yandex#publicMap' : 'pmap'},
      center = map.getCenter(),
      mapUrl = 'http://static-maps.yandex.ru/1.x/?ll='+center[1]+','+center[0]+
               '&z='+map.getZoom()+'&l='+stMapTypes[map.getType()]+
               '&size=650,450'+
               '&pt='+center[1]+','+center[0]+'%2Cpm2lbl';
  //$('#'+canvas).data('static-map-link', mapUrl);
  // Так карта перед печатью не всегда успевает подгрузиться, пришлось вставлять тут(
  $('#'+canvas).parent().append('<img class="right static-map" src="'+ mapUrl +'" alt="" />');

  this.map = map;
  this._initMarkers();

  // Устанавливаем шаблон для балуна
  var balloonLayout = ymaps.templateLayoutFactory.createClass(this.balloonLayout.replace("{content}", "$[properties.balloonContent]"));
  ymaps.layout.storage.add('dealers#baloonLayout', balloonLayout);
};

/**
 * Инициализируем маркеры и помещаем их в кластер
 *
 * @param clustering
 */
YaMaps.prototype._initMarkers = function(clustering)
{
  // todo: реализовать возможность работы без кластеризации
  clustering = true;

  for(var i in this.data.filials)
  {
    if( this.data.filials.hasOwnProperty(i) )
    {
      if( !this.data.filials[i].coordinates )
      {
        this._updateMarkerCoordinates(this.data.filials[i]);
        continue;
      }

      var marker = this._buildMarker(this.data.filials[i]);
      this.markers.push(marker);
    }
  }

  if( clustering )
  {
    this._initCluster();
  }
};

/**
 * Инициализируем кластер маркеров
 */
YaMaps.prototype._initCluster = function()
{
  var self = this;

  var iconContentLayout = ymaps.templateLayoutFactory.createClass(
    '<div style="display: none;">$[properties.geoObjects.length]</div>'
  );

  var balloonLayout = ymaps.templateLayoutFactory.createClass("", {
    build: function(){self._buildClusterBalloon(this)}
  });

  var clusterer = new ymaps.Clusterer({
    gridSize                        : 60,
    clusterIcons                    : [this.icon],
    balloonOffset                   : this.balloonOffset,
    hideIconOnBalloonOpen           : true,
    clusterDisableClickZoom         : true,
    clusterIconContentLayout        : iconContentLayout,
    clusterBalloonContentBodyLayout : balloonLayout,
    clusterNumbers                  : []
  });

  clusterer.add(this.markers);
  this.map.geoObjects.add(clusterer);
  this.clusterer = clusterer;
};

/**
 * Строим содержимое попапа кластера
 *
 * @private
 */
YaMaps.prototype._buildClusterBalloon = function(baloon)
{
  var parent     = baloon.getParentElement();
  var geoObjects = baloon.getData().properties.get('geoObjects');
  var content    = "";

  geoObjects.sort(this._sortClusterObjects);

  for(var i = 0, l = geoObjects.length; i < l; i++)
    content += geoObjects[i].properties.get('balloonContent');

  parent.innerHTML = this.balloonLayout.replace("{content}", content);
};

/**
 * Сортируем объекты кластера перед его показом
 *
 * @param a
 * @param b
 * @private
 */
YaMaps.prototype._sortClusterObjects = function(a, b)
{
  var acPos = Number(a.properties.get('city_position'));
  var afPos = Number(a.properties.get('position'));

  var bcPos = Number(b.properties.get('city_position'));
  var bfPos = Number(b.properties.get('position'));

  return Number(acPos * 1000 + afPos) > Number(bcPos * 1000 + bfPos) ? 1 : -1;
};

/**
 * Строим объект маркера
 *
 * @param data
 * @return {}
 * @private
 */
YaMaps.prototype._buildMarker = function(data)
{
  return new ymaps.Placemark(data.coordinates, {
    clusterCaption : data.dealer.name,
    balloonContent : this._createBalloonContent(data),

    // Произвольные данные маркера
    position       : data.position,
    city_position  : data.city_position,
    city           : data.city_id
  }, {
    balloonContentBodyLayout : 'dealers#baloonLayout',
    iconImageHref            : this.icon['href'],
    iconImageSize            : this.icon['size'],
    iconImageOffset          : this.icon['offset'],
    balloonOffset            : this.balloonOffset,
    hideIconOnBalloonOpen    : true
  });
};

/**
 * Строим содержимое Балуна
 *
 * @param data
 * @return {String}
 * @private
 */
YaMaps.prototype._createBalloonContent = function(data)
{
  var content =
    "<div class='text-container'>" +
    "<div style='width: 100px; text-align: center;'>";

  if( data.dealer.img )
  {
    content +=
      "<a href='" + data.url + "'>" +
        "<img class='left' alt='' style='max-width: 100px; max-height: 100px;' src='" + data.dealer.img + "'>" +
      "</a>";
  }

  content +=
    "</div>" +
    "<div class='nofloat'>" +
    "<div class='s14 bb'><a href='" + data.url + "'>" + data.dealer.name + "</a>" + (data.city != "" ? "(" + data.city + ")" : "") + "</div>";

  if( data.link )
    content += "<div class='s11 grey'>URL-адрес: <a href='" + data.href + "'>" + data.link + "</a></div>";

  content += "<div class='s11 grey'>Адрес: г. " + data.city + ", " + data.address + "</div>";

  if( data.phone )
    content += "<div class='s11 grey'>Тел: " + data.phone +"</div>";

  if( data.worktime )
    content += "<div class='s11 grey'>Время работы: " + data.worktime + "</div>";

  content += "</div>";
  content += "</div>";

  return content;
};

/**
 * Обработчик клика по названию города под картой
 *
 * @param cities
 */
YaMaps.prototype.citiesHandler = function(cities)
{
  var self = this;

  $(cities).on('click', function()
  {
    var id = $(this).data('city-id');
    self._createCityBalloon(id);
  });
};

/**
 * Создаем балун для выбранного города
 *
 * @param id
 * @private
 */
YaMaps.prototype._createCityBalloon = function(id)
{
  var cityMarkers = [];

  for(var i in this.markers)
    if( this.markers.hasOwnProperty(i) && this.markers[i].properties.get('city') == id )
      cityMarkers.push(this.markers[i]);

  if( cityMarkers.length )
    this._showCityBalloon(cityMarkers);
};

/**
 * Отображаем маркеры, принадлежащие выбранному городу
 *
 * @param cityMarkers
 * @private
 */
YaMaps.prototype._showCityBalloon = function(cityMarkers)
{
  var self = this;
  var marker = cityMarkers[0];
  var balloon;
  var clusterMarkers;

  if( this.map.balloon.isOpen() )
    this.map.balloon.close();

  this.map.setCenter(marker.geometry.getCoordinates(), this.map.getZoom(),
  { checkZoomRange: true, callback: function(err)
    {
      if(!err)
      {
        window.scrollBy(0, -9999);

        var state = self.clusterer.getObjectState(marker);

        if( state.isShown )
        {
          if( state.isClustered )
          {
            clusterMarkers = state.cluster.properties.get('geoObjects');
            state.cluster.properties.set('geoObjects', cityMarkers);
            balloon = state.cluster.balloon;
          }
          else
            balloon = marker.balloon;

          setTimeout(function(){
            balloon.open();
            if( clusterMarkers && clusterMarkers.length )
              state.cluster.properties.set('geoObjects', clusterMarkers);
          }, 40);
        }
      }
    }
  });

};
/**
 * Инициализируем регистрационнубю карту для диллера
 */

YaMaps.prototype.dealerRegistration = function()
{
  var self        = this;
  this.regMaps    = {};
  this.regMarks   = {};
  $('[name*=\\[city_id\\]]').live('change', function()
  {
    self.initReg(this);
  });
  $('[name*=\\[address\\]]').live('blur', function()
  {
    self.initReg(this);
  });

};
/**
 * Функция проверки входных данных с формы регистрации
 * Определяет с чем ей придется работать: много карт, либо одна
 * После чего ветвит сценарий обработки входных данных и координат
 */

YaMaps.prototype.initReg = function(pointer)
{
  var self    = this;
  var matches = $(pointer).attr('id').match(/_(\d+)_/);
  var id      = matches ? matches[1] : undefined;
  var city_id = $('[name*=' + (matches ? '\\['+ id +'\\]' : '') + '\\[city_id\\]] option:selected').val();
  var city    = $('[name*=' + (matches ? '\\['+ id +'\\]' : '') + '\\[city_id\\]] option:selected').text();
  var address = $('[name*=' + (matches ? '\\['+ id +'\\]' : '') + '\\[address\\]]').val();
  if( !city_id )
  {
    /**
     * Необходимо инициализировать валидацию города перед тем как строить адресс
     */
  }
  else
  {
    var locName = city + ' , ' +  address;
    self.showMapMark(locName, id);
  }
};
/**
 * Обрабатываем адрес геокодером и отдаем его функции создания карты и маркера
 *
 * @param locName - строка полного адрес "Город" + "Адрес"
 * @param id - идентификатор филиала на странице добавления филиалов, считаются от 0.
 */
YaMaps.prototype.showMapMark = function(locName, id)
{
  var self      = this;
  ymaps.geocode(locName, { results: 1 }).then(function(res){
    var location = res.geoObjects.get(0).geometry.getCoordinates();
    self.regShowMark( location , 16, id);
    $('[name*=' + (id  ? '\\['+ id + '\\]' : '')  + '\\[coordinates\\]]').val(location.join(','));
  }, function(err){
    /**
     * Обработчик ошибки геокодера, в общем случае нам не потребуется, т.к. мы постараемся отсеять ошибкоопасные места
     * до геокодирования, если же когда встанет вопрос, то писать его необходимо тут
     */
  });
};
/**
 * Создаем карту и маркер на ней, включаем отображение дива с картой
 * @param coords - координаты полученные от геокодера.
 * @param zoom - уровень зума на карте, так же получаемый извне.
 * @param id - идентификатор филиала на странице добавления филиалов, считаются от 0.
 */
YaMaps.prototype.regShowMark = function(coords, zoom, id)
{
  if( $('#YandexMap_'+ (id ? id : 0)).is(':hidden') )
  {
    $('#YandexMap_'+ (id ? id : 0)).show();
    this.regMaps[(id ? id : 0)] = new ymaps.Map( 'YandexMap_'+(id ? id : 0) ,{
      center: coords,
      zoom: zoom || 4,
      type: 'yandex#map'
    });
    this.regMaps[(id ? id : 0)].controls
      // Кнопка изменения масштаба
      .add('smallZoomControl')
      // Стандартные инструменты карты
      .add('mapTools');
  }
  else
  {
    this.regMaps[(id ? id : 0)].setCenter(coords, zoom, { checkZoomRange: true });
  }
  if( this.regMarks[(id ? id : 0)] )
  {
    this.regMaps[(id ? id : 0)].geoObjects.remove(this.regMarks[(id ? id : 0)]);
    delete this.regMarks[(id ? id : 0)];
  }
  this.regMarks[(id ? id : 0)] = new ymaps.GeoObject({
      geometry: {
        type               : "Point",
        coordinates        : coords
      }},
    {
      draggable            : true,
      iconImageHref        : this.icon['href'],
      iconImageSize        : this.icon['size'],
      iconImageOffset      : this.icon['offset']
    });
  this.regMaps[(id ? id : 0)].geoObjects.add(this.regMarks[(id ? id : 0)]);
  this.regMarks[(id ? id : 0)].events.add("dragend", function(e){
    $('[name*=' + (id  ? '\\['+ id + '\\]' : '')  + '\\[coordinates\\]]').attr('value', e.get('target').geometry.getCoordinates().join(','));
  });
}



