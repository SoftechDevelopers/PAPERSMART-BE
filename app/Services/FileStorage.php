<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileStorage
{
    // Copy a file to assest folder
    public static function storeFileInAssets(?string $subdir, UploadedFile $file){
        $assetsPath = env('ASSETS_PATH') . '/' . $subdir;

        $uniqueFileName = now()->format('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $file->move($assetsPath, $uniqueFileName);

        $fileUrl = $uniqueFileName;

        return $fileUrl;
    }

    // Delete a file
    public static function deleteFile(?string $subdir, string $fileName): bool {

        $filePath = env('ASSETS_PATH') . '/' . $subdir . '/'. $fileName;

        if (File::exists($filePath)) {
            File::delete($filePath);

            return true;
        }

        return false;
    }

    // Send file url for response payload
    public static function getUrl(?string $subdir, string $fileName){

        return config('app.url'). '/paper-assets/' . $subdir . '/'. $fileName;
    }
}
