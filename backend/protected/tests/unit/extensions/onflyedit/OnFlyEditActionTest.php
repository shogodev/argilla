<?php
class OnFlyEditActionTest extends CTestCase
{
  public function testParseGridId()
  {
    $action = new OnFlyEditAction(null, 'null');
    $method = new ReflectionMethod('OnFlyEditAction', 'parseGridId');
    $method->setAccessible(true);

    $model = $method->invoke($action, 'BGallery_gallery_image-files');
    $this->assertInstanceOf('UploadModel', $model);

    $model = $method->invoke($action, 'BCounters-files');
    $this->assertInstanceOf('BCounters', $model);
  }
}