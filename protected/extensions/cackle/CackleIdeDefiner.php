<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package
 */

/**
 * Interface CackleResponseReviews
 * @property CackleResponseReview[] $content
 * @property integer $size
 * @property integer $number
 * @property array $sort
 * @property integer $totalPages
 * @property integer $numberOfElements
 * @property boolean $firstPage
 * @property boolean $lastPage
 */
interface CackleResponseReviews
{
}

/**
 * Interface CackleResponseReview
 * @property integer $id
 * @property integer $siteId
 * @property integer $scoreId
 * @property integer $sum
 * @property integer $count
 * @property integer $stars
 * @property string $dignity
 * @property string $lack
 * @property string $comment
 * @property string $title
 * @property string $url
 * @property CackleResponseAuthor $author
 * @property CackleAnonym $anonym
 * @property string $ip
 * @property integer $rating
 * @property integer $nrating
 * @property integer $created
 * @property string $modified
 * @property string $status
 * @property string $channel
 */
interface CackleResponseReview
{

}


/**
 * Interface CackleResponseAuthor
 * @property integer $id
 * @property string|null $email
 * @property string $name
 * @property string $avatar
 * @property string $www
 * @property string $provider
 * @property string $extWww
 * @property string $openId
 * @property boolean $verify
 * @property boolean $notify
 * @property boolean $accountId
 * @property boolean $token
 * @property boolean $signature
 * @property boolean $avatarSrc
 */
interface CackleResponseAuthor
{

}


/**
 * Interface CackleAnonym
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $hash
 */
interface CackleAnonym
{

}


/**
 * Interface CackleResponseComments
 * @property CackleResponseComment[] $content
 * @property integer $size
 * @property integer $number
 * @property array $sort
 * @property integer $totalPages
 * @property integer $numberOfElements
 * @property boolean $firstPage
 * @property boolean $lastPage
 */
interface CackleResponseComments
{

}

/**
 * Interface CackleResponseComment
 * @property integer $id
 * @property integer $siteId
 * @property integer $siteForumId
 * @property integer $sitePageId
 * @property string $url
 * @property string $title
 * @property string $channel
 * @property string $message
 * @property integer $rating
 * @property string $status
 * @property integer $created
 * @property CackleResponseAuthor $author
 * @property CackleAnonym $anonym
 * @property string $ip
 * @property string $userAgent
 * @property string $modified
 */
interface CackleResponseComment
{

}