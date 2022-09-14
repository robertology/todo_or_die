<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test\Check;

use PHPUnit\Framework\TestCase;
use Robertology\TodoOrDie\Check\Defined;

class DefinedTest extends TestCase {

  public function testIsTrue() {
    $check = new Defined(true);
    $this->assertTrue($check());
  }

  public function testIsFalse() {
    $check = new Defined(false);
    $this->assertFalse($check());
  }

}
