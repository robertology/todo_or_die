<?php
declare(strict_types=1);
// @phan-file-suppress PhanNoopNew, PhanTypeMismatchArgumentProbablyReal, PhanUndeclaredMethodInCallable

namespace Robertology\TodoOrDie\Test;

use stdClass;
use PHPUnit\Framework\ {
  MockObject\MockObject,
  TestCase
};
use Robertology\TodoOrDie\ {
  Cache,
  Check\Defined as BooleanCheck,
  OverdueError as Exception,
  Todo
};

class TodoTest extends TestCase {

  public function setUp() : void {
    // remove this env value if set
    putenv('TODOORDIE_SKIP_DIE');

    Cache::clearAll();
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

    ExtendedTodo::setUpForTest($mock);
    (new ExtendedTodo('Some message', true))
      ->alertIf(true, [$mock, 'warn']);
    ExtendedTodo::setUpForTest();
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

  public function testAlertThrottle() {
    $mock = $this->getMockBuilder(stdClass::class)
      ->addMethods(['sendWarning'])
      ->getMock();

    $mock->expects($this->once())
      ->method('sendWarning')
      ->with('Dummy');

    Dummy::alert($mock);

    // should not expect this one to trigger due to throttling
    Dummy::alert($mock);
  }

  public function testConstructingWithNonTriggeringCheckObject() {
    $this->expectNotToPerformAssertions();

    new Todo('Some message', new BooleanCheck(false));
  }

  public function testConstructingWithTriggeringCheckObject() {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Some message');

    new Todo('Some message', new BooleanCheck(true));
  }


}

/**
 * For testing behavior of overriding methods
 */
class ExtendedTodo extends Todo {

  static private $_mock;

  static public function setUpForTest(MockObject $mock = null) {
    self::$_mock = $mock;
  }

  protected function _die() {
    // @phan-suppress-next-line PhanUndeclaredMethod
    self::$_mock->die();
  }

}

/**
 * For having a separate class create the Todo
 */
class Dummy {

  static public function alert($mock) {
    new Todo('Dummy', true, [$mock, 'sendWarning']);
  }

}
