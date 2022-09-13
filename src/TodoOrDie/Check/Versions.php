<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Check;

class Versions {

  private string $_version_left;
  private string $_version_right;
  private string $_operator;

  public function __construct(string $version_left, string $version_right, string $operator = '>=') {
    $this->_version_left = $version_left;
    $this->_version_right = $version_right;
    $this->_operator = $operator;
  }

  public function __invoke() : bool {
    return version_compare($this->_version_left, $this->_version_right, $this->_operator);
  }
}
