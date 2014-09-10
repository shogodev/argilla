<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
Yii::import('ext.cackle.models.*');
Yii::import('ext.cackle.components.*');

final class CackleSync
{
  private $limit;

  /**
   * @var CackleManagerInterface
   */
  private $manager;

  /**
   * @param CackleManagerInterface $manager
   * @param int $limit default 50
   */
  public function __construct(CackleManagerInterface $manager, $limit = 50)
  {
    $this->manager = $manager;
    $this->limit = $limit;
  }

  /**
   * @var CackleManagerInterface $manager
   */
  public function create()
  {
    Yii::app()->getDb()->beginTransaction();

    $this->manager->clearAll();
    $this->process();

    Yii::app()->getDb()->currentTransaction->commit();
  }

  public function update()
  {
    Yii::app()->getDb()->beginTransaction();

    $this->process($this->manager->getLastModified());

    Yii::app()->getDb()->currentTransaction->commit();
  }

  /**
   * @param CackleManagerInterface $manager
   * @param CackleResponseReview[] $items
   * @param array $idsForUpdate
   */
  protected function saveItems($manager, array $items, $idsForUpdate = array())
  {
    foreach($items as $item)
    {
      if( !isset($idsForUpdate[$item->id]) )
      {
        $manager->insert($item);
      }
      else
      {
        $manager->update($item);
      }
    }
  }

  protected function process($modified = null)
  {
    $idsForUpdate = $this->manager->getIdsForUpdate();
    $defaultItems = $this->manager->getRemoteItems(0, $this->limit, $modified);

    for($page = 0; $page < $defaultItems->totalPages; $page++)
    {
      $items = $this->manager->getRemoteItems($page, $this->limit, $modified);
      $this->saveItems($this->manager, $items->content, $idsForUpdate);
    }
  }
}