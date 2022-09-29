<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  System\File,
  Todo
};

class Cache {

  private array $_data;
  private object $_todo;

  public function __construct(Todo $todo) {
    $this->_todo = $todo;
  }

  static public function clearAll() {
    static::_getFile()->truncate();
  }

  static protected function _getFile() : File {
    return new File(sys_get_temp_dir() . '/todo_or_die');
  }

  public function getLastAlert() : ?int {
    $last = $this->_get('last_alert');
    return isset($last) ? (int)$last : null;
  }

  public function setLastAlert(int $timestamp) {
    $this->_set('last_alert', (string)$timestamp);
  }

  protected function _get(string $key) : ?string {
    return $this->_getData()[$key] ?? null;
  }

  protected function _getFullCache() : array {
    return json_decode(static::_getFile()->read(), true) ?? [];
  }

  protected function _getData() : array {
    if (! isset($this->_data)) {
      $this->_data = $this->_getFullCache()[$this->_todo->getId()] ?? [];
    }

    return $this->_data;
  }

  protected function _set(string $key, string $value) {
    $data = $this->_getFullCache();
    $data[$this->_todo->getId()][$key] = $value;
    $this->_getFile()->write(json_encode($data));
  }

}
