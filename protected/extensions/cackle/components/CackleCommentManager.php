<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class CackleCommentManager extends CackleAbstractManager implements CackleManagerInterface
{
  /**
   * @param $page
   * @param $limit
   * @param $modified
   *
   * @return array
   */
  public function getRemoteItems($page, $limit, $modified)
  {
    return $this->cackleApi->getComments($page, $limit, $modified);
  }

  protected function getModel()
  {
    return CackleComment::model();
  }

  protected function getNewModel()
  {
    return new CackleComment();
  }

  /**
   * @param CackleComment|FActiveRecord $comment
   * @param CackleResponseComment $item
   *
   * @throws CHttpException
   */
  protected function save(FActiveRecord $comment, $item)
  {
    $comment->id = $item->id;
    $comment->url = $item->url;
    $comment->channel = $item->channel;
    $comment->date = date('Y-m-d H:i:s', $item->created / 1000);
    $comment->comment = $item->message;
    $comment->rating = $item->rating;

    if( isset($item->author) )
    {
      $comment->author = isset($item->author->name) ? $item->author->name : null;
      $comment->email = isset($item->author->email) ? $item->author->email : null;
      $comment->avatar = isset($item->author->avatar) ? $item->author->avatar : null;
    }
    else if( isset($item->anonym) )
    {
      $comment->author = $item->anonym->name;
      $comment->email = isset($item->anonym->email) ? $item->anonym->email : null;
    }

    $comment->ip = $item->ip;
    $comment->status = $item->status;
    $comment->modified = $item->modified;

    if( !$comment->save() )
    {
      throw new CHttpException(500, Arr::reset(Arr::reset($comment->errors)));
    }
  }
} 