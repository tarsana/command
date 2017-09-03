<?php namespace Tarsana\Command\Console;

use Tarsana\Syntax\ArraySyntax;
use Tarsana\Syntax\Exceptions\ParseException;
use Tarsana\Syntax\ObjectSyntax;
use Tarsana\Syntax\Syntax;

/**
 * Transforms an exception to a string to be shown on the console.
 */
class ExceptionPrinter {

    /**
     * Converts the given exception to a string.
     *
     * @param  \Exception $e
     * @return string
     */
    public function print(\Exception $e) : string
    {
        if ($e instanceof ParseException)
            return $this->printParseException($e);

        return "<error>{$e}</error>";
    }

    /**
     * Converts a parse exception to a string.
     *
     * @param  Tarsana\Syntax\Exceptions\ParseException $e
     * @return string
     */
    public function printParseException(ParseException $e) : string
    {
        $syntax = $e->syntax();
        $error  = '';
        if ($syntax instanceof ObjectSyntax) {
            $i = $e->extra();
            if ($i['type'] == 'invalid-field')
                $error = "{$i['field']} is invalid!";
            if ($i['type'] == 'missing-field')
                $error = "{$i['field']} is missing!";
            if ($i['type'] == 'additional-items') {
                $items = implode($syntax->separator(), $i['items']);
                $error = "additional items {$items}";
            }
        }
        $syntax = $this->printSyntax($e->syntax());

        $output = "<reset>Failed to parse <warn>'{$e->input()}'</warn> as <info>{$syntax}</info>";
        if ('' != $error)
            $output .= " <error>{$error}</error>";

        $previous = $e->previous();
        if ($previous) {
            $output .= '<br>' . $this->printParseException($previous);
        }

        return $output;
    }

    protected function printSyntax(Syntax $s, bool $short = false) : string
    {
        if ($s instanceof ObjectSyntax) {
            if ($short) return 'object';
            return implode($s->separator(), array_keys($s->fields()));
        }
        if ($s instanceof ArraySyntax) {
            if ($short) return 'array';
            return $this->printSyntax($s->syntax()).$s->separator().'...';
        }
        return (string) $s;
    }
}
