<?php namespace Tarsana\Application\Syntaxes;

use Tarsana\Syntax\Syntax;
use Tarsana\Functional as F;
use Tarsana\Application\Inputs\ApplicationInput;

/**
 * This syntax represents an application input.
 * It follows the format: [command-name] [command-input]
 */
class ApplicationInputSyntax extends Syntax {

    /**
     * Returns the string representation of the syntax.
     *
     * @return string
     */
    public function __toString()
    {
        return '[flags... cmd args...]';
    }

    /**
     * Checks if the provided string can be parsed as application input.
     *
     * @param  string $text
     * @return array
     */
    public function checkParse($text)
    {
        return is_string($text) ? [] : ["Unable to parse '{$text}' as '{$this}'"];
    }

    /**
     * Converts a string to an ApplicationInput.
     *
     * @param  string $text
     * @return ApplicationInput
     */
    protected function doParse($text)
    {
        $text = F\split(' ', $text);
        $isFlag = F\startsWith('--');
        $i = F\reduce(function($result, $item) use ($isFlag) {
            if (null !== $result['command']) {
                $result['args'][] = $item;
            } else {
                if ($isFlag($item))
                    $result['flags'][] = $item;
                else
                    $result['command'] = $item;
            }
            return $result;
        }, ['flags' => [], 'command' => null, 'args' => []], $text);
        $i['args'] = F\join(' ', $i['args']);
        return new ApplicationInput($i['flags'], $i['command'], $i['args']);
    }

    /**
     * Checks if the provided argument can be dumped as ApplicationInput.
     *
     * @param  ApplicationInput $input
     * @return array
     */
    public function checkDump($input)
    {
        return ($input instanceof ApplicationInput) ? [] : ["Unable to dump '{$input}' as '{$this}'"];
    }

    /**
     * Converts the ApplicationInput to string.
     *
     * @param  ApplicationInput $value
     * @return string
     */
    public function doDump($value)
    {
        return trim(F\join(' ', $value->flags()) . " {$value->command()} {$value->args()}");
    }
}
