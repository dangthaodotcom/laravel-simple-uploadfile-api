<?php

namespace Dt\Media\Services\Contracts;


interface FileManagerInterface
{
    public function upload($files);

    public function store($image, $folder);
}
