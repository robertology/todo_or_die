<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  Check,
  Todo
};

class AlertChecker {

  private const _ALERT_THROTTLE_THRESHOLD = '1 hour';

  private bool $_alerted = false;
  private Todo $_todo;

  public function __construct(Todo $todo) {
    $this->_todo = $todo;
  }

  public function __invoke(Check $check) : bool {
    $alerted = ! $this->_todo->hasDied() &&
      $check() &&
      ($this->_hasAlerted() || ! $this->_hasRecentlyAlerted());

    if ($alerted) {
      $this->_alerted = true;
      $this->_todo->getCache()->setLastAlert(time());
    }

    return $alerted;
  }

  protected function _hasAlerted() : bool {
    return $this->_alerted;
  }

  protected function _hasRecentlyAlerted() : bool {
    $last_alert = $this->_todo->getCache()->getLastAlert() ?? 0;
    return $last_alert >= strtotime('-' . static::_ALERT_THROTTLE_THRESHOLD);
  }

}
