<?php

namespace Dt\Media\Http\Controllers;

use Dt\Media\Http\Requests\UploadImagesRequest;
// use Dt\Core\Http\Controllers\BaseController;
use Illuminate\Routing\Controller;
use Dt\Media\Services\Contracts\FileManagerInterface;
use Illuminate\Http\Response

class UploadController extends Controller
{
    protected $mediaService;
    protected $response;


    public function __construct(
        FileManagerInterface $mediaService,
        Response $response
    )
    {
        parent::__construct();
        $this->mediaService = $mediaService;
        $this->response = $response;
    }

    public function upload(UploadImagesRequest $request)
    {
        if ($request->hasFile('file') ) {
            $files = $request->validated();
            $results = $this->mediaService->upload($files);
        }
    }

}
