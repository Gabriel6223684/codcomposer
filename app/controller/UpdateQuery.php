<?php
namespace app\database\builder;

use PDO;
use Exception;

class UpdateQuery
{
    protected $table;
    protected $data = [];
    protected $wheres = [];
    protected $pdo;

    public function __construct()
    {
        // Configuração da conexão PDO
        $this->pdo = new PDO('mysql:host=localhost;dbname=seu_banco', 'usuario', 'senha');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function table($table)
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function set(array $data)
    {
        $this->data = $data;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->wheres[] = [$column, $operator, $value];
        return $this;
    }

    public function update()
    {
        if (empty($this->table) || empty($this->data) || empty($this->wheres)) {
            throw new Exception("Tabela, dados ou condição WHERE não definidos");
        }

        $setParts = [];
        $params = [];

        foreach ($this->data as $column => $value) {
            $setParts[] = "$column = :$column";
            $params[":$column"] = $value;
        }

        $whereParts = [];
        foreach ($this->wheres as $i => $where) {
            list($column, $operator, $value) = $where;
            $paramKey = ":where_$i";
            $whereParts[] = "$column $operator $paramKey";
            $params[$paramKey] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount() > 0; // Retorna true se alguma linha foi atualizada
    }
}
