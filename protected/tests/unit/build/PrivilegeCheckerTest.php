<?php
/**
 * @author    Vladimir Utenkov <utenkov@shogo.ru>
 * @link      https://github.com/shogodev/argilla/
 * @copyright Copyright &copy; 2003-2014 Shogo
 * @license   http://argilla.ru/LICENSE
 */
require_once Yii::getPathOfAlias('frontend').DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'build'.DIRECTORY_SEPARATOR.'tasks'.DIRECTORY_SEPARATOR.'CheckDbTask.php';

class PrivilegeCheckerTest extends CTestCase
{
  public function testHasPrivilege()
  {
    $checker = new PrivilegeChecker($this->getShowGrantsRawOutput());

    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::TRIGGER, 'cuberussia2013', 'cuberussia2013', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::SHOW_VIEW, 'cuberussia2013', 'cuberussia2013', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::CREATE_VIEW, 'cuberussia2013', 'cuberussia2013', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::CREATE_ROUTINE, 'cuberussia2013', 'cuberussia2013', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::ALTER_ROUTINE, 'cuberussia2013', 'cuberussia2013', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::LOCK_TABLES, 'cuberussia2013', 'cuberussia2013', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::CREATE_TEMPORARY_TABLES, 'cuberussia2013', 'cuberussia2013', 'localhost'));

    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::CREATE_TEMPORARY_TABLES, '*', 'prog', 'localhost'));

    $this->assertFalse($checker->hasPrivilege(PrivilegeEnum::TRIGGER, 'kalenji', 'kalenji', 'localhost'));

    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::TRIGGER, 'new_project', 'root', 'localhost'));
    $this->assertTrue($checker->hasPrivilege(PrivilegeEnum::TRIGGER, 'new_project', 'root', '123.123.123.123'));
    $this->assertFalse($checker->hasPrivilege(PrivilegeEnum::TRIGGER, 'cuberussia2013', 'cuberussia2013', '123.123.123.123'));
  }

  /**
   * @return array
   */
  private function getShowGrantsRawOutput()
  {
    return array(
      array(
        'Grants for kalenji@localhost' => "GRANT USAGE ON *.* TO 'kalenji'@'localhost' IDENTIFIED BY PASSWORD '*6A0BED15E93743BE29A176B410CA53734C4EF768'",
        "GRANT USAGE ON *.* TO 'kalenji'@'localhost' IDENTIFIED BY PASSWORD '*6A0BED15E93743BE29A176B410CA53734C4EF768'",
      ),
      array(
        'Grants for kalenji@localhost' => "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE ON `kalenji`.* TO 'kalenji'@'localhost'",
        "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE ON `kalenji`.* TO 'kalenji'@'localhost'",
      ),
      array(
        'Grants for cuberussia2013@localhost' => "GRANT USAGE ON *.* TO 'cuberussia2013'@'localhost' IDENTIFIED BY PASSWORD '*A099BC057F9047DF2B250C89B19EA06FEEB5B2DB'",
        "GRANT USAGE ON *.* TO 'cuberussia2013'@'localhost' IDENTIFIED BY PASSWORD '*A099BC057F9047DF2B250C89B19EA06FEEB5B2DB'",
      ),
      array(
        'Grants for cuberussia2013@localhost' => "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON `cuberussia2013`.* TO 'cuberussia2013'@'localhost'",
        "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON `cuberussia2013`.* TO 'cuberussia2013'@'localhost'",
      ),
      array(
        'Grants for prog@localhost' => "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, SHOW DATABASES, SUPER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON *.* TO 'prog'@'localhost' IDENTIFIED BY PASSWORD '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257'",
        "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, SHOW DATABASES, SUPER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON *.* TO 'prog'@'localhost' IDENTIFIED BY PASSWORD '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257'"
      ),
      array(
        'Grants for prog@localhost' => "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, SHOW DATABASES, SUPER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON *.* TO 'prog'@'localhost' IDENTIFIED BY PASSWORD '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257'",
        "GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, FILE, INDEX, ALTER, SHOW DATABASES, SUPER, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE, CREATE VIEW, SHOW VIEW, CREATE ROUTINE, ALTER ROUTINE, EVENT, TRIGGER ON *.* TO 'prog'@'localhost' IDENTIFIED BY PASSWORD '*23AE809DDACAF96AF0FD78ED04B6A265E05AA257'"
      ),
      array(
        'All privilegies' => "GRANT ALL PRIVILEGES ON *.* TO 'root'@'localhost' WITH GRANT OPTION"
      ),
      array(
        'All privilegies 2' => "GRANT ALL ON *.* TO 'root'@'localhost' WITH GRANT OPTION"
      ),
      array(
        'All privilegies and all hosts' => "GRANT ALL ON *.* TO 'root'@'%' WITH GRANT OPTION"
      ),
    );
  }
}