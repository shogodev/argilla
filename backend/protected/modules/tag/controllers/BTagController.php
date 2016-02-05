<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2015 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BTagController extends BController
{
  public $position = 10;

  public $name = 'Теги';

  public $modelClass = 'BTag';

  public function actionAppendTag()
  {
    try
    {
      if( $data = Yii::app()->request->getPost('appendTag') )
      {
        $tagName = Yii::app()->format->trim($data['name']);

        if( !$tag = BTag::model()->findByAttributes(array('name' => $tagName, 'group' => $data['group'])) )
        {
          $tag = new BTag();
          $tag->setAttributes($data);
          if( !$tag->save() )
            throw new ModelValidateException($tag);
        }

        if( !TagItem::model()->findByAttributes(array('tag_id' => $tag->id, 'group' => $tag->group, 'item_id' => $data['itemId'])) )
        {
          $tagItem = new TagItem();
          $tagItem->setAttributes(array(
            'tag_id' => $tag->id,
            'group' => $tag->group,
            'item_id' => $data['itemId']
          ));

          if( !$tagItem->save() )
            throw new ModelValidateException($tagItem);
        }
      }
    }
    catch(ModelValidateException $e)
    {
      throw new Http404Exception(500, $e->getMessage());
    }
  }

  public function actionDeleteTagItem()
  {
    if( $data = Yii::app()->request->getPost('deleteTag') )
    {
      if( $model = TagItem::model()->findByAttributes(array('tag_id' => $data['id'], 'group' => $data['group'], 'item_id' => $data['itemId'])) )
      {
        if( !$model->delete() )
          throw new WarningException('Не удалсь удалить tag');
      }
    }
  }
}