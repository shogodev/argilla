<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.models
 *
 * @method static CackleComment model(string $class = __CLASS__)
 *
 * @property integer $id
 * @property string $channel
 * @property string $url
 * @property string $comment
 * @property integer $rating
 * @property string $date
 * @property string $author
 * @property string $email
 * @property string $avatar
 * @property string $ip
 * @property string $status
 * @property integer $modified
 */
class CackleComment extends FActiveRecord
{
  const STATUS_APPROVED = 'approved';

  public function rules()
  {
    return array(
      array('channel', 'required'),
      array('modified, rating', 'numerical', 'integerOnly' => true),
      array('author, email, status, ip, status', 'length', 'max' => 255),
      array('date, comment, url', 'safe'),
    );
  }

  public function getCommentByChannel($channel)
  {
    return $this->findAllByAttributes(array('channel' => $channel, 'status' => self::STATUS_APPROVED));
  }
}