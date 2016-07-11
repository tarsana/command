<?php namespace Tarsana\Command\Syntax;

use Tarsana\Syntax\Syntax;
use Tarsana\Functional as F;
use Tarsana\Syntax\ArraySyntax;
use Tarsana\Syntax\Factory as S;
use Tarsana\Syntax\ObjectSyntax;
use Tarsana\Command\Exceptions\SyntaxBuilderException;

/**
 * This class is used to define and describe a syntax
 * and specify default values of optional fields.
 */
class SyntaxBuilder {

    /**
     * The built syntax.
     *
     * @var Tarsana\Syntax\ObjectSyntax
     */
    protected $syntax;

    /**
     * A constructor alias; If you don't like the 'new' keyword.
     * @param  string|Tarsana\Syntax\ObjectSyntax $syntax
     * @return Tarsana\Command\Syntax\SyntaxBuilder
     * @throws Tarsana\Command\Exceptions\SyntaxBuilderException
     */
    public static function of($syntax)
    {
        return new SyntaxBuilder($syntax);
    }

    /**
     * Creates a new SyntaxBuilder from a string or an ObjectSyntax.
     *
     * @param  string|Tarsana\Syntax\ObjectSyntax $syntax
     * @throws Tarsana\Command\Exceptions\SyntaxBuilderException
     */
    public function __construct($syntax)
    {
        if (is_string($syntax)) {
            $syntax = self::syntaxFromString($syntax);
        } else if (! ($syntax instanceof ObjectSyntax)) {
            throw new SyntaxBuilderException("The command syntax should be an insatnce of ObjectSyntax");
        }

        $this->syntax = $syntax;
    }

    /**
     * syntax getter.
     *
     * @return Tarsana\Syntax\ObjectSyntax
     */
    public function get()
    {
        return $this->syntax;
    }

    /**
     * make a Syntax from custom string.
     *
     * @param  string $value
     * @return Tarsana\Syntax\Syntax
     * @throws Tarsana\Command\Exceptions\SyntaxBuilderException
     */
    protected static function syntaxFromString ($value)
    {
        $syntax = F\s($value)->then(        // "first name" [#age]
            F\chunks('(){}[]""', ' '),      // ["first name", [#age]]
            F\join(','),                    // "first name",[#age]
            F\prepend('{ ,'), F\append('}') // { ,"first name",[#age]}
        )->get();

        $ss = S::syntax();

        if (!$ss->canParse($syntax))
            throw new SyntaxBuilderException("Invalid Syntax: '{$syntax}'");

        return $ss->parse($syntax);
    }

    /**
     * Describes a field or subfield of the syntax.
     * `$name` has the format "field.subfield...".
     *
     * @param  string $name
     * @param  string $description
     * @param  mixed  $default
     * @return Tarsana\Command\Syntax\SyntaxBuilder
     */
    public function describe($name, $description, $default = null)
    {
        $syntax = $this->field($this->syntax, F\split('.', trim($name)))
            ->description($description);
        if (null !== $default) {
            $syntax->setDefault($default);
        }

        return $this;
    }

    /**
     * Gets a field or subfield from an Syntax.
     *
     * @param  Tarsana\Syntax\Syntax $syntax
     * @param  array $names
     * @return Tarsana\Syntax\Syntax
     * @throws Tarsana\Command\Exceptions\SyntaxBuilderException
     */
    protected function field(Syntax $syntax, $names)
    {
        return empty($names) ? $syntax :
            $this->field(
                $this->ensureObject($syntax)->field(F\head($names)),
                F\tail($names)
            );
    }

    /**
     * Ensures that `$syntax` is an ObjectSyntax or an ArraySyntax of ObjectSyntaxes,
     * and returns the ObjectSyntax or throws a SyntaxBuilderException.
     *
     * @param  Tarsana\Syntax\Syntax $syntax
     * @return Tarsana\Syntax\Syntax
     * @throws Tarsana\Command\Exceptions\SyntaxBuilderException
     */
    protected function ensureObject(Syntax $syntax)
    {
        while ($syntax instanceof ArraySyntax) {
            $syntax = $syntax->itemSyntax();
        }
        if (! ($syntax instanceof ObjectSyntax)) {
            throw new SyntaxBuilderException("Could not retreive field of non object syntax '{$syntax}'");
        }
        return $syntax;
    }

}
