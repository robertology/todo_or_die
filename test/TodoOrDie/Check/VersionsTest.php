<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test\Check;

use PHPUnit\Framework\ {
  MockObject\MockObject,
  TestCase
};
use Robertology\TodoOrDie\Check\Versions;

class VersionsTest extends TestCase {

  public function testVersionLessThanIsFalse() {
    $check = new Versions('1.2', '2.0');
    $this->assertFalse($check());
  }

  public function testVersionGreaterThanIsTrue() {
    $check = new Versions('2.2', '2.0');
    $this->assertTrue($check());
  }

  public function testVersionEqualToIsTrue() {
    $check = new Versions('2.2.6', '2.2.6');
    $this->assertTrue($check());
  }

  public function testComparisonOperator() {
    $check = new Versions('1.2', '2.0', '<');
    $this->assertTrue($check());

    $check = new Versions('2.0', '2.0', '=');
    $this->assertTrue($check());

    $check = new Versions('2.2', '2.0', '>');
    $this->assertTrue($check());
  }

  public function testPhpVersion() {
    $check = Versions::php('4.1');
    $this->assertFalse($check());

    $check = Versions::php('17.1');
    $this->assertTrue($check());
  }

}
