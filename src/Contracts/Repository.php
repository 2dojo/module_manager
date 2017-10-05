<?php

namespace TwoDojo\ModuleManager\Contracts;

interface Repository
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function save(array $attributes);

    /**
     * @param mixed $id
     * @param array $attributes
     * @return boolean
     */
    public function update($id, array $attributes);

    /**
     * @param mixed $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public function findByField(string $field, $value);

    /**
     * @return mixed
     */
    public function all();
}
