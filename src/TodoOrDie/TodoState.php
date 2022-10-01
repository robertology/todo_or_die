<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  AlertChecker,
  Cache,
  Check,
  Todo
};

class TodoState {

  private AlertChecker $_alert_checker;
  private DieChecker $_die_checker;
  private array $_events = ['alert' => 0, 'die' => 0];
  private Todo $_todo;

  public function __construct(Todo $todo) {
    $this->_todo = $todo;
  }

  public function getLastAlert() : int {
    return (int)($this->_getCache()->getLastAlert() ?? 0);
  }

  public function hasAlerted() : bool {
    return $this->_events['alert'] > 0;
  }

  public function hasDied() : bool {
    return $this->_events['die'] > 0;
  }

  public function recordAlert() {
    $this->_events['alert']++;
    $this->_getCache()->setLastAlert(time());
  }

  public function recordDie() {
    $this->_events['die']++;
  }

  public function shouldAlert(Check $check) : bool {
    return $this->_getAlertChecker()($check);
  }

  public function shouldDie(Check $check) : bool {
    return $this->_getDieChecker()($check);
  }

  protected function _getAlertChecker() : AlertChecker {
    return $this->_alert_checker  = ($this->_alert_checker ?? new AlertChecker($this));
  }

  protected function _getCache() : Cache {
    return new Cache($this->_todo);
  }

  protected function _getDieChecker() : DieChecker {
    return $this->_die_checker  = ($this->_die_checker ?? new DieChecker($this));
  }

}
