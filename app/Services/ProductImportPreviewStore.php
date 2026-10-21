<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImportPreviewStore
{
    public static function cacheKey(string $token): string
    {
        return 'product_import_preview_' . $token;
    }

    public static function rowsFilePath(string $token): string
    {
        return 'temp/product-imports/' . $token . '.jsonl';
    }

    public static function createPendingFromUpload($uploadedFile, int $userId, int $branchId): string
    {
        $token     = (string) Str::uuid();
        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: 'xlsx');

        Storage::makeDirectory('temp/product-imports');
        $uploadRelative = 'temp/product-imports/' . $token . '_source.' . $extension;
        $uploadedFile->storeAs('temp/product-imports', $token . '_source.' . $extension);

        Cache::put(self::cacheKey($token), [
            'user_id'        => $userId,
            'branch_id'      => $branchId,
            'file_name'      => (string) $uploadedFile->getClientOriginalName(),
            'upload_file'    => $uploadRelative,
            'rows_file'      => self::rowsFilePath($token),
            'status'         => 'pending',
            'total_count'    => 0,
            'valid_count'    => 0,
            'processed_rows' => 0,
            'lookups'        => null,
            'error'          => null,
            'created_at'     => now()->toDateTimeString(),
        ], now()->addHours(3));

        return $token;
    }
}
