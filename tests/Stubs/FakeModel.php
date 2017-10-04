<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use Illuminate\Database\Eloquent\Model;

class FakeModel extends Model
{
    protected $guarded = [];

    public $saved = false;

    public function save(array $options = [])
    {
        $this->saved = true;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $this->fill($attributes);

        return true;
    }
}
