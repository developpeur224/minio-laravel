<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaStorageService
{
    protected string $disk;

    public function __construct()
    {
        $this->disk = config('filesystems.default', 's3');
    }

    public function upload(UploadedFile $file, string $type = 'other'): array
    {
        $path = "media/{$type}/" . Str::uuid() . '_' . $file->getClientOriginalName();
        $storedPath = Storage::disk($this->disk)->putFileAs("media/{$type}", $file, basename($path));

        return [
            'path' => $storedPath,
            'url'  => $this->getUrl($storedPath),
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'disk' => $this->disk,
        ];
    }

    public function getUrl(string $path): string
    {
        try {
            return Storage::disk($this->disk)->url($path);
        } catch (\Exception $e) {
            return Storage::disk($this->disk)->temporaryUrl($path, now()->addMinutes(15));
        }
    }

    /**
     * Supprime un fichier du disque.
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }
}
