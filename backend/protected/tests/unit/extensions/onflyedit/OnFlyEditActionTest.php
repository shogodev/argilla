<?php
class OnFlyEditActionTest extends CTestCase
{
  public function setUp()
  {

  }

  public function testParseGridId()
  {
    $action = new OnFlyEditAction(null, 'null');
    $method = new ReflectionMethod('OnFlyEditAction', 'parseGridId');
    $method->setAccessible(true);

    $model = $method->invoke($action, 'BGallery_gallery_image-files');
    $this->assertEquals('UploadModel', get_class($model));

    $model = $method->invoke($action, 'BCounters-files');
    $this->assertEquals('BCounters', get_class($model));
  }
}