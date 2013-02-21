<?php

/**
 * @author Nikita Melnikov <melnikov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package backend.modules.menu
 *
 * @property integer $id
 * @property BFrontendMenuItem[] $entries
 * @property IFrontendMenuEntry[] $availableEntries
 * @property string $name
 * @property string $sysname
 * @property string $url
 * @property integer $visible
 *
 * @method static BFrontendMenu model(string $class = __CLASS__)
 */
class BFrontendMenu extends BAbstractMenuEntry
{
  /**
   * Массив с доступными для меню классами
   * 'class' - название модели
   * 'key'   - ключ, определяющий участие записи в меню
   *
   * @var array
   */
  protected $availableClasses = array(
    array('class' => 'BFrontendMenu', 'key'   => 'visible'),
    array('class' => 'BInfo', 'key' => 'menu'),
    array('class' => 'BFrontendCustomMenuItem', 'key' => 'visible'),
  );

  /**
   * @var IFrontendMenuEntry[]
   */
  protected $availableEntries = array();

  /**
   * Загрузка дополнительных классов для работы с меню
   * @HINT: По большей части нужны классы, доступные как элементы меню
   */
  public static function loadExtraModels()
  {
    Yii::import('backend.modules.info.models.*');
  }

  /**
   * @param string $scenario
   */
  public function __construct($scenario = 'insert')
  {
    self::loadExtraModels();
    return parent::__construct($scenario);
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }

  /**
   * @return string
   */
  public function getFrontendModelName()
  {
    return 'Menu';
  }

  /**
   * @return string
   */
  public function tableName()
  {
    return '{{menu}}';
  }

  /**
   * @return array
   */
  public function rules()
  {
    return array(
      array('name, sysname', 'required'),
      array('name, sysname', 'length', 'max' => 255),
      array('name, sysname, url', 'safe'),

    );
  }

  /**
   * @return array
   */
  public function relations()
  {
    return array(
      'entries' => array(self::HAS_MANY, 'BFrontendMenuItem', 'menu_id'),
    );
  }

  /**
   * Добавление записи меню
   *
   * @param IBFrontendMenuEntry $e
   *
   * @return bool
   */
  public function addEntry(IBFrontendMenuEntry $e)
  {
    if( !empty($this->id) && $e->getId() !== null )
    {
      $entry                 = new BFrontendMenuItem();
      $entry->item_id        = $e->getId();
      $entry->menu_id        = $this->id;
      $entry->type           = get_class($e);
      $entry->frontend_model = $e->getFrontendModelName();

      return $entry->save();
    }

    return false;
  }

  /**
   * Удаление записи меню
   *
   * @param IBFrontendMenuEntry $e
   *
   * @return bool
   */
  public function removeEntry(IBFrontendMenuEntry $e)
  {
    if( !empty($this->id) )
    {
      $criteria = new CDbCriteria();
      $criteria->compare('menu_id', $this->id);
      $criteria->compare('item_id', $e->getId());
      $criteria->compare('type', get_class($e));

      return BFrontendMenuItem::model()->find($criteria)->delete();
    }

    return false;
  }

  /**
   * Переключение отношения записи к меню
   *
   * @param IBFrontendMenuEntry $e
   */
  public function switchMenuEntryStatus(IBFrontendMenuEntry $e)
  {
    if( $this->hasMenuEntry($e) )
      $this->removeEntry($e);
    else
      $this->addEntry($e);
  }

  /**
   * Является ли запись CustomMenuItem элементом меню
   *
   * @param BFrontendCustomMenuItem $c
   *
   * @return bool
   */
  public function hasCustomMenuItem(BFrontendCustomMenuItem $c)
  {
    return $this->hasMenuEntry($c);
  }

  /**
   * Является ли $e записью меню
   *
   * @param IBFrontendMenuEntry $e
   *
   * @return bool
   */
  public function hasMenuEntry(IBFrontendMenuEntry $e)
  {
    $criteria = new CDbCriteria();
    $criteria->compare('type', get_class($e));
    $criteria->compare('item_id', $e->getId());
    $criteria->compare('menu_id', $this->id);

    return (boolean) BFrontendMenuItem::model()->find($criteria);
  }

  /**
   * @return IFrontendMenuEntry[]
   * @throws BFrontendMenuException
   */
  public function getAvailableEntries()
  {
    if( empty($this->availableEntries) )
    {
      foreach( $this->availableClasses as $class )
      {
        $className = $class['class'];

        if( new $className() instanceof IBFrontendMenuEntry )
        {
          $this->availableEntries = CMap::mergeArray(
              $className::model()->findAll($class['key'] . ' = 1'),
              $this->availableEntries
          );
        }
        else
          throw new BFrontendMenuException('Невозможно использовать класс для добавления к меню', BFrontendMenuException::WRONG_CLASS_INHERITANCE);
      }

      $this->clearEntries();
    }

    return $this->availableEntries;
  }

  /**
   * @return BActiveDataProvider
   */
  public function search()
  {
    $criteria = new CDbCriteria;

    $criteria->compare('name', $this->name, true);
    $criteria->compare('sysname', $this->sysname, true);
    $criteria->compare('url', $this->url, true);

    return new BActiveDataProvider($this, array(
      'criteria' => $criteria,
    ));
  }

  /**
   * Удаление из доступных элементов для добавления уже добавленных
   */
  protected function clearEntries()
  {
    foreach( $this->availableEntries as $key => $item )
    {
      foreach( $this->entries as $entry )
      {
        if( $entry->getModel()->getId() === $item->getId() && $entry->getModelClass() === get_class($item) )
          unset($this->availableEntries[$key]);

        if( $item->id === $this->id && get_class($item) === get_class($this) )
          unset($this->availableEntries[$key]);
      }
    }
  }
}