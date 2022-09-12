<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\OverdueError as Exception;

class Todo {

  private string $_message;

  public function __construct(string $what_to_do, bool $condition_met) {
    $this->_message = $what_to_do;

    if ($condition_met) {
      $this->_die();
    }
  }

  protected function _die() {
    throw new Exception($this->_message);
  }

  public function orIf(bool $condition_met) : self {
    if ($condition_met) {
      $this->_die();
    }

    return $this;
  }

  public function warnIf(bool $condition_met, callable $warn) : self {
    if ($condition_met) {
      $warn($this->_message);
    }

    return $this;
  }
}
