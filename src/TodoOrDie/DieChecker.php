<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  Check,
  Todo
};

class DieChecker {

  private bool $_died = false;

  public function __invoke(Check $check) : bool {
    $died = $check() &&
      ! $this->_hasDied() &&
      ! getenv('TODOORDIE_SKIP_DIE');

    $this->_died = $this->_died || $died;

    return $died;
  }

  protected function _hasDied() : bool {
    return $this->_died;
  }

}
