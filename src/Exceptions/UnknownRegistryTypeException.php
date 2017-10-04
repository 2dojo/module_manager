<?php

namespace TwoDojo\ModuleManager\Exceptions;

class UnknownRegistryTypeException extends \Exception
{
    public function __construct($type)
    {
        parent::__construct('Unknown registry type: '.$type);
    }
}