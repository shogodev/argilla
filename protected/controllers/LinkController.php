<?php
/**
 * @author Nikita Melnikov <nickswdit@gmail.com>
 * @author Vladimir Utenkov <utenkov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2013 Shogo
 * @license http://argilla.ru/LICENSE
 */
class LinkController extends FController
{
  public function actionIndex()
  {
    $this->render('index', [
      'dataProvider' => new FActiveDataProvider('LinkSection'),
    ]);
  }

  /**
   * @param string $url
   * @param int $page
   *
   * @throws CHttpException
   */
  public function actionSection($url, $page)
  {
    /** @var $section LinkSection */
    $section = LinkSection::model()->whereUrl($url)->find();
    if( $section === null )
    {
      throw new CHttpException(404);
    }

    $this->breadcrumbs = [
      'Каталог ссылок' => $this->createUrl('link/index'),
      $section->name,
    ];

    $pages = new FFixedPageCountPagination($section->pageCount);

    /** @var $links Link[] */
    $links = $section->getLinksOnPage($page);

    $this->render('section', [
      'links' => $links,
      'pages' => $pages,
    ]);
  }

  public function actionAdd()
  {
    $form = new FForm('LinkForm', new Link());

    $this->render('add', array(
      'form' => $form,
    ));
  }
}