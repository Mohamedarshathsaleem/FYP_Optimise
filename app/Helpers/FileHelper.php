<?php

namespace App\Helpers;

use File;

class FileHelper
{
    public static function ensureDirectoryExists($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        return $path;
    }

    public static function uploadFile($file, $directory, $prefix = '')
    {
        $uploadPath = public_path($directory);
        self::ensureDirectoryExists($uploadPath);

        $filename = $prefix . time() . '_' . \Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);

        return $directory . '/' . $filename;
    }

    public static function deleteFile($path)
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
            return true;
        }
        return false;
    }
}
