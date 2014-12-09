<?php namespace Shark\UploadManager\Facades;

use Illuminate\Support\Facades\Facade;

class UploadManager extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'upload-manager'; }

}