<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  Check,
  Check\Defined as BooleanCheck,
  OverdueError as Exception,
  TodoState,
};

class Todo {

  private string $_id;
  private string $_message;
  private TodoState $_state;

  public function __construct(string $todo_message, bool|Check $check, callable $alert = null) {
    $this->_id = $this->_generateId();
    $this->_message = $todo_message;
    $check = $this->_coerceToCheckObject($check);

    // If an alert callable was given: do not die, only alert
    if (! isset($alert)) {
      $this->_dieIf($check);
    } else {
      $this->alertIf($check, $alert);
    }
  }

  public function alertIf(bool|Check $check, callable $callable) : self {
    $check = $this->_coerceToCheckObject($check);
    if ($this->_shouldAlert($check)) {
      $this->_getState()->recordAlert();
      $callable($this->_getMessage());
    }

    return $this;
  }

  public function getId() : string {
    return $this->_id;
  }

  public function hasDied() : bool {
    return $this->_getState()->hasDied();
  }

  protected function _coerceToCheckObject(bool|Check $check) : Check {
    return is_bool($check) ? new BooleanCheck($check) : $check;
  }

  protected function _die() {
    $this->_getState()->recordDie();
    throw new Exception($this->_getMessage());
  }

  protected function _dieIf(Check $check) : self {
    if ($this->_shouldDie($check)) {
      $this->_die();
    }

    return $this;
  }

  protected function _generateId() : string {
    // The first entry will be the call to this method; use the second one
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? [];
    $file = $trace['file'] ?? '';
    $line = $trace['line'] ?? 0;

    return "{$file}:{$line}";
  }

  protected function _getState() : TodoState {
    return $this->_state = ($this->_state ?? new TodoState($this));
  }

  protected function _getMessage() : string {
    return $this->_message;
  }

  protected function _shouldAlert(Check $check) : bool {
    return $this->_getState()->shouldAlert($check);
  }

  protected function _shouldDie(Check $check) : bool {
    return $this->_getState()->shouldDie($check);
  }

}
