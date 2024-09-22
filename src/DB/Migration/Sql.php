<?php

namespace WPDrill\DB\Migration;

class Sql
{
    protected string $sql = '';

    public function __construct(string $sql)
    {
        $this->sql = $this->validate($sql);
    }

    public function __toString(): string
    {
        return $this->sql;
    }

    protected function validate($query): string
    {
        // Remove comments
        $query = preg_replace('/(--.*)|(#.*)/', '', $query);

        // Check if the query contains certain keywords
        $keywords = array('SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP', 'TRUNCATE', 'GRANT', 'REVOKE', 'COMMIT', 'ROLLBACK');
        $result = false;
        foreach ($keywords as $keyword) {
            if (stripos($query, $keyword) !== false) {
                $result = $result | true;
            }
        }

        if (!$result) {
            throw new \Exception('Invalid SQL query');
        }

        return $query; // Query is considered valid
    }

    public function concat(self $sql): self
    {
        $this->sql .= $sql->__toString();

        return $this;

    }

}
