<?php

class User extends DB
{
    private string $table = 'users';

    public function find($data)
    {
        $data = $this->_preparePwd($data);
        return parent::selectRow($this->table, $data) ?? false;
    }

    public function insert($data)
    {
        $data = $this->_preparePwd($data);
        return parent::insert($this->table, $data) ?? false;
    }

    public function update($data, $attrs)
    {
        $data = $this->_preparePwd($data);
        return parent::update($this->table, $data, $attrs) ?? false;
    }

    private function _preparePwd($data)
    {
        if (isset($data['password']) && trim($data['password'])) {
            $data['password'] = hashPwd($data['password']);
        }

        return $data;
    }
}
