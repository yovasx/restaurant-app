<?php

if (!function_exists('media_url')) {
    function media_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, '/')) {
            return asset($path);
        }
        return asset('storage/' . $path);
    }
}

if (!function_exists('resolve_media_input')) {
    function resolve_media_input(\Illuminate\Http\Request $request, string $field, string $storagePath): ?string
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store($storagePath, 'public');
        }
        $value = $request->input($field);
        if (!empty($value) && (str_starts_with($value, 'http://') || str_starts_with($value, 'https://'))) {
            return $value;
        }
        return null;
    }
}

if (!function_exists('resolve_media_value')) {
    function resolve_media_value(mixed $value, string $storagePath): ?string
    {
        if ($value instanceof \Illuminate\Http\UploadedFile) {
            return $value->store($storagePath, 'public');
        }
        if (is_string($value) && !empty($value) && (str_starts_with($value, 'http://') || str_starts_with($value, 'https://'))) {
            return $value;
        }
        return null;
    }
}
