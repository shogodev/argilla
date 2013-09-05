<?php
class SUriValidatorTest extends CTestCase
{
  public function testValidateAttribute()
  {
    $model = new Product();

    $validator = new SUriValidator;
    $validator->attributes = array('url');

    $this->setUrl($model, null);
    $validator->validate($model);
    $this->assertFalse($model->hasErrors('url'));

    $this->setUrl($model, 'testUrl_123');
    $validator->validate($model);
    $this->assertFalse($model->hasErrors('url'));

    $this->setUrl($model, 'тест');
    $validator->validate($model);
    $this->assertTrue($model->hasErrors('url'));

    $this->setUrl($model, 'test-test');
    $validator->validate($model);
    $this->assertTrue($model->hasErrors('url'));
  }

  protected function setUrl(Product $model, $url)
  {
    $model->clearErrors();
    $model->url = $url;
  }
}