<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FFavorite extends FCollectionUI
{
  protected $classMergeWithBasket = 'merge-{keyCollection}-with-basket';

  public function buttonMergeWithBasket($text = '', $htmlOptions = array())
  {
    $this->appendHtmlOption($htmlOptions, $this->classMergeWithBasket);

    return CHtml::link($text, '#', $htmlOptions);
  }

  protected function registerScriptButtonMergeWithBasket()
  {
    $url = Yii::app()->controller->createUrl('favorite/mergeWithBasket');

    $this->registerScript("$('body, .{$this->classMergeWithBasket}').on('click', '.{$this->classMergeWithBasket}', function(e){
      e.preventDefault();

      var collection = $.fn.collection('{$this->keyCollection}');

      collection.send({
        'url' : '{$url}',
        'action' : 'mergeWithBasket',
      });
    });");
  }


  protected function registerScripts()
  {
    parent::registerScripts();

    $this->registerScriptButtonMergeWithBasket();
  }
}