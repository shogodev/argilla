<?php
/**
 * @author Alexey Tatarivov <tatarinov@shogo.ru>
 * @link https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license http://argilla.ru/LICENSE
 * @package 
 */
class UserProfileTest extends CTestCase
{
  /**
   * @expectedException CDbException
   */
  public function testRegistrationFailed()
  {
    $profile = new UserProfile();
    $profile->attributes = array('user_id' => '1000', 'phone' => '8888');
    $profile->save();
  }

  public function testRegistrationSuccess()
  {
    $user = new User();
    $user->id = 1000;
    $user->save(false);

    $profile = new UserProfile();
    $profile->user_id = 1000;
    $profile->attributes = array('phone' => '8888');
    $this->assertTrue($profile->save());
  }

  public function testChangeDataFailed()
  {
    /**
     * @var UserProfile $profile
     */
    $profile = UserProfile::model()->findByPk(2000);

    $this->assertEquals('profile test', $profile->name);
    $this->assertEquals('99999', $profile->phone);

    $profile->attributes = array('name' => '', 'phone' => '5555');
    $this->assertFalse($profile->save());
  }

  public function testChangeDataSuccess()
  {
    /**
     * @var UserProfile $profile
     */
    $profile = UserProfile::model()->findByPk(2000);

    $this->assertEquals('profile test', $profile->name);
    $this->assertEquals('99999', $profile->phone);

    $profile->attributes = array('name' => 'test name', 'phone' => '5555');
    $profile->save();

    /**
     * @var UserProfile $model
     */
    $model = UserProfile::model()->findByPk(2000);
    $this->assertEquals('test name', $model->name);
    $this->assertEquals('5555', $model->phone);
  }

  public function tearDown()
  {
    User::model()->deleteAllByAttributes(array('id' => 1000));
  }
} 