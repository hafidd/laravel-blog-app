<?php

namespace App\Services;

class UploadService
{
    public function upload($userId, $file, $cloudinaryPath, $localPath)
    {
        // upload pic
        if (config('app.use_cloudinary')) {
            // cloudinary
            $result = $file->storeOnCloudinary($cloudinaryPath . $userId);
            return $result->getSecurePath();
        } else {
            // local
            return '/storage/' . $file->store($localPath, 'public');
        }
    }

    public function uploadProfile($userId, $file, $cloudinaryPath = 'laravel-blog/users/', $localPath = 'users')
    {
        return $this->upload($userId, $file, $cloudinaryPath, $localPath);
    }

    public function uploadPost($userId, $file, $cloudinaryPath = 'laravel-blog/images/', $localPath = 'post')
    {      
        return $this->upload($userId, $file, $cloudinaryPath, $localPath);
    }
}
