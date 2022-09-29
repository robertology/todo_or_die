<?php
declare(strict_types=1);
// @phan-file-suppress PhanTypeMismatchArgumentProbablyReal, PhanTypeMismatchArgument

namespace Robertology\TodoOrDie\Test;

use PHPUnit\Framework\ {
  TestCase
};
use Robertology\TodoOrDie\ {
  Cache,
  CacheStorage,
  Todo,
};

class CacheTest extends TestCase {

  public function testClearAll() {
    $store = $this->getMockBuilder(CacheStorage::class)
      ->onlyMethods(['read', 'write', 'truncate'])
      ->getMock();
    $store->expects($this->once())
      ->method('truncate');

    Cache::clearAll($store);
  }

  public function testGetLastAlert() {
    $todo = $this->createStub(Todo::class);
    $todo->method('getId')
      ->willReturn('my_todo_id');

    $store = $this->createStub(CacheStorage::class);
    $store->method('read')
      ->willReturn('{"my_todo_id": {"last_alert": "1234"}}');

    $this->assertSame(
      1234,
      (new Cache($todo, $store))->getLastAlert()
    );
  }

  public function testSetLastAlert() {
    $todo = $this->createStub(Todo::class);
    $todo->method('getId')
      ->willReturn('my_todo_id');

    $store = $this->getMockBuilder(CacheStorage::class)
      ->onlyMethods(['read', 'write', 'truncate'])
      ->getMock();
    $store->expects($this->once())
      ->method('write')
      ->with('{"my_todo_id":{"last_alert":"1234"}}');

    (new Cache($todo, $store))->setLastAlert(1234);
  }

}
