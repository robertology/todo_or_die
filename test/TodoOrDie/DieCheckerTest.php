<?php
declare(strict_types=1);
// @phan-file-suppress PhanTypeMismatchArgumentProbablyReal, PhanTypeMismatchArgument

namespace Robertology\TodoOrDie\Test;

use PHPUnit\Framework\ {
  TestCase
};
use Robertology\TodoOrDie\ {
  DieChecker,
  Check,
  Todo
};

class DieCheckerTest extends TestCase {

  public function setUp() : void {
    // remove this env value if set
    putenv('TODOORDIE_SKIP_DIE');
  }

  public function testTrueCheckReturnsTrue() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $checker = new DieChecker();
    $this->assertTrue($checker($check));
  }

  public function testFalseCheckReturnsFalse() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(false);

    $checker = new DieChecker();
    $this->assertFalse($checker($check));
  }

  public function testItDoesNotDieWhenEnvIsSet() {
    putenv('TODOORDIE_SKIP_DIE=1');

    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $checker = new DieChecker();
    $this->assertFalse($checker($check));
  }

  public function testOnlyDieOnce() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $checker = new DieChecker();
    $this->assertTrue($checker($check));
    $this->assertFalse($checker($check));
  }

}
