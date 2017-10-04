<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Registry Mode
    |--------------------------------------------------------------------------
    |
    | This option tells to the module manager where you want to store the module configs.
    |
    | Possible options:
    | -----------------
    | - database        Use the database(don't forget run migrations).
    | - file            Use files.
    */
    'registry' => 'file',

    'file_registry_storage' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    */
    'models' => [
        // The module model class
        'module' => \TwoDojo\ModuleManager\Models\Module::class,
    ],
];
