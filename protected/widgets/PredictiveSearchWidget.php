<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.widgets
 *
 * Пример использования:
 * $this->widget('PredictiveSearchWidget', array(
 *   'name' => 'search',
 *   'url' => $this->createUrl('search/predictiveSearch'),
 *   'htmlOptions' => array(
 *     'class' => 'inp',
 *     'title' => 'поиск...'
 *   )
 * ));
 */
class PredictiveSearchWidget extends CWidget
{
  public $name;

  public $value;

  public $url;

  public $htmlOptions = array();

  public function init()
  {
    if( isset($this->htmlOptions['id']) )
      $this->id = $this->htmlOptions['id'];
    else
      $this->htmlOptions['id'] = $this->id;
  }

  public function run()
  {
    $this->registerPredictiveSearchScript();

    // todo: Исправить на searchField после релиза Yii 1.1.15
    echo CHtml::tag(
      'input',
      CMap::mergeArray($this->htmlOptions, array(
        'type' => 'search',
        'name' => $this->name,
        'value' => $this->value
    )));
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
                label : resp[i].replace(new RegExp('(' + query.term + ')', 'gi'), '<strong>$1</strong>'),
                value : resp[i]
              });
            }
            setData(array);
          }, 'json');
        },
        select: function( event, ui ) {
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

          var keywords = $.trim(this.term).split(' ').join('|');
          var output = item.label.replace(new RegExp('(' + keywords + ')', 'gi'), '<span class=\"ui-menu-item-highlight\">$1</span>');

          return $('<li>')
            .append($('<a>').html(output))
            .appendTo(ul);
        };
      }
    ");
  }
}