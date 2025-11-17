<?php 
// Inicia o código PHP

namespace app\database\builder;
// Define o namespace onde essa classe está organizada

use app\database\Connection;
// Importa a classe responsável pela conexão com o banco

class SelectQuery
// Declara a classe SelectQuery, que monta queries SELECT
{
    private string $fields;
    // Guarda os campos da consulta (ex: "*", "id, nome")

    private string $table;
    // Guarda o nome da tabela que será consultada

    private array $where = [];
    // Array que armazena as condições WHERE da consulta

    private array $binds = [];
    // Array que armazena os valores dos placeholders (bind parameters)

    private string $order;
    // Armazena a cláusula ORDER BY

    private int $limit;
    // Armazena o valor do LIMIT

    private int $offset;
    // Armazena o valor do OFFSET

    private string $limits;
    // String final contendo LIMIT e OFFSET

    public static function select(string $fields = '*'): self
    // Método estático para iniciar a consulta SELECT e definir campos
    {
        $self = new self;
        // Cria uma nova instância da classe

        $self->fields = $fields;
        // Define os campos que serão selecionados

        return $self;
        // Retorna a própria instância (method chaining)
    }

    public function from(string $table): self
    // Define a tabela da consulta
    {
        $this->table = $table;
        // Atribui o nome da tabela

        return $this;
        // Retorna a instância para encadear métodos
    }

    public function where(string $field, string $operator, string | int $value, ?string $logic = null): self
    // Adiciona uma condição WHERE com operador e valor
    {
        $placeholder = '';
        // Cria uma variável para o placeholder

        $placeholder = $field;
        // Inicialmente usa o nome do campo como placeholder

        if (str_contains($placeholder, '.')) {
            // Verifica se o campo contém ponto (ex: users.id)

            $placeholder = substr($field, strpos($field, '.') + 1);
            // Remove o prefixo antes do ponto (ex: fica só "id")
        }

        $this->where[] = "{$field}  {$operator} :{$placeholder} {$logic}";
        // Monta a string do WHERE e adiciona ao array

        $this->binds[$placeholder] = $value;
        // Armazena o valor associado ao placeholder

        return $this;
        // Retorna a instância para method chaining
    }

    public function order(string $field, string $typeOrder = 'asc'): self
    // Define a cláusula ORDER BY
    {
        $this->order = " order by {$field}  {$typeOrder}";
        // Monta a string do ORDER BY

        return $this;
        // Retorna a instância
    }

    public function limit(int $limit, int $offset = 0): self
    // Define LIMIT e OFFSET
    {
        $this->limit = $limit;
        // Armazena o LIMIT

        $this->offset = $offset;
        // Armazena o OFFSET

        $this->limits = " limit {$this->limit} offset {$this->offset} ";
        // Monta a string final do LIMIT/OFFSET

        return $this;
        // Retorna a instância
    }

    private function createQuery(): string
    // Método interno para montar a query SQL completa
    {
        if (!$this->fields) {
            // Verifica se há campos definidos

            throw new \Exception("Para realizar uma consulta SQL é necessário informa os campos da consulta");
            // Lança erro caso não tenha campos
        }

        if (!$this->table) {
            // Verifica se a tabela foi definida

            throw new \Exception("Para realizar a consulta SQL é necessário informa a nome da tabela.");
            // Lança erro se não tiver tabela
        }

        $query = '';
        // Inicia variável da query como string vazia

        $query = 'select ';
        // Inicia a query com SELECT

        $query .= $this->fields . ' from ';
        // Adiciona os campos e a palavra FROM

        $query .= $this->table;
        // Adiciona o nome da tabela

        $query .= (isset($this->where) and (count($this->where) > 0)) ? ' where ' . implode(' ', $this->where) : '';
        // Caso existam condições WHERE, junta todas em uma string

        $query .= $this->order ?? '';
        // Adiciona ORDER BY (se existir)

        $query .= $this->limits ?? '';
        // Adiciona LIMIT e OFFSET (se existir)

        return $query;
        // Retorna a query montada
    }

    public function fetch()
    // Executa a consulta e retorna apenas 1 registro
    {
        $query = '';
        // Inicia string vazia

        $query = $this->createQuery();
        // Cria a query final

        try {
            $connection = Connection::connection();
            // Obtém a conexão com o banco usando PDO

            $prepare = $connection->prepare($query);
            // Prepara a query para evitar SQL Injection

            $prepare->execute($this->bind ?? []);
            // Executa a query com os valores vinculados (ERRO: deveria ser $this->binds)

            return $prepare->fetch(\PDO::FETCH_ASSOC);
            // Retorna um único registro como array associativo

        } catch (\Exception $e) {
            // Captura qualquer erro

            throw new \Exception("Restrição: " . $e->getMessage());
            // Lança exceção personalizada
        }
    }

    public function fetchAll()
    // Executa a consulta e retorna todos os registros
    {
        $query = '';
        // Inicia string vazia

        $query = $this->createQuery();
        // Monta a query final

        try {
            $connection = Connection::connection();
            // Obtém a conexão com o banco

            $prepare = $connection->prepare($query);
            // Prepara a execução

            $prepare->execute($this->bind ?? []);
            // Executa com binds (ERRO: deveria ser $this->binds)

            return $prepare->fetchAll(\PDO::FETCH_ASSOC);
            // Retorna todos os registros como array associativo

        } catch (\Exception $e) {
            // Captura erro

            throw new \Exception("Restrição: " . $e->getMessage());
            // Repassa erro
        }
    }
}
