<?php

namespace Laracatch\Client\Storage;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Laracatch\Client\Contracts\StorageContract;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class FilesystemStorage extends Storage implements StorageContract
{
    use ImplementsComparison,
        ImplementsFiltering;

    /** @var Filesystem */
    protected $files;

    /** @var string */
    protected $dirname;

    /**
     * @param Filesystem $files
     * @param string $dirname
     */
    public function __construct(Filesystem $files, $dirname)
    {
        parent::__construct();

        $this->files = $files;
        $this->dirname = rtrim($dirname, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Save a new file.
     *
     * @param array $data
     *
     * @return string
     * @throws RuntimeException
     */
    public function save(array $data): ?string
    {
        if ( ! $this->files->isDirectory($this->dirname)) {
            if ($this->files->makeDirectory($this->dirname, 0777, true)) {
                $this->files->put($this->dirname . '.gitignore', "*\n!.gitignore\n");
            } else {
                throw new RuntimeException("Cannot create directory '$this->dirname'.");
            }
        }

        try {
            $id = $this->generateIdentifier();

            $this->files->put($this->makeFilename($id), json_encode($data));

            $this->garbageCollect();

            return $id;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create the filename for the data, based on the id.
     *
     * @param $id
     *
     * @return string
     */
    public function makeFilename($id): string
    {
        return $this->dirname . $id . '.json';
    }

    /**
     * Delete files older then a certain age
     */
    public function garbageCollect(): void
    {
        /** @var SplFileInfo $file */
        foreach (Finder::create()->files()->name('*.json')->date('< ' . $this->retention . ' hour ago')->in($this->dirname) as $file) {
            $this->files->delete($file->getRealPath());
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function find($id): array
    {
        return json_decode($this->files->get($this->makeFilename($id)), true);
    }

    /**
     * {@inheritDoc}
     */
    public function get(array $filters = [], $max = 20, $offset = 0): array
    {
        $sort = static function (SplFileInfo $a, SplFileInfo $b) {
            return strcmp($b->getMTime(), $a->getMTime());
        };

        $i = 0;
        $results = [];

        /** @var SplFileInfo $file */
        foreach (Finder::create()->files()->name('*.json')->in($this->dirname)->sort($sort) as $file) {
            if ($i++ < $offset && empty($filters)) {
                $results[] = null;
                continue;
            }

            $data = json_decode($file->getContents(), true);

            $data['id'] = $file->getBasename('.json');

            if ($this->filter($data, $filters)) {
                $results[] = $data;
            }

            if (count($results) >= ($max + $offset)) {
                break;
            }
        }
        return array_slice($results, $offset, $max);
    }

    /**
     * Clear the storage path.
     */
    public function clear(): void
    {
        /** @var SplFileInfo $file */
        foreach (Finder::create()->files()->name('*.json')->in($this->dirname) as $file) {
            $this->files->delete($file->getRealPath());
        }
    }
}
