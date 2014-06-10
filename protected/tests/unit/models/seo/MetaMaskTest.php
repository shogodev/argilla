<?php
/**
 * @author Sergey Glagolev <glagolev@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 */
class MetaMaskTest extends CDbTestCase
{
  protected $fixtures = array(
    'seo_meta_mask' => 'MetaMask',
  );

  public function testFindByUri()
  {
    $model = MetaMask::model()->findByUri('/parketnaya_doska/price/0-1500/');
    $this->assertNotNull($model);
  }
}