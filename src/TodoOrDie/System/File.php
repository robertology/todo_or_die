<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\System;

use Robertology\TodoOrDie\CacheStorage;

class File implements CacheStorage {

  private string $_path;

  public function __construct(string $path) {
    $this->_path = $path;
  }

  public function exists() : bool {
    return file_exists($this->_path);
  }

  public function read() : string {
    return $this->exists() ? file_get_contents($this->_path) : '';
  }

  public function write(string $data) {
    file_put_contents($this->_path, $data);
  }

  public function truncate() {
    if ($this->exists()) {
      $this->write('');
    }
  }

}
