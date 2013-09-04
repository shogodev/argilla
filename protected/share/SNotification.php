<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.form.models
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
class SNotification extends CActiveRecord
{
  public $defaultSubject = "Письмо с сайта {projectName}";

  public $layout = 'main';

  public $from = "info@{projectName}";

  private $emailComponent;

  public static function model($className = __CLASS__)
  {
    return parent::model(get_called_class());
  }

  public function init()
  {
    parent::init();

    $this->setEmailComponent();
    $this->from = $this->replaceProjectName($this->from);
  }

  public function tableName()
  {
    return '{{notification}}';
  }

  public function afterFind()
  {
    parent::afterFind();

    $this->email   = $this->trimEmails($this->email);
    $this->subject = $this->replaceProjectName($this->subject);
  }

  public function setEmailComponent($component = null)
  {
    if( $component === null )
    {
      $component = Yii::app()->email;
    }

    $this->emailComponent = $component;
  }

  /**
   * @param CModel|string $index
   * @param array $varsForView
   * @param string $mailForSend дополнительные адреса для отправки
   */
  public function send($index, $varsForView = array(), $mailForSend = '')
  {
    if( $index instanceof CModel )
    {
      $varsForView['model'] = $index;
      $index = get_class($index);
    }

    $this->registerIndex($index);
    $data = $this->findByIndex($index);

    if( $data !== null )
    {
      if( empty($data->email) && empty($mailForSend) )
      {
        return;
      }

      $this->prepareEmail($varsForView, $data);
      $this->sendEmail($mailForSend, $data);
    }
  }

  /**
   * @param $varsForView
   * @param $data
   */
  private function prepareEmail($varsForView, $data)
  {
    $this->emailComponent->viewsPath = 'frontend.views.email.';
    $this->emailComponent->from      = $this->from;
    $this->emailComponent->layout    = $this->layout;
    $this->emailComponent->subject   = $data->subject;
    $this->emailComponent->view      = null;

    if( !empty($data->view) )
    {
      $this->emailComponent->view     = $data->view;
      $this->emailComponent->viewVars = $varsForView;
    }
    else
    {
      $this->emailComponent->message = $data->message;
    }
  }

  /**
   * @param $mailForSend
   * @param $data
   */
  private function sendEmail($mailForSend, $data)
  {
    $emailsTo = array();

    if( !empty($data->email) )
    {
      $emailsTo = array_merge($emailsTo, explode(',', $data->email));
    }

    if( !empty($mailForSend) )
    {
      $emailsTo = explode(',', $mailForSend);
    }

    foreach(array_unique($emailsTo) as $email)
    {
      $this->emailComponent->to = trim($email);
      $this->emailComponent->send();
    }
  }

  /**
   * @param string $data
   *
   * @return string
   */
  private function replaceProjectName($data)
  {
    $projectName = isset(Yii::app()->params->project) ? Yii::app()->params->project : '';
    return strtr($data, array('{projectName}' => $projectName));
  }

  /**
   * @param $index
   *
   * @return SNotification
   */
  private function findByIndex($index)
  {
    return $this->find("`index` = :index AND visible = 1 AND (view != '' OR message != '')", array(':index' => $index));
  }

  /**
   * @param string $emails
   *
   * @return string
   */
  private function trimEmails($emails)
  {
    $emails  = !empty($emails) ? explode(',', $emails) : array();
    $trimmed = Arr::trim($emails);

    return implode(',', $trimmed);
  }

  /**
   * @param string $index
   */
  private function registerIndex($index)
  {
    if( !$this->findByAttributes(array('index' => $index)) )
    {
      $model          = new self;
      $model->index   = $index;
      $model->subject = $this->defaultSubject;
      $model->visible = 0;
      $model->save(false);
    }
  }
}