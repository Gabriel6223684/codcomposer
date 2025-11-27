<?php

namespace app\database\builder;

use app\database\Connection;

class InsertQuery
{
    private string $table;
    private array $FieldsAndValues = [];

    public static function table(string $table): self
    {
        $self = new self;
        $self->table = $table;
        return $self;
    }

    private function createQuery(): string
    {
        $fields  = implode(',', array_keys($this->FieldsAndValues));
        $placeHolder = ':' . implode(',:', array_keys($this->FieldsAndValues));
        return "INSERT INTO $this->table ($fields) VALUES ($placeHolder)";
    }

    private function execute(string $query): bool
    {
        $con = Connection::connection();
        $prepare = $con->prepare($query);

        $ok = $prepare->execute($this->FieldsAndValues);

        if (!$ok) {
            file_put_contents("insert_error.log", print_r($prepare->errorInfo(), true), FILE_APPEND);
        }

        return $ok;
    }


    public function save(array $FieldsAndValues): bool
    {
        $this->FieldsAndValues = $FieldsAndValues;
        $query = $this->createQuery();
        try {
            return $this->execute($query);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getLastInsertId(): int
    {
        return Connection::connection()->lastInsertId();
    }
}
