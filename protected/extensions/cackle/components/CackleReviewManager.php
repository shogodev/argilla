<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class CackleReviewManager extends CackleAbstractManager implements CackleManagerInterface
{
  public function getRemoteItems($page, $limit, $modified)
  {
    return $this->cackleApi->getReviews($page, $limit, $modified);
  }

  protected function getModel()
  {
    return CackleReview::model();
  }

  protected function getNewModel()
  {
    return new CackleReview();
  }

  /**
   * @param CackleReview|FActiveRecord $review
   * @param $item
   *
   * @throws CHttpException
   */
  protected function save(FActiveRecord $review, $item)
  {
    $review->id = $item->id;
    $review->url = $item->url;
    $review->channel = $item->channel;
    $review->dignity = $item->dignity;
    $review->lack = $item->lack;
    $review->comment = $item->comment;
    $review->date = date('Y-m-d H:i:s', $item->created / 1000);

    if( isset($item->author) )
    {
      $review->author = $item->author->name;
      $review->email = isset($item->author->email) ? $item->author->email : null;
      $review->avatar = isset($item->author->avatar) ? $item->author->avatar : null;
    }
    else if( isset($item->anonym) )
    {
      $review->author = $item->anonym->name;
      $review->email = isset($item->anonym->email) ? $item->anonym->email : null;
    }

    $review->ip = $item->ip;
    $review->stars = $item->stars;
    $review->rating = $item->rating;
    $review->status = $item->status;
    $review->modified = $item->modified;

    if( !$review->save() )
    {
      throw new CHttpException(500, Arr::reset(Arr::reset($review->errors)));
    }
  }
} 