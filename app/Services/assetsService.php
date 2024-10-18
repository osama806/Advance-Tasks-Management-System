<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class assetsService {

    public function storeImage($file)
    {
        $originalName = $file->getClientOriginalName();

        // Ensure the file extension is valid and there is no path traversal in the file name
        if (preg_match('/\.[^.]+\./', $originalName)) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }

        // Check for path traversal attack (e.g., using ../ or ..\ or / to go up directories)
        if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.pathTraversalDetected'), 403);
        }

        // Validate the MIME type to ensure it's an image
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $mime_type = $file->getClientMimeType();

        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }

        // Generate a safe, random file name
        $fileName = Str::random(32);

        $extension = $file->getClientOriginalExtension(); // Safe way to get file extension
        $filePath = "Images/{$fileName}.{$extension}";

        // Store the file securely
        $path = Storage::disk('local')->putFileAs('Images', $file, $fileName . '.' . $extension);

        // Get the full URL path of the stored file
        $url = Storage::disk('local')->url($path);

        // Return the URL of the uploaded image
        return $url;
    }


}
