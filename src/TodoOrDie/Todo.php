<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Exception;

class Todo {

  public function __construct(string $what_to_do, bool $condition_met) {
    if ($condition_met) {
      throw new Exception($what_to_do);
    }
  }
}
