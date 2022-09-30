<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  Check,
  TodoState,
};

class AlertChecker {

  private TodoState $_todo_state;

  public function __construct(TodoState $todo_state) {
    $this->_todo_state = $todo_state;
  }

  public function __invoke(Check $check) : bool {
    return ! $this->_todo_state->hasDied() &&
      $check() &&
      ($this->_todo_state->hasAlerted() || ! $this->_todo_state->hasRecentlyAlerted());
  }

}
