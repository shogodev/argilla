<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.share
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

    $this->email = $this->trimEmails($this->email);
    $this->subject = $this->replaceProjectName($this->subject);
  }

  /**
   * @param CModel|string $index
   * @param array $vars
   * @param string $mailForSend дополнительные адреса для отправки
   * @param string $layout по умолчанию main
   */
  public function send($index, $vars = array(), $mailForSend = '', $layout = null)
  {
    $this->prepareVars($index, $vars);
    $this->registerIndex($index);

    if( $data = $this->findByIndex($index) )
    {
      if( empty($data->email) && empty($mailForSend) )
      {
        return;
      }

      if( !is_null($layout) )
        $data->layout = $layout;

      $this->prepareEmail($vars, $data);
      $this->sendEmail($mailForSend, $data);
    }
  }

  private function setEmailComponent($component = null)
  {
    if( $component === null )
    {
      $component = Yii::app()->email;
    }

    $this->emailComponent = $component;
  }

  /**
   * @param $vars
   * @param $data
   */
  private function prepareEmail($vars, $data)
  {
    $this->emailComponent->viewsPath = 'frontend.views.email.';
    $this->emailComponent->from = $this->from;
    $this->emailComponent->layout = $data->layout;
    $this->emailComponent->subject = ViewHelper::replace($data->subject, $vars, true);
    $this->emailComponent->view = null;

    if( !empty($data->view) )
    {
      $this->emailComponent->view = $data->view;
      $this->emailComponent->viewVars = CMap::mergeArray($vars, array('subject' => $this->emailComponent->subject));
    }
    else
    {
      $this->emailComponent->message = ViewHelper::replace($data->message, $vars, true);
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
    $emails = !empty($emails) ? explode(',', $emails) : array();
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
      $model = new self;
      $model->index = $index;
      $model->subject = $this->defaultSubject;
      $model->visible = 0;
      $model->save(false);
    }
  }

  private function prepareVars(&$index, &$vars)
  {
    if( $index instanceof CModel )
    {
      $vars['model'] = $index;
      $index = get_class($index);
    }

    $vars['host'] = Yii::app()->request->hostInfo;
    $vars['project'] = Yii::app()->params->project;
    $vars['subject'] = Yii::app()->params->project;

    if( Yii::app()->controller->asa('common') )
    {
      if( $contact = Yii::app()->controller->getHeaderContacts() )
      {
        $vars['emails'] = $contact->getFields('emails');
        $vars['email'] = Arr::get($vars['emails'], 0, '');
        $vars['phones'] = $contact->getFields('phones');
        $vars['phone'] = Arr::get($vars['phones'], 0, '');
      }
    }

    return $index;
  }
}