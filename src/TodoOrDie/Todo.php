<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\OverdueError as Exception;

class Todo {

  private bool $_died = false;
  private string $_message;

  public function __construct(string $todo_message, bool $condition_met, callable $alert = null) {
    $this->_message = $todo_message;

    // If an alert callable was given: do not die, only alert
    if (! isset($alert)) {
      $this->orIf($condition_met);
    } else {
      $this->alertIf($condition_met, $alert);
    }
  }

  protected function _die() {
    $this->_markAsDied();
    throw new Exception($this->_getMessage());
  }

  protected function _getMessage() : string {
    return $this->_message;
  }

  protected function _hasDied() : bool {
    return $this->_died;
  }

  protected function _markAsDied() {
    $this->_died = true;
  }

  protected function _shoudDie(bool $condition_met) : bool {
    return $condition_met &&
      ! $this->_hasDied() &&
      ! getenv('TODOORDIE_SKIP_DIE');
  }

  public function orIf(bool $condition_met) : self {
    if ($this->_shoudDie($condition_met)) {
      $this->_markAsDied();
      $this->_die();
    }

    return $this;
  }

  public function alertIf(bool $condition_met, callable $callable) : self {
    if (! $this->_hasDied() && $condition_met) {
      $callable($this->_getMessage());
    }

    return $this;
  }
}
