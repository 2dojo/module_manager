<?php

namespace TwoDojo\ModuleManager\Registries;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use TwoDojo\ModuleManager\Support\ModuleDescriptor;

class FileRegistry extends BaseRegistry
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $storage;

    /**
     * @var string
     */
    protected $directory = 'module_manager/registry';

    protected $scopes = [];

    /**
     * FileRegistry constructor.
     */
    public function __construct()
    {
        $this->storage = Storage::disk(config('module_manager.file_registry_storage'));
        if (!$this->storage->exists($this->directory)) {
            $this->storage->makeDirectory($this->directory);
        }
    }

    /**
     * @inheritdoc
     */
    public function save(array $attributes)
    {
        $uniqueName = $attributes['uniqueName'];
        $lastId = $this->getFiles()->sortByDesc('id')->pluck('id')->first();
        $lastId = $lastId ? $lastId : 1;

        $saved = $this->storage->put($this->getFullPath($uniqueName), json_encode(array_merge([
            'id' => $lastId,
            'created_at' => Carbon::now()->timestamp,
            'updated_at' => Carbon::now()->timestamp,
        ], $attributes)));

        return $saved ? $this->getFile($uniqueName) : (object)$attributes;
    }

    /**
     * @inheritdoc
     */
    public function update($id, array $attributes)
    {
        $record = $this->find($id);
        if (is_null($record)) {
            return false;
        }

        $saved = $this->storage->put($this->getFullPath($record->uniqueName), json_encode(array_merge($record->getAttributes(), [
            'updated_at' => Carbon::now()->timestamp
        ], $attributes)));

        return $saved;
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->applyScopes($this->getFiles()->where('id', $id))->first();
    }

    /**
     * @inheritdoc
     */
    public function findByUniqueName(string $uniqueName)
    {
        $data = $this->applyScopes($this->getFile($uniqueName))->first();
        $collection = collect([]);

        return $data !== null ? $collection->put($data->id, $data) : null;
    }

    /**
     * @inheritdoc
     */
    public function findByField(string $field, $value)
    {
        return $this->applyScopes($this->getFiles()->where($field, $value));
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->applyScopes($this->getFiles());
    }

    /**
     * Open the module's json file
     *
     * @param $file
     * @return null|ModuleDescriptor
     */
    protected function getFile($file)
    {
        $path = $this->getFullPath($file);
        if (!$this->storage->exists($path)) {
            return null;
        }

        $data = json_decode($this->storage->get($path), true);
        return $this->getNewModuleDescriptor($data);
    }

    /**
     * Get all saved modules' json file
     *
     * @return Collection
     */
    protected function getFiles()
    {
        $files = collect([]);
        foreach ($this->storage->allFiles($this->directory) as $file) {
            $data = json_decode($this->storage->get($file), true);
            $files->put($data['id'], $this->getNewModuleDescriptor($data));
        }

        return $files;
    }

    /**
     * Returns a new ModuleDescriptor instance.
     *
     * @param array $attributes
     * @return ModuleDescriptor
     */
    protected function getNewModuleDescriptor(array $attributes)
    {
        return new ModuleDescriptor($attributes);
    }

    /**
     * Get the full path for a registry entry.
     *
     * @param $file
     * @return string
     */
    protected function getFullPath($file)
    {
        return $this->directory.'/'.$file.'.json';
    }

    /**
     * Apply the registered scopes
     *
     * @param $target
     * @return Collection
     */
    protected function applyScopes($target)
    {
        $collection = $target;
        if (!($target instanceof Collection)) {
            $collection = collect([$target]);
        }

        foreach ($this->scopes as $data) {
            list($scope, $arguments) = $data;
            switch ($scope) {
                case 'enabled':
                    $collection = $collection->where('is_enabled', true);
                    break;
                default:
                    throw new \InvalidArgumentException();
            }
        }

        $this->scopes = [];

        return $collection;
    }

    /**
     * @inheritdoc
     */
    protected function scope($scope, $arguments)
    {
        $this->scopes[] = [$scope, $arguments];

        return parent::scope($scope, $arguments);
    }
}
