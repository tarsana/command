<?php namespace Tarsana\Command\Tests\Unit\Console;

use PHPUnit\Framework\TestCase;
use Tarsana\Command\Console\OutTransformer;


class OutTransformerTest extends TestCase {

    public function test_it_applies_controls() {
        $t = new OutTransformer;
        $this->assertEquals(
            "\033[38;5;42m",
            $t->transform('<color:42>')
        );
        $this->assertEquals(
            "\033[48;5;42m",
            $t->transform('<background:42>')
        );
    }

    public function test_it_applies_aliases() {
        $t = new OutTransformer;
        $this->assertEquals("Line1<br>Line2", $t->transform('Line1<br>Line2'));

        $t->alias('<br>', PHP_EOL);
        $this->assertEquals("Line1".PHP_EOL."Line2", $t->transform('Line1<br>Line2'));
    }
}
