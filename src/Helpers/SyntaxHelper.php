<?php namespace Tarsana\Command\Helpers;

use Tarsana\Syntax\Syntax;


class SyntaxHelper {

    protected static $instance;

    public static function instance() : SyntaxHelper
    {
        if (null === self::$instance)
            self::$instance = new SyntaxHelper;
        return self::$instance;
    }

    private function __construct() {}

    public function type(Syntax $syntax) : string
    {
        $class = explode("\\", get_class($syntax));
        return strtolower(substr(array_pop($class), 0, -6));
    }

    public function asString(Syntax $syntax) : string
    {
        $type = $this->type($syntax);
        if ($type == 'optional')
            return $this->asString($syntax->syntax());
        switch ($type) {
            case 'object':
                return implode(
                    $syntax->separator(),
                    array_keys($syntax->fields())
                );
            break;
            case 'array':
                $text = $this->asString($syntax->syntax());
                return "{$text}{$syntax->separator()}...";
            break;
            default:
                return $type;
        }
    }

    public function fields(Syntax $syntax) : array
    {
        $type = $this->type($syntax);
        switch ($type) {
            case 'object':
                return $syntax->fields();
            case 'array':
            case 'optional':
                return $this->fields($syntax->syntax());
        }
        return [];
    }

}
