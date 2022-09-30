<?php
declare(strict_types=1);
// @phan-file-suppress PhanTypeMismatchArgumentProbablyReal, PhanTypeMismatchArgument

namespace Robertology\TodoOrDie\Test;

use PHPUnit\Framework\ {
  TestCase
};
use Robertology\TodoOrDie\ {
  TodoState,
  Cache,
  Check,
  Todo
};

class TodoStateTest extends TestCase {

  public function testShouldAlertReturnsTrue() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $todo = $this->getMockBuilder(Todo::class)
      ->disableOriginalConstructor()
      ->getMock();

    $state = new TodoState($todo);
    $this->assertTrue($state->shouldAlert($check));
  }

  public function testShouldAlertReturnsFalse() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(false);

    $todo = $this->getMockBuilder(Todo::class)
      ->disableOriginalConstructor()
      ->getMock();

    $state = new TodoState($todo);
    $this->assertFalse($state->shouldAlert($check));
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

    $state = new TodoState($todo);
    $this->assertFalse($state->shouldAlert($check));
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

    $state = new TodoState($todo);
    $this->assertTrue($state->shouldAlert($check));
    $this->assertTrue($state->shouldAlert($check));
  }

  public function testNoAlertIfTodoHasDied() {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $todo = $this->getMockBuilder(Todo::class)
      ->disableOriginalConstructor()
      ->getMock();

    $state = new TodoState($todo);
    $this->assertTrue($state->shouldDie($check));
    $this->assertFalse($state->shouldDie($check));
  }

}
