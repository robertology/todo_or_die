<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\OverdueError as Exception;

class Todo {

  private string $_message;

  public function __construct(string $what_to_do, bool $condition_met) {
    if ($condition_met) {
      throw new Exception($what_to_do);
    }

    $this->_message = $what_to_do;
  }

  public function warnIf(bool $condition_met, callable $warn) : self {
    if ($condition_met) {
      $warn($this->_message);
    }

    return $this;
  }
}
