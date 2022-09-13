<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\Todo;

class Cache {

  private array $_data;
  private object $_todo;

  public function __construct(Todo $todo) {
    $this->_todo = $todo;
  }

  static public function clearAll() {
    file_put_contents(static::_getFilePath(), '');
  }

  static protected function _getFilePath() : string {
    return sys_get_temp_dir() . '/todo_or_die';
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

  protected function _getAllData() : array {
    $path = $this->_getFilePath();
    $raw = file_exists($path) ? file_get_contents($path) : '';
    return json_decode($raw, true) ?? [];
  }

  protected function _getData() : array {
    if (! isset($this->_data)) {
      $this->_data = $this->_getAllData()[$this->_todo->getId()] ?? [];
    }

    return $this->_data;
  }

  protected function _set(string $key, string $value) {
    $data = $this->_getAllData();
    $data[$this->_todo->getId()][$key] = $value;
    file_put_contents($this->_getFilePath(), json_encode($data));
  }

}
