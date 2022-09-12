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

}
