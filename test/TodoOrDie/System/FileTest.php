<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test\System;

use PHPUnit\Framework\ {
  TestCase
};
use Robertology\TodoOrDie\ {
  System\File
};

class FileTest extends TestCase {

  private File $_file;
  private string $_path;

  public function setUp() : void {
    $this->_path = sys_get_temp_dir() . '/' . uniqid('todo_or_die');
    $this->_file = new File($this->_path);
  }

  public function tearDown() : void {
    if (file_exists($this->_path)) {
      unlink($this->_path);
    }
  }

  public function testNoFileUntilWriteExists() {
    $this->assertFalse($this->_file->exists());
    file_put_contents($this->_path, '');
    $this->assertTrue($this->_file->exists());
  }

  public function testRead() {
    $data = 'asdf 1234';
    file_put_contents($this->_path, $data);
    $this->assertSame($data, $this->_file->read());
  }

  public function testReadMultiLineFile() {
    $data = "asdf 1234\nqwerty";
    file_put_contents($this->_path, $data);
    $this->assertSame($data, $this->_file->read());
  }

  public function testWrite() {
    $data = 'asdf 1234';
    $this->_file->write($data);
    $this->assertSame($data, file_get_contents($this->_path));
  }

  public function testWriteMultiLine() {
    $data = "asdf 1234\nqwerty";
    $this->_file->write($data);
    $this->assertSame($data, file_get_contents($this->_path));
  }

  public function testTruncate() {
    $data = 'asdf 1234';
    file_put_contents($this->_path, $data);
    $this->_file->truncate();

    $this->assertSame('', file_get_contents($this->_path));
  }

}
