<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

use Robertology\TodoOrDie\ {
  CacheStorage,
  System\File,
  Todo
};

class Cache {

  private array $_data;
  private CacheStorage $_store;
  private Todo $_todo;

  public function __construct(Todo $todo, CacheStorage $store = null) {
    $this->_todo = $todo;
    if (isset($store)) {
      $this->_store = $store;
    }
  }

  static public function clearAll(CacheStorage $store = null) {
    $store ??= static::_getDefaultStore();
    $store->truncate();
  }

  static protected function _getDefaultStore() : CacheStorage {
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

  protected function _getData() : array {
    if (! isset($this->_data)) {
      $this->_data = $this->_getFullCache()[$this->_todo->getId()] ?? [];
    }

    return $this->_data;
  }

  protected function _getFullCache() : array {
    return json_decode($this->_getStore()->read(), true) ?? [];
  }

  protected function _getStore() : CacheStorage {
    return $this->_store ?? $this->_getDefaultStore();
  }

  protected function _set(string $key, string $value) {
    $data = $this->_getFullCache();
    $data[$this->_todo->getId()][$key] = $value;
    $this->_getStore()->write(json_encode($data));
  }

}
