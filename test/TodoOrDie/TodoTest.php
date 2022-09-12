<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test;

use stdClass;
use PHPUnit\Framework\TestCase;
use Robertology\TodoOrDie\ {
  OverdueError as Exception,
  Todo
};

class TodoTest extends TestCase {

  public function testDoNotDie() {
    $this->expectNotToPerformAssertions();

    new Todo('Some message', false);
  }

  public function testDoesDie() {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Some message');

    new Todo('Some message', true);
  }

  public function testItWarns() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning'])
      ->getMock();

    $mock->expects($this->once())
      ->method('sendWarning')
      ->with('Some message');

    (new Todo('Some message', false))
      ->warnIf(true, [$mock, 'sendWarning']);
  }

  public function testItDoesNotWarn() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning'])
      ->getMock();

    $mock->expects($this->never())
      ->method('sendWarning');

    (new Todo('Some message', false))
      ->warnIf(false, [$mock, 'sendWarning']);
  }

  public function testItWarnsOnAnotherCondition() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['noSendWarning', 'sendWarning'])
      ->getMock();

    $mock->expects($this->never())
      ->method('noSendWarning');
    $mock->expects($this->once())
      ->method('sendWarning')
      ->with('Some message');

    (new Todo('Some message', false))
      ->warnIf(false, [$mock, 'noSendWarning'])
      ->warnIf(true, [$mock, 'sendWarning']);
  }

  public function testItWarnsOnAllConditions() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning1', 'sendWarning2'])
      ->getMock();

    $mock->expects($this->once())
      ->method('sendWarning1')
      ->with('Some message');
    $mock->expects($this->once())
      ->method('sendWarning2')
      ->with('Some message');

    (new Todo('Some message', false))
      ->warnIf(true, [$mock, 'sendWarning1'])
      ->warnIf(true, [$mock, 'sendWarning2']);
  }

}
