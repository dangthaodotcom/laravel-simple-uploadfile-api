<?php

namespace Dt\Media\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Dt\Media\Models\Media;
use Dt\Media\Services\Contracts\FileManagerInterface;
use \Uuid;

class FileManager implements FileManagerInterface
{
    private $filesystem;
    private $media;
    private $imageManager;
    private $width = 840;
    private $height = 472;
    private $folder = 'upload';
    private $folderResize = 'resize';
    protected $resizing = [
        (object) [
            'width'   => 27,
            'height'  => 27,
            'quality' => 80
        ],
        (object) [
            'width'   => 300,
            'height'  => 200,
            'quality' => 100
        ],
    ];

    /**
     * FileManager constructor.
     * @param  Media  $model
     * @param  ImageManager  $imageManager
     * @param $width
     * @param $height
     * @param $folder
     * @param $folderResize
     * @param $resizing
     */
    public function __construct(
        Media $model,
        ImageManager $imageManager,
        $width,
        $height,
        $folder,
        $folderResize,
        $resizing
    ) {
        $this->filesystem = \Storage::disk('ftp');
        $this->media = $model;
        $this->imageManager = $imageManager;
        $this->width = $width;
        $this->height = $height;
        $this->folder = $folder;
        $this->folderResize = $folderResize;
        $this->resizing = $resizing;
    }

    /**
     * @param $files
     * @return array
     */
    public function upload($files)
    {
        $arr = [];
        foreach ($files as $img) {
            $imgLink = $this->store($img, $this->folder);
            if (!empty($imgLink)) {
                $arr[] = $imgLink;
            }
        }
        return $arr;
    }

    /**
     * @param $folder
     */
    public function makeFolder($folder)
    {
        if (!$this->filesystem->exists($folder)) {
            $this->filesystem->makeDirectory($folder);
        }
    }

    /**
     * @param $parent
     * @param $prefix
     * @return string
     */
    public function createPath($parent, $prefix)
    {
        return $parent.$prefix.date('Y').'/'.date('m').'/'.date('d').'/';
    }

    /**
     * @param $image
     * @param $extension
     * @param $width
     * @param $height
     * @param $quality
     * @return \Psr\Http\Message\StreamInterface
     */
    public function resize($image, $extension, $width, $height, $quality)
    {
        $realImage = false;
        list($imageWidth, $imageHeight) = getimagesize($image);

        if ($imageWidth > $height) {
            $realImage = $this->imageManager->make($image)->resize(null, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->stream($extension, $quality);
        } else {
            if ($imageHeight > $width) {
                $realImage = $this->imageManager->make($image)->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->stream($extension, $quality);
            } else {
                $realImage = $image;
            }
        }
        return $realImage;
    }

    /**
     * @param $image
     * @param $folder
     * @return mixed
     */
    public function store($image, $folder)
    {
        $originalName = $image->getClientOriginalName();
        $extension = $image->extension();
        $mineType = $image->getMimeType();
        $name = substr(Str::slug($originalName, "-"), 0, 20);
        $hashName = Uuid::generate(3, time(), Uuid::NS_DNS);
        $fileName = sprintf($name.'-%s.%s', $hashName, $extension);
        $this->makeFolder($folder);

        $baseFolder = $this->createPath($folder, '/');
        $originalImage = $this->resize($image, $extension, $this->width, $this->height, 100);
        $this->filesystem->put($baseFolder.$fileName, $originalImage);

        foreach ($this->resizing as $value) {
            $uploadFolder = $this->createPath($folder,
                '/'.$this->folderResize.'/'.$value->width.'x'.$value->height.'/');
            $originalImage = $this->resize($image, $extension, $value->width, $value->height, $value->quality);
            $this->filesystem->put($uploadFolder.$fileName, $originalImage);
        }
        $this->media->create([
            'name'        => $name,
            'hashed_name' => $hashName,
            'path'        => $baseFolder,
            'minetype'    => $mineType,
            'extension'   => $extension,
            'user_id'     => Auth::user()->id,
        ]);

        return $this->media->id;
    }
}
