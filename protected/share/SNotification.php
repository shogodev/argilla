<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
 *
 * @method static SNotification model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $index
 * @property string $name
 * @property string $email
 * @property string $subject
 * @property string $view
 * @property string $message
 * @property integer $visible
 */
class SNotification extends FActiveRecord
{
  public $defaultSubject = "Письмо с сайта {projectName}";
  public $layout         = 'main';
  public $from           = "info@{projectName}";

  public function init()
  {
    parent::init();
    $this->from =  strtr($this->from, array('{projectName}' => Yii::app()->params->project));
  }

  public function tableName()
  {
    return '{{notification}}';
  }

  public function rules()
  {
    return array(
      array('index', 'required'),
      array('index', 'unique'),
      array('subject, visible', 'safe')
    );
  }

  public function afterFind()
  {
    parent::afterFind();

    $this->email   = $this->clearEmails($this->email);
    $this->subject = strtr($this->subject, array('{projectName}' => Yii::app()->params->project));
  }

  /**
   * @param $model модель или индекс
   * @param array $varsForView
   * @param string $mailForSend не обязательно
   */
  public function send($model, $varsForView = array(), $mailForSend = '')
  {
    $index = is_a($model, 'CModel') ? get_class($model) : $model;

    $this->registerIndex($index);
    $data = $this->find("`index` = :index AND visible = 1 AND (view != '' OR message != '')", array(':index' => $index));

    if( $data !== null )
    {
      if( empty($data->email) && empty($mailForSend) )
        return;
      else
        $data->email = !empty($data->email) ? $data->email.','.trim($mailForSend) : trim($mailForSend);

      $email            = Yii::app()->email;
      $email->viewsPath = 'frontend.views.email.';

      $email->from    = $this->from;
      $email->layout  = $this->layout;

      $email->to      = $data->email;
      $email->subject = $data->subject;

      // Сбрасывем предыдущий шаблон
      $email->view    = null;

      if( !empty($data->view) )
      {
        if( is_a($model, 'CModel') )
          $varsForView['model'] = $model;

        $email->view     = $data->view;
        $email->viewVars = $varsForView;
      }
      else
        $email->message = $data->message;

      $email->send();
    }
  }

  private function clearEmails($emails)
  {
    $emails = !empty($emails) ? explode(',', $emails) : array();

    $clear_emails = array();
    foreach($emails as $email)
    {
      $email = trim($email);
      $clear_emails[$email] = $email;
    }

    return !empty($clear_emails)? implode(',', $clear_emails) : '';
  }

  private function registerIndex($index)
  {
    $data = $this->find('`index` = :index', array(':index' => $index));

    if( $data == null )
    {
      $data = new SNotification();
      $data->index   = $index;
      $data->subject = $this->defaultSubject;
      $data->visible = 0;

      $data->save();
    }
  }
}