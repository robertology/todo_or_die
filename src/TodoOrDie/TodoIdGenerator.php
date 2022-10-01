<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  System\Trace,
  Todo,
};

class TodoIdGenerator {

  private Todo $_todo;

  public function __construct(Todo $todo) {
    $this->_todo = $todo;
  }

  public function __invoke() : string {
    $entry = Trace::fromOutside(get_class($this->_todo))[0] ?? [];

    $file = $entry['file'] ?? '';
    $line = $entry['line'] ?? 0;

    return "{$file}:{$line}";
  }

}
