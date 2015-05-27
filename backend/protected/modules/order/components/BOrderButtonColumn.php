<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.order.components
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
      'options' => array('class' => 'add'),
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
      'multiSelect' => false,
      'updateGridId' => $this->grid->id,
    ));

    Yii::app()->clientScript->registerScript('userSearchPopup', "
      jQuery('body').on('click', '.items a.add', function(e){
        e.preventDefault();
        var options = {$assignerOptions};
        options.iframeUrl = $(this).attr('href');
        options.submitUrl = '".Yii::app()->controller->createUrl('/order/bOrder/setUser')."' + '?' + $(this).attr('href').split('?')[1];
        assigner.apply(this, options);
      });
    ", CClientScript::POS_READY);
  }
}