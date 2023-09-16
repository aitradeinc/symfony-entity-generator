<?php

namespace EntityGenerator\Generator;

class Reference
{
    /** @var string */
    private $table;

    /** @var string */
    private $column;

    /** @var bool */
    private $nullable;

    /** @var string */
    private $referencedColumn;

    /** @var bool */
    private $isOwningSide;

    private $group;

    public function __construct(string $table, string $column, bool $nullable, string $referencedColumn, bool
                                       $isOwningSide = true, string $group = 'public')
    {
        $this->table = $table;
        $this->column = $column;
        $this->nullable = $nullable;
        $this->referencedColumn = $referencedColumn;
        $this->isOwningSide = $isOwningSide;
        $this->group = $group;
    }

    public function invert(string $table): Reference
    {
        return new self($table, $this->referencedColumn, false, $this->column, false);
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @return string
     */
    public function getReferencedColumn(): string
    {
        return $this->referencedColumn;
    }

    /**
     * @return bool
     */
    public function isOwningSide(): bool
    {
        return $this->isOwningSide;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup(string $group): void
    {
        $this->group = $group;
    }
}
