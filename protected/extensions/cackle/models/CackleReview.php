<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @method static CackleReview model(string $class = __CLASS__)
 *
 * @property string $id
 * @property string $url
 * @property string $channel
 * @property string $dignity
 * @property string $lack
 * @property string $comment
 * @property string $date
 * @property string $author
 * @property string $email
 * @property string $avatar
 * @property string $ip
 * @property string $status
 * @property integer $stars
 * @property integer $rating
 * @property integer $modified
 */
class CackleReview extends FActiveRecord
{
  const STATUS_APPROVED = 'approved';

  public function rules()
  {
    return array(
      array('channel', 'required'),
      array('stars, rating, modified', 'numerical', 'integerOnly' => true),
      array('author, email, avatar, avatar, status, ip', 'length', 'max' => 255),
      array('date, comment, dignity, lack, comment', 'safe'),
    );
  }

  public function getReviewsByChannel($channel)
  {
    return $this->findAllByAttributes(array('channel' => $channel, 'status' => self::STATUS_APPROVED));
  }
}