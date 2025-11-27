<?php

namespace app\database\builder;

use app\database\Connection;

class DeleteQuery
{
    private string $table;
    private array $where = [];
    private array $binds = [];

    public static function table(string $table): self
    {
        $self = new self;
        $self->table = $table;
        return $self;
    }

    public function where(string $field, string $operator, string|int $value, string $logic = 'AND'): self
{
    $placeholder = str_contains($field, '.') ? substr($field, strpos($field, '.') + 1) : $field;
    if (!empty($this->where)) $this->where[] = $logic;
    $this->where[] = "{$field} {$operator} :{$placeholder}";
    $this->binds[$placeholder] = $value;
    return $this;
}

public function delete(): bool
{
    $query = $this->createQuery();
    $connection = Connection::connection();
    $stmt = $connection->prepare($query);
    return $stmt->execute($this->binds);
}



    private function createQuery(): string
    {
        $query = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $query .= ' WHERE ' . implode(' ', $this->where);
        }

        return $query;
    }


    public function executeQuery(string $query): bool
    {
        $connection = Connection::connection();
        $prepare = $connection->prepare($query);
        return $prepare->execute($this->binds ?? []);
    }
}
