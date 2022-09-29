<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Check;

use Robertology\TodoOrDie\Check as CheckInterface;

class Dates implements CheckInterface {

  private int $_timestamp;

  public function __construct(int $timestamp) {
    $this->_timestamp = $timestamp;
  }

  public function __invoke() : bool {
    return $this->_timestamp <= time();
  }

  static public function fromString(string $string) : static {
    return new static(strtotime($string));
  }

}
