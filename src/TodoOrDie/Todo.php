<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  Cache,
  OverdueError as Exception
};

class Todo {

  private bool $_alerted = false;
  private bool $_died = false;
  private string $_id;
  private string $_message;

  public function __construct(string $todo_message, bool $condition_met, callable $alert = null) {
    $this->_id = $this->_generateId();
    $this->_message = $todo_message;

    // If an alert callable was given: do not die, only alert
    if (! isset($alert)) {
      $this->_dieIf($condition_met);
    } else {
      $this->alertIf($condition_met, $alert);
    }
  }

  public function alertIf(bool $condition_met, callable $callable) : self {
    if ($this->_shouldAlert($condition_met)) {
      $this->_markAsAlerted();
      $callable($this->_getMessage());
    }

    return $this;
  }

  protected function _dieIf(bool $condition_met) : self {
    if ($this->_shouldDie($condition_met)) {
      $this->_markAsDied();
      $this->_die();
    }

    return $this;
  }

  public function getId() : string {
    return $this->_id;
  }

  protected function _die() {
    $this->_markAsDied();
    throw new Exception($this->_getMessage());
  }

  protected function _generateId() : string {
    // The first entry will be the call to this method; use the second one
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? [];
    $file = $trace['file'] ?? '';
    $line = $trace['line'] ?? 0;

    return "{$file}:{$line}";
  }

  protected function _getCache() : Cache {
    return new Cache($this);
  }

  protected function _getMessage() : string {
    return $this->_message;
  }

  protected function _hasDied() : bool {
    return $this->_died;
  }

  protected function _hasRecentlyAlerted() : bool {
    $last_alert = $this->_getCache()->getLastAlert() ?? 0;
    return $last_alert >= strtotime('-1 hour');
  }

  protected function _markAsAlerted() {
    $this->_getCache()->setLastAlert(time());
    $this->_alerted = true;
  }

  protected function _markAsDied() {
    $this->_died = true;
  }

  protected function _shouldAlert(bool $condition_met) : bool {
    return $condition_met &&
      ! $this->_hasDied() &&
      // If this has chained alerts, don't let one of them throttle the others in the same run
      ($this->_alerted || ! $this->_hasRecentlyAlerted());
  }

  protected function _shouldDie(bool $condition_met) : bool {
    return $condition_met &&
      ! $this->_hasDied() &&
      ! getenv('TODOORDIE_SKIP_DIE');
  }

}
