<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  Check,
  TodoState,
};

class AlertChecker {

  private const _ALERT_THROTTLE_THRESHOLD_SECONDS = 3600;

  private TodoState $_todo_state;

  public function __construct(TodoState $todo_state) {
    $this->_todo_state = $todo_state;
  }

  public function __invoke(Check $check) : bool {
    return ! $this->_todo_state->hasDied() &&
      ! $this->_isThrottled() &&
      $check();
  }

  protected function _getThresholdTimestamp() : int {
    return time() - static::_ALERT_THROTTLE_THRESHOLD_SECONDS;
  }

  protected function _hasRecentlyAlerted() : bool {
    return $this->_todo_state->getLastAlert() >= $this->_getThresholdTimestamp();
  }

  protected function _isThrottled() : bool {
    // If this Todo has alerted, consider it not throttled
    return ! $this->_todo_state->hasAlerted() &&
      $this->_hasRecentlyAlerted();
  }

}
