<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Check;

use Robertology\TodoOrDie\Check as CheckInterface;

class Versions implements CheckInterface {

  private const _DEFAULT_OPERATOR = '>=';

  private string $_version_left;
  private string $_version_right;
  private string $_operator;

  public function __construct(string $version_left, string $version_right, string $operator = self::_DEFAULT_OPERATOR) {
    $this->_version_left = $version_left;
    $this->_version_right = $version_right;
    $this->_operator = $operator;
  }

  public function __invoke() : bool {
    return version_compare($this->_version_left, $this->_version_right, $this->_operator);
  }

  static public function php(string $version, string $operator = self::_DEFAULT_OPERATOR) {
    return new static($version, phpversion(), $operator);
  }

}
