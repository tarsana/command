<?php namespace Tarsana\Command\Tests\Unit\Console;

use PHPUnit\Framework\TestCase;
use Tarsana\Command\Console\ExceptionPrinter;
use Tarsana\Syntax\Exceptions\ParseException;
use Tarsana\Syntax\Factory as S;


class ExceptionPrinterTest extends TestCase {

    public function test_it_prints_generic_exception() {
        $e = new \Exception("Useful message");
        $p = new ExceptionPrinter;
        $this->assertEquals("<error>{$e}</error>", $p->print($e));
    }

    public function test_it_prints_simple_parse_exception() {
        $p = new ExceptionPrinter;
        $e = new ParseException(S::number(), 'test', 0, 'Not a number!');
        $this->assertEquals(
            "<reset>Failed to parse <warn>'test'</warn> as <info>Number</info>",
            $p->print($e)
        );
    }

    public function test_it_prints_array_parse_exception() {
        $p = new ExceptionPrinter;
        $syntax = S::array(S::number());
        try {
            $syntax->parse('11,foo');
            $this->assertTrue(false, "Exception not thrown!");
        } catch(ParseException $e) {
            $this->assertEquals(
                "<reset>Failed to parse <warn>'11,foo'</warn> as <info>Number,...</info><br>".
                "<reset>Failed to parse <warn>'foo'</warn> as <info>Number</info>",
                $p->print($e)
            );
        }
    }

    public function test_it_prints_object_parse_exception_field_is_missing() {
        $p = new ExceptionPrinter;
        $syntax = S::object([
            'name' => S::string(),
            'age' => S::number()
        ]);
        try {
            $syntax->parse('foo');
            $this->assertTrue(false, "Exception not thrown!");
        } catch(ParseException $e) {
            $this->assertEquals(
                "<reset>Failed to parse <warn>'foo'</warn> as <info>name:age</info> ".
                "<error>age is missing!</error><br>".
                "<reset>Failed to parse <warn>''</warn> as <info>Number</info>",
                $p->print($e)
            );
        }
    }

    public function test_it_prints_object_parse_exception_field_is_invalid() {
        $p = new ExceptionPrinter;
        $syntax = S::object([
            'name' => S::string(),
            'age' => S::number()
        ]);
        try {
            $syntax->parse('foo:bar');
            $this->assertTrue(false, "Exception not thrown!");
        } catch(ParseException $e) {
            $this->assertEquals(
                "<reset>Failed to parse <warn>'foo:bar'</warn> as <info>name:age</info> ".
                "<error>age is invalid!</error><br>".
                "<reset>Failed to parse <warn>'bar'</warn> as <info>Number</info>",
                $p->print($e)
            );
        }
    }

    public function test_it_prints_object_parse_exception_additional_items() {
        $p = new ExceptionPrinter;
        $syntax = S::object([
            'name' => S::string(),
            'age' => S::number()
        ]);
        try {
            $syntax->parse('foo:11:baz:lorem');
            $this->assertTrue(false, "Exception not thrown!");
        } catch(ParseException $e) {
            $this->assertEquals(
                "<reset>Failed to parse <warn>'foo:11:baz:lorem'</warn> as <info>name:age</info> ".
                "<error>additional items baz:lorem</error>",
                $p->print($e)
            );
        }
    }

}
