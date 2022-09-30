<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  AlertChecker,
  Check,
  Todo
};

class TodoState {

  private const _ALERT_THROTTLE_THRESHOLD = '1 hour';

  private AlertChecker $_alert_checker;
  private bool $_alerted = false;
  private DieChecker $_die_checker;
  private bool $_died = false;
  private Todo $_todo;

  public function __construct(Todo $todo) {
    $this->_todo = $todo;
  }

  public function hasAlerted() : bool {
    return $this->_alerted;
  }

  public function hasDied() : bool {
    return $this->_died;
  }

  public function hasRecentlyAlerted() : bool {
    $last_alert = $this->_todo->getCache()->getLastAlert() ?? 0;
    return $last_alert >= strtotime('-' . static::_ALERT_THROTTLE_THRESHOLD);
  }

  public function shouldAlert(Check $check) : bool {
    $result = $this->_getAlertChecker()($check);

    if ($result) {
      $this->_alerted = true;
      $this->_todo->getCache()->setLastAlert(time());
    }

    return $result;
  }

  public function shouldDie(Check $check) : bool {
    $result = $this->_getDieChecker()($check);

    if ($result) {
      $this->_died = true;
    }

    return $result;
  }

  protected function _getAlertChecker() : AlertChecker {
    return $this->_alert_checker  = ($this->_alert_checker ?? new AlertChecker($this->_todo));
  }

  protected function _getDieChecker() : DieChecker {
    return $this->_die_checker  = ($this->_die_checker ?? new DieChecker());
  }

}
