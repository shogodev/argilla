<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.orders
 */
class BOrderButtonColumn extends BButtonColumn
{
  public $template = '{setUser} {update} {delete}';

  public function init()
  {
    parent::init();

    $this->buttons['setUser'] = array(
      'label' => 'Зарегистрировать пользователя',
      'icon' => 'pencil',
      'url' => 'Yii::app()->controller->createUrl("/user/bFrontendUser/search", array(
        "BOrder[id]" => $data->id,
        "BFrontendUser[userPhone]" => $data->phone,
        "BFrontendUser[email]" => $data->email,
        "BFrontendUser[fullName]" => $data->name,
        "popup" => true
      ))',
      'options' => array(
        'class' => 'add',
        'data-ajaxurl' => Yii::app()->controller->createUrl('/order/bOrder/setUser'),
      ),
    );

    $this->registerPopupScript();
  }

  protected function renderButton($id, $button, $row, $data)
  {
    if( $id === 'setUser' && !empty($data->user_id) )
    {
      return;
    }

    parent::renderButton($id, $button, $row, $data);
  }

  protected function registerPopupScript()
  {
    $assignerOptions = CJavaScript::encode(array(
      'addButton' => true,
    ));

    Yii::app()->clientScript->registerScript('userSearchPopup', <<<EOD
jQuery('body').on('click', '.items a.add', function(e){
  e.preventDefault();
  var options = {$assignerOptions};

  var iframeUrl = $(this).attr('href');
  var ajaxUrl = $(this).data('ajaxurl') + '?' + iframeUrl.split('?')[1];

  options.callback = function(elements)
  {
    var ids = [];
    $(elements).each(function(){
      ids.push($(this).attr('id').match(/pk_(\d+)/)[1]);
    });

    if( !ids.length )
      return;

    var finish = function(){jQuery.fn.yiiGridView.update('{$this->grid->id}');};
    $.post(ajaxUrl, {'ids' : ids}, finish, 'json').fail(function(xhr){ajaxUpdateError(xhr)});
  };

  options.iframe_load = function()
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
  };

  assigner.open(iframeUrl, options);
});
EOD
, CClientScript::POS_READY);
  }
}