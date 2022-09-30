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
  TodoState,
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

    $state = $this->getMockBuilder(TodoState::class)
      ->disableOriginalConstructor()
      ->getMock();

    $checker = new DieChecker($state);
    $this->assertTrue($checker($check));
  }

  public function testFalseCheckReturnsFalse() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(false);

    $state = $this->getMockBuilder(TodoState::class)
      ->disableOriginalConstructor()
      ->getMock();

    $checker = new DieChecker($state);
    $this->assertFalse($checker($check));
  }

  public function testItDoesNotDieWhenEnvIsSet() {
    putenv('TODOORDIE_SKIP_DIE=1');

    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->getMockBuilder(TodoState::class)
      ->disableOriginalConstructor()
      ->getMock();

    $checker = new DieChecker($state);
    $this->assertFalse($checker($check));
  }

  public function testOnlyDieOnce() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->createStub(TodoState::class);
    $state->method('hasDied')
      ->will($this->onConsecutiveCalls(false, true));

    $checker = new DieChecker($state);
    $this->assertTrue($checker($check));
    $this->assertFalse($checker($check));
  }

}
