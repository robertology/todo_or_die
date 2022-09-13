<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Check;

class Dates {

  private int $_timestamp;

  public function __construct(int $timestamp) {
    $this->_timestamp = $timestamp;
  }

  public function __invoke() : bool {
    return $this->_timestamp <= time();
  }
}
