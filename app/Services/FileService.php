<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;

//use Intervention\Image\Image;
//use Intervention\Image\ImageManagerStatic as Image;

//use Intervention\Image\ImageManager as Image;

//use Intervention\Image\ImageManagerStatic;

// use Intervention\Image\ImageManager;
// use Intervention\Image\Drivers\Imagick\Driver;


class FileService
{
    public function updateFile($model, $request, $type)
    {
        if (!empty($model->file)) {
            $currentFile = public_path() . $model->file;

            if (file_exists($currentFile) && $currentFile != public_path() . '/user-placeholder.png') {
                unlink($currentFile);
            }
        }

        $file = null;
        if ($type === "user") {
            $file = Image::read($request->file('file'))->resize(400, 400);
        } else {
            $file = Image::read($request->file('file'));
        }
        $ext = $request->file('file');
        $extension = $ext->getClientOriginalExtension();
        $name = time() . '.' . $extension;
        $file->save(public_path() . '/file/' . $name);
        $model->file = '/file/' .$name;

        return $model;
    }
}
