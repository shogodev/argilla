<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 *
 * Пример использования:
 * $this->widget('SearchWidget', array(
 *   'htmlOptions' => array(
 *     'class' => 'inp',
 *     'title' => 'поиск...'
 *   )
 * ));
 */
class SearchWidget extends CWidget
{
  public $name = 'text';

  public $value;

  public $url;

  public $htmlOptions = array();

  public function init()
  {
    if( isset($this->htmlOptions['id']) )
      $this->id = $this->htmlOptions['id'];
    else
      $this->htmlOptions['id'] = $this->id;

    if( !isset($this->url) )
      $this->url = Yii::app()->createUrl('search/predictive');
  }

  public function run()
  {
    $this->registerPredictiveSearchScript();

    echo CHtml::hiddenField('searchid', Yii::app()->params['yandexSearchId']);
    echo CHtml::searchField($this->name, $this->value, $this->htmlOptions);
  }

  private function registerPredictiveSearchScript()
  {
    Yii::app()->clientScript->registerScript('PredictiveSearchScript#'.$this->id, "
      autocompletePatch();

      $('#".$this->id."').autocomplete({
        minLength: 2,
        delay: 300,
        search: '',
        source: function(query, setData) {
          var array = [];
          $.post('".$this->url."', {'query' : query.term}, function(resp) {
            for(i in resp)
            {
              array.push({
                label : resp[i].label,
                value : resp[i].value,
                url : resp[i].url
              });
            }
            setData(array);
          }, 'json');
        },
        select: function( event, ui ) {
          if( ui.item.url !== undefined )
            location.href = ui.item.url;
          else
          $('#".$this->id."').val(ui.item.value).closest('form').submit();
        }
      });");

    $this->registerScriptAutocompletePath();
  }

  private function registerScriptAutocompletePath()
  {
    Yii::app()->clientScript->registerScript('ScriptAutocompletePath', "
      function autocompletePatch() {
        $.ui.autocomplete.prototype._renderItem = function (ul, item) {
          return $('<li>').append($('<a>').html(item.label)).appendTo(ul);
        };
      }
    ");
  }
}