<?php

namespace TwoDojo\Test\ModuleManager\Stubs;

use TwoDojo\ModuleManager\Repositories\EloquentRepository;

class EloquentRepositoryStub extends EloquentRepository
{
    public function getScopes()
    {
        return $this->scopes;
    }
}
