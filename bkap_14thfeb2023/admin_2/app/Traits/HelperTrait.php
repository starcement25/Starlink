<?php
namespace App\Traits;

trait HelperTrait{

    public function uploadFile($file, $folder = "")
    {
        $filename = $this->getUniqueId() . "." . $file->getClientOriginalExtension();
        $location = public_path(). '/'. $folder;
        $file->move($location, $filename);
        return ['fileName'=> $filename, "path" => $folder."/".$filename] ;
    }

    public function getUniqueId()
    {
        return md5(microtime().\Config::get('app.key'));
    }
}