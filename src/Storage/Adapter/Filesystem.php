<?php

namespace Nicoren\CronBundle\Storage\Adapter;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem as FilesystemClient;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\SplFileInfo;



/**
 * Created on Wed Mar 16 2022
 * @author : Nicolas RENAULT <nrenault@tangkoko.com>
 * @copyright (c) 2022 Tangkoko
 **/


class Filesystem implements AdapterInterface
{

    private string $file;
    private FilesystemClient $client;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->client = new FilesystemClient();
    }

    public function get(): array
    {
        $ids = [];
        if ($this->client->exists($this->file)) {
            $fileInfo = new SplFileInfo($this->file, '', '');
            $ids = json_decode($fileInfo->getContents(), true);
        }
        return $ids;
    }

    public function set(array $value): self
    {
        $this->client->dumpFile($this->file, json_encode($value));
        return $this;
    }
}
