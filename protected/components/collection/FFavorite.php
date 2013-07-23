<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.components.collection
 */
class FFavorite extends FBasket
{
/*  public function save()
  {
    Favorite::model()->deleteAllByAttributes(array('user_id' => Yii::app()->user->id));

    $favorite = new Favorite();

    $favorite->setAttributes(array(
      'user_id' => Yii::app()->user->id,
      'type' => $this->keyCollection,
      'value' => CJSON::encode($this->toArray($this)),
    ));

    $favorite->save();

    $this->createPathsRecursive();
  }

  public function load()
  {
    $favorite = Favorite::model()->findByAttributes(array('user_id'=> Yii::app()->user->id, 'type' => $this->keyCollection));

    $collectionData = $favorite ? CJSON::decode($favorite->value) : array();

    foreach($collectionData as $data)
    {
      $this->restoreIndex($data['index']);
      $this->add($data);
    }

    $this->createPathsRecursive();
  }*/
}