<?php namespace Shark\UploadManager;

use View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class UploadManager {

    private $upload_dir, $temp_dir, $filenames;

    /**
     * Create a new profiler instance.
     *
     * @param  \Illuminate\View\Environment  $view
     * @return void
     */
    public function __construct()
    {
        $this->upload_dir = Config::get('upload-manager::files_directory');
        $this->temp_dir = Config::get('upload-manager::temp_files_dir');
        $this->filenames = array();
    }
    /**
     * Show the upload form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
       return View::make('upload-manager::index');
    }

    public function processUpload()
    {
        // Load Autoloader of Flow php server
        \Flow\Autoloader::register();

        $config = new \Flow\Config(array(
            'tempDir' => $this->temp_dir
        ));

        $request = new \Flow\Request();

        if (\Flow\Basic::save($this->upload_dir . DIRECTORY_SEPARATOR . $request->getFileName(), $config, $request)) {
            return "Hurray, file was saved in " . $this->upload_dir . DIRECTORY_SEPARATOR . $request->getFileName();
        }
        exit;
    }

    public function saveToDB() 
    {
        if (Request::isMethod('post'))
        {
            $input = Input::all();
            $titles = Input::get('titles');
            $filenames = Input::get('filenames');
            $tags = Input::get('tags');
            
            
            return array('status'=>'OK', 'msg' => var_export($input, true));
        }
    }
    
    public function getTags() 
    {
        if (Request::isMethod('get'))
        {
            $input = Input::get('query');
            return Response::json(array(array('text' => 'Steve'), array('text' => 'CA')));
        }
    }
    
    public function removeFiles()
    {
       $filenames =  Input::get('filenames');

        foreach ($filenames as $filename) {
           $filename = realpath(public_path()) . DIRECTORY_SEPARATOR . $this->upload_dir . DIRECTORY_SEPARATOR . $filename;

            if (is_file($filename)) {
                unlink($filename);
            }
        }

    }
}