<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\Check;

class DieChecker {

  private TodoState $_todo_state;

  public function __construct(TodoState $todo_state) {
    $this->_todo_state = $todo_state;
  }

  public function __invoke(Check $check) : bool {
    return ! $this->_todo_state->hasDied() &&
      ! getenv('TODOORDIE_SKIP_DIE') &&
      $check();
  }

}
