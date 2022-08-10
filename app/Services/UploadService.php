<?php

namespace App\Services;

class UploadService
{
    public function uploadProfile($userId, $file)
    {
        // upload
        if (config('app.use_cloudinary')) {
            // cloudinary
            $result = $file->storeOnCloudinary('laravel-blog/users/' . $userId);
            return $result->getSecurePath();
        } else {
            // local
            return '/storage/' . request()->picture->store('users', 'public');
        }
    }
}
