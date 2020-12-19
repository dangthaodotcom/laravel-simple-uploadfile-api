<?php

namespace Dt\Media\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name'         => $this->name,
            'hashed_name'  => $this->hashed_name,
            'path'         => $this->path,
            'resize_path'  => $this->resize_path,
            'extension'    => $this->extension,
            'url_computed' => $this->url_computed,
            'url_resized'  => $this->url_resized,
        ];
    }
}
