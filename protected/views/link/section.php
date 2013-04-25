<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 *
 * @var LinkController $this
 * @var FActiveDataProvider $dataProvider
 *
 * @var Link[] $links
 * @var FFixedPageCountPagination $pages
 */
foreach( $links as $link )
{
  $this->renderPartial('link_link', ['link' => $link]);
}

$this->widget('FLinkPager', ['pages' => $pages]);