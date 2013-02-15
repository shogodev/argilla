<?php
/**
 * User: glagolev
 * Date: 15.08.12
 */
class AutentificationTest extends WebTestCase
{
  protected function setUp()
  {
    parent::setUp();
    $this->setBrowser('*firefox');
  }

  public function testShow()
  {
    $this->open('backend/');
  }
}

?>