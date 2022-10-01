<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\System;

class Trace {

  /**
   * Get a trace starting from the first call to the given class
   *
   * Considers parent & child class to be same as class
   * Note: this is a backtrace, so "start" means "going backward"
   */
  static public function fromOutside(string $class) : array {
    $trace = static::startAt($class);

    foreach ($trace as $key => $entry) {
      $check = $entry['class'] ?? '';
      if (empty($check) || ! static::_isA($check, $class)) {
        $key--;
        break;
      }
    }

    return isset($key) ? array_splice($trace, $key) : [];
  }

  /**
   * Get a trace starting at calls involving the given class
   *
   * Considers parent & child class to be same as class
   * Note: this is a backtrace, so "start" means "going backward"
   */
  static public function startAt(string $class) : array {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

    $check = $trace[0];
    while (! static::_isA($class, $check['class'] ?? '')) {
      array_shift($trace);
      $check = reset($trace);
    }


    // renumber the keys with array_values()
    return array_values($trace);
  }

  /**
   * Companre the class names for match or subclass in either direction
   */
  static protected function _isA(string $a, string $b) : bool {
    return is_a($a, $b, true) ||
      is_a($b, $a, true);
  }

}
