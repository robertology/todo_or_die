<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

interface CacheStorage {

  /** Read the entire cache */
  public function read() : string;

  /** Write the entire cache */
  public function write(string $data);

  /** Truncate the entire cache */
  public function truncate();

}
