<?php
/**
 * @author Alexey Tatarinov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package frontend.tests.unit.components.validators
 */
Yii::import('frontend.tests.components.ValidatorTestModel');

class LoginValidatorTest extends CTestCase
{
  public function testValidate()
  {
    $testModel = new ValidatorTestModel();

    $testModel->login = 'q';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = str_repeat('э', LoginValidator::MAX_LENGTH + 1);
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'qw';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'qwertyuiopasdfgh';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'qwertyuiopasdfg';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = '123tyasdfg';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'йцуке4567';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'йцуе890гщш';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'englishруский';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'АаБбВвГгДдЕеЁё';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'ЖжЗзИиЙйКкЛлМм';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'НнОоПпРрСсТтУу';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'ФфХхЦцЧчШшЩщЪъ';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'ЫыЬьЭэЮюЯя';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'ЫыЬь╤ЭэЮюЯя';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'AaBbCcDdEeFfGg';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'HhIiJjKkLlMmNn';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'OoPpQqRrSsTtUu';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'VvWwXxYyZz';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'sa-sdf sa';
    $this->assertTrue($testModel->validate('login', true));

    $testModel->login = 'sa-sd sa_f';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = '_sasdf';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = ' sasdf';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = '-sasdf';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'sasdf_';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'sasdf ';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'sasdf-';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'qwe--rty';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'qwe  rty';
    $this->assertFalse($testModel->validate('login', true));

    $testModel->login = 'qwe__rty';
    $this->assertFalse($testModel->validate('login', true));
  }
}