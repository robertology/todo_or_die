<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test;

use stdClass;
use PHPUnit\Framework\ {
  MockObject\MockObject,
  TestCase
};
use Robertology\TodoOrDie\ {
  OverdueError as Exception,
  Todo
};

class TodoTest extends TestCase {

  public function setUp() : void {
    // remove this env value if set
    putenv('TODOORDIE_SKIP_DIE');
  }

  public function testDoNotDie() {
    $this->expectNotToPerformAssertions();

    new Todo('Some message', false);
  }

  public function testDoesDie() {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Some message');

    new Todo('Some message', true);
  }

  public function testItDiesOnAnotherCondition() {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Some message');

    (new Todo('Some message', false))
      ->orIf(false)
      ->orIf(true);
  }

  public function testItOnlyDiesOnce() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['die'])
      ->getMock();

    $mock->expects($this->once())
      ->method('die');

    (new ExtendedTodo('Some message', false))
      ->setUpForTest($mock)
      ->orIf(true)
      ->orIf(true);
  }

  public function testItAlerts() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning'])
      ->getMock();

    $mock->expects($this->once())
      ->method('sendWarning')
      ->with('Some message');

    (new Todo('Some message', false))
      ->alertIf(true, [$mock, 'sendWarning']);
  }

  public function testItDoesNotAlert() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning'])
      ->getMock();

    $mock->expects($this->never())
      ->method('sendWarning');

    (new Todo('Some message', false))
      ->alertIf(false, [$mock, 'sendWarning']);
  }

  public function testItAlertsOnAnotherCondition() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['noSendWarning', 'sendWarning'])
      ->getMock();

    $mock->expects($this->never())
      ->method('noSendWarning');
    $mock->expects($this->once())
      ->method('sendWarning')
      ->with('Some message');

    (new Todo('Some message', false))
      ->alertIf(false, [$mock, 'noSendWarning'])
      ->alertIf(true, [$mock, 'sendWarning']);
  }

  public function testItAlertsOnAllConditions() {
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
      ->alertIf(true, [$mock, 'sendWarning1'])
      ->alertIf(true, [$mock, 'sendWarning2']);
  }

  public function testItDoesNotAlertIfDied() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['die', 'warn'])
      ->getMock();

    $mock->expects($this->once())
      ->method('die');
    $mock->expects($this->never())
      ->method('warn');

    (new ExtendedTodo('Some message', false))
      ->setUpForTest($mock)
      ->orIf(true)
      ->alertIf(true, [$mock, 'warn']);
  }

  public function testItDoesNotDieWhenEnvIsSet() {
    $this->expectNotToPerformAssertions();

    putenv('TODOORDIE_SKIP_DIE=1');

    new Todo('Some message', true);
  }

  public function testItOnlyAlerts() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning'])
      ->getMock();

    $mock->expects($this->once())
      ->method('sendWarning')
      ->with('Some message');

    new Todo('Some message', true, [$mock, 'sendWarning']);
  }

}

/**
 * For testing behavior of overriding methods
 */
class ExtendedTodo extends Todo {

  private $_mock;

  protected function _die() {
    $this->_mock->die();
  }

  public function setUpForTest(MockObject $mock) {
    $this->_mock = $mock;
    return $this;
  }
}
