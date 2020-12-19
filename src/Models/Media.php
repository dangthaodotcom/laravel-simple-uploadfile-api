<?php

namespace Dt\Media\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['name', 'hashed_name', 'path', 'minetype','extension', 'user_id', 'admin_id'];
    protected $table = 'media';
    protected $appends = array('url_computed');

    public function getUrlComputedAttribute()
    {
        return config('app.static_server').'/'.$this->path.$this->name.'-'.$this->hashed_name.'.'.$this->extension;
    }
}
