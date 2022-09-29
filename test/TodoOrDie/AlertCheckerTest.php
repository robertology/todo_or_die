<?php
declare(strict_types=1);
// @phan-file-suppress PhanTypeMismatchArgumentProbablyReal, PhanTypeMismatchArgument

namespace Robertology\TodoOrDie\Test;

use PHPUnit\Framework\ {
  TestCase
};
use Robertology\TodoOrDie\ {
  AlertChecker,
  Cache,
  Check,
  Todo
};

class AlertCheckerTest extends TestCase {

  public function testTrueCheckReturnsTrue() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $todo = $this->getMockBuilder(Todo::class)
      ->disableOriginalConstructor()
      ->getMock();

    $alert = new AlertChecker($todo);
    $this->assertTrue($alert($check));
  }

  public function testFalseCheckReturnsFalse() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(false);

    $todo = $this->getMockBuilder(Todo::class)
      ->disableOriginalConstructor()
      ->getMock();

    $alert = new AlertChecker($todo);
    $this->assertFalse($alert($check));
  }

  public function testNoAlertIfTodoHasDied() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $todo = $this->createStub(Todo::class);
    $todo->method('hasDied')
      ->willReturn(true);

    $alert = new AlertChecker($todo);
    $this->assertFalse($alert($check));
  }

  public function testWillAlertIfTodoHasNotDied() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $todo = $this->createStub(Todo::class);
    $todo->method('hasDied')
      ->willReturn(false);

    $alert = new AlertChecker($todo);
    $this->assertTrue($alert($check));
  }

  public function testNoAlertIfRecentlyAlerted() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $cache = $this->createStub(Cache::class);
    $cache->method('getLastAlert')
      ->willReturn(time());

    $todo = $this->createStub(Todo::class);
    $todo->method('getCache')
      ->willReturn($cache);

    $alert = new AlertChecker($todo);
    $this->assertFalse($alert($check));
  }

  public function testChainedAlertDoesAlertIfRecentlyAlerted() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $cache = $this->createStub(Cache::class);
    $cache->method('getLastAlert')
      ->will($this->onConsecutiveCalls(0, time()));

    $todo = $this->createStub(Todo::class);
    $todo->method('getCache')
      ->willReturn($cache);

    $alert = new AlertChecker($todo);
    $this->assertTrue($alert($check));
    $this->assertTrue($alert($check));
  }

}
