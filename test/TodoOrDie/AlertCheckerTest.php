<?php
declare(strict_types=1);
// @phan-file-suppress PhanTypeMismatchArgumentProbablyReal, PhanTypeMismatchArgument

namespace Robertology\TodoOrDie\Test;

use PHPUnit\Framework\ {
  TestCase
};
use Robertology\TodoOrDie\ {
  AlertChecker,
  Check,
  TodoState,
};

class AlertCheckerTest extends TestCase {

  public function testTrueCheckReturnsTrue() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->getMockBuilder(TodoState::class)
      ->disableOriginalConstructor()
      ->getMock();

    $alert = new AlertChecker($state);
    $this->assertTrue($alert($check));
  }

  public function testFalseCheckReturnsFalse() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(false);

    $state = $this->getMockBuilder(TodoState::class)
      ->disableOriginalConstructor()
      ->getMock();

    $alert = new AlertChecker($state);
    $this->assertFalse($alert($check));
  }

  public function testNoAlertIfTodoHasDied() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->createStub(TodoState::class);
    $state->method('hasDied')
      ->willReturn(true);

    $alert = new AlertChecker($state);
    $this->assertFalse($alert($check));
  }

  public function testWillAlertIfTodoHasNotDied() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->createStub(TodoState::class);
    $state->method('hasDied')
      ->willReturn(false);

    $alert = new AlertChecker($state);
    $this->assertTrue($alert($check));
  }

  public function testNoAlertIfRecentlyAlerted() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->createStub(TodoState::class);
    $state->method('hasAlerted')
      ->willReturn(false);
    $state->method('getLastAlert')
      ->willReturn(time());

    $alert = new AlertChecker($state);
    $this->assertFalse($alert($check));
  }

  public function testChainedAlertDoesAlertIfRecentlyAlerted() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->createStub(TodoState::class);
    $state->method('hasAlerted')
      ->will($this->onConsecutiveCalls(false, true));
    $state->method('getLastAlert')
      ->will($this->onConsecutiveCalls(0, time()));

    $alert = new AlertChecker($state);
    $this->assertTrue($alert($check));
    $this->assertTrue($alert($check));
  }

}
