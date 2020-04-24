<?php

class DB
{
    public mysqli $db;

    public function __construct()
    {
        $this->db = new mysqli("127.0.0.1", "root", "", "simple_php");
        if (!$this->db) {
            die('Error: Unable to establish connection');
        }
    }

    /**
     * @param string $table
     * @param bool $params
     * @return array|bool|null
     */
    public function selectRow(string $table, $params = false, $operator = ' AND ')
    {
        if (is_array($params)) {
            foreach ($params as $k => $v) {
                if (($k = checkValid($k, 'si')) && ($v = checkValid($v))) {
                    $where[] = "`{$k}` = '" . $this->_escape($v) . "'";
                }
            }
        }

        if (!$params || !$where) {
            return false;
        }

        $result = $this->db->query("SELECT * FROM `{$table}` WHERE " . implode($operator, $where). " LIMIT 1");
        if ($result->num_rows) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function insert(string $table, $params = false)
    {
        if (is_array($params)) {
            if ($table === 'users') {
                $params += [
                    'verify_token' => setToken($_SERVER['HTTP_USER_AGENT'] ?? 'simple-php / localhost'),
                    'token' => setToken(mosMakePassword(rand(12, 15))),
                    'created_at' => 'NOW()',
                ];
            }

            foreach ($params as $k => $v) {
                if (($k = checkValid($k, 'si')) && ($v = checkValid($v))) {
                    $columns[] = '`' . str_replace('fullname', 'full_name', $k) . '`';
                    $values[] = strpos($v, 'NOW()') === false
                        ? "'". $this->_escape($v) . "'"
                        : $v;
                }
            }

            $columns = implode(',', $columns ?? null);
            $values = implode(',', $values ?? null);
        }

        if (!$params || !$columns || !$values) {
            return false;
        }

        $this->db->query("INSERT INTO `{$table}` ({$columns}) VALUES ({$values})");

        return $this->db->insert_id;
    }

    public function update(string $table, $params = false, $attrs = false, $operator = ' AND ')
    {
        if (is_array($attrs)) {
            foreach ($attrs as $k => $v) {
                if (($k = checkValid($k, 'si')) && ($v = checkValid($v))) {
                    $where[] = "`{$k}` = '" . $this->_escape($v) . "'";
                }
            }
        }

        if (is_array($params)) {
            foreach ($params as $k => $v) {
                if (($k = checkValid($k, 'si')) && ($v = checkValid($v))) {
                    $setData[] = "`{$k}` = '" . $this->_escape($v) . "'";
                }
            }
        }

        if (!$params || !$where || !$setData) {
            return false;
        }

        return (bool)$this->db->query("UPDATE `{$table}` SET " . implode(', ', $setData). " WHERE " . implode($operator, $where));
    }

    public function __destruct()
    {
        mysqli_close($this->db);
    }

    private function _escape($v)
    {
        $v = str_ireplace(
            ['"', "'", '--', ',', '.', ';', '#', 'or', 'and', 'select', 'insert', 'delete', 'explain', 'update', 'describe'],
            '',
            $v
        );

        return mysqli_real_escape_string($this->db, $v);
    }
}
