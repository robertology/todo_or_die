<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Check;

use Robertology\TodoOrDie\Check as CheckInterface;

class Defined implements CheckInterface {

  private bool $_met;

  public function __construct(bool $condition_met) {
    $this->_met = $condition_met;
  }

  public function __invoke() : bool {
    return $this->_met;
  }

}
