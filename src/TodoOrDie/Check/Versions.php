<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Check;

class Versions {

  private string $_version_left;
  private string $_version_right;

  public function __construct(string $version_left, string $version_right) {
    $this->_version_left = $version_left;
    $this->_version_right = $version_right;
  }

  public function __invoke() : bool {
    return version_compare($this->_version_left, $this->_version_right, '>=');
  }
}
