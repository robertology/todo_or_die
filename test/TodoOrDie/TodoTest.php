<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test;

use PHPUnit\Framework\TestCase;
use Robertology\TodoOrDie\ {
  OverdueError as Exception,
  Todo
};

class TodoTest extends TestCase {

  public function testDoNotDie() {
    $this->expectNotToPerformAssertions();

    new Todo('Some message', false);
  }

  public function testDoesDie() {
    $this->expectException(Exception::class);
    $this->expectExceptionMessage('Some message');

    new Todo('Some message', true);
  }

}
