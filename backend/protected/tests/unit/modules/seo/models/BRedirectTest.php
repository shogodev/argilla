<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class BRedirectTest extends CTestCase
{
  public function testPrepareUrl()
  {
    $model = new BRedirect();
    $model->base = 'http://www.argilla.ru/info/about';
    $model->target = 'http://www.argilla.ru/contacts.html';
    $model->type_id = 1;
    $model->save();

    $this->assertEquals('/info/about/', $model->base);
    $this->assertEquals('/contacts.html', $model->target);
  }
}