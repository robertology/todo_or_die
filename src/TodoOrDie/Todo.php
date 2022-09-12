<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\OverdueError as Exception;

class Todo {

  private bool $_died = false;
  private string $_message;

  public function __construct(string $what_to_do, bool $condition_met) {
    $this->_message = $what_to_do;

    if ($condition_met) {
      $this->_markAsDied();
      $this->_die();
    }
  }

  protected function _die() {
    $this->_markAsDied();
    throw new Exception($this->_message);
  }

  protected function _hasDied() : bool {
    return $this->_died;
  }

  protected function _markAsDied() {
    $this->_died = true;
  }

  public function orIf(bool $condition_met) : self {
    if (! $this->_hasDied() && $condition_met) {
      $this->_markAsDied();
      $this->_die();
    }

    return $this;
  }

  public function warnIf(bool $condition_met, callable $warn) : self {
    if (! $this->_hasDied() && $condition_met) {
      $warn($this->_message);
    }

    return $this;
  }
}
