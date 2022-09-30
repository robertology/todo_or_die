<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie;

interface Check {

  public function __invoke() : bool;

}
