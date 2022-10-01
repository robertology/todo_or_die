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

  /**
   * @dataProvider alertThresholdValues
   */
  public function testAlertThresholdConfigurable(
    bool $will_alert,
    string|int $setting,
    int $last_alert
  ) {
    $check = $this->createStub(Check::class);
    $check->method('__invoke')
      ->willReturn(true);

    $state = $this->createStub(TodoState::class);
    $state->method('hasAlerted')
      ->willReturn(false);
    $state->method('getLastAlert')
      ->willReturn($last_alert);

    putenv("TODOORDIE_ALERT_THRESHOLD={$setting}");
    $alert = new AlertChecker($state);
    $this->assertSame($will_alert, $alert($check));

    putenv('TODOORDIE_ALERT_THRESHOLD');
  }

  /**
   * Data Provider
   *
   * should_alert, setting, [last_alert_timestamp = time()]
   */
  public function alertThresholdValues() : array {
    $default = strtotime('-1 hour');

    return [
      [true, 86400, strtotime('-3 day')],
      [false, 86400, strtotime('-3 hour')],
      [true, 10, strtotime('-30 minute')],
      [false, 10, strtotime('-1 second')],

      // Invalid values should use the default
      [true, 'asdf', strtotime('-3 minute', $default)],
      [false, 'asdf', strtotime('+3 minute', $default)],
      [true, '84600asdf', strtotime('-3 minute', $default)],
      [false, '84600asdf', strtotime('+3 minute', $default)],
      [true, -84600, strtotime('-3 minute', $default)],
      [false, -84600, strtotime('+3 minute', $default)],
      [true, '', strtotime('-3 minute', $default)],
      [false, '', strtotime('+3 minute', $default)],
    ];
  }

}
