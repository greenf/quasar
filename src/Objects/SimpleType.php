<?php namespace Greenf\Quasar\Objects;

class SimpleType implements ReturnType {

    const VOID = 'void';
    const STRING = 'string';
    const INTEGER = 'int';

    private $type;

    private function __construct(string $type)
    {
        $type->type = $type;
    }

    public static function void()
    {
        return new self(self::VOID);
    }

    public static function string()
    {
        return new self(self::STRING);
    }

    public static function integer()
    {
        return new self(self::INTEGER);
    }

    public function getType(): string
    {
        return $this->type;
    }

}