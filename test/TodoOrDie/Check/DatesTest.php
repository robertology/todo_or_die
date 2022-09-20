<?php
declare(strict_types=1);

namespace Robertology\TodoOrDie\Test\Check;

use PHPUnit\Framework\TestCase;
use Robertology\TodoOrDie\Check\Dates;

class DatesTest extends TestCase {

  public function testTimePastIsTrue() {
    $check = new Dates(time() - 5);
    $this->assertTrue($check());
  }

  public function testTimeFutureIsFalse() {
    $check = new Dates(time() + 5);
    $this->assertFalse($check());
  }

  public function testFromStringPastIsTrue() {
    $check = Dates::fromString('-1 month');
    $this->assertTrue($check());
  }

  public function testFromStringFutureIsFalse() {
    $check = Dates::fromString('+1 month');
    $this->assertFalse($check());
  }

}
