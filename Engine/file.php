<?php

namespace Engine;

class File
{
    /**
     * @var Di The dependency injection container
     */
    public Di $di;

    /**
     * @var Request The request object
     */
    public Request $request;

    /**
     * @var array The uploaded file data
     */
    public static array $file;

    /**
     * File constructor.
     *
     * @param Di $di The dependency injection container
     */
    public function __construct(Di $di)
    {
        $this->di = $di;
        self::$file = $this->di->get('request')->files;
    }

    /**
     * Handle file upload.
     *
     * @param string $fileInputName The name of the file input field
     * @param string $destination The destination directory to save the uploaded file
     * @param array|null $allowTypes An array of allowed file extensions
     * @param int|null $maxSize The maximum allowed file size in bytes
     * @param bool $sanitizeName Whether to sanitize the file name
     * @param string|null $newFilename The new file name (optional)
     * @return bool|string Returns the destination path if the upload is successful, false otherwise
     */
    public static function handleUpload(
        string $fileInputName,
        string $destination,
        array $allowTypes = null,
        int $maxSize = null,
        bool $sanitizeName = true,
        ?string $newFilename = null
    ): bool|string {
        if (!isset(static::$file[$fileInputName])) {
            return false;
        }
        $file = static::$file[$fileInputName];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $maxSize = $maxSize ?? \App\Config::file_upload_max_size;

        $allowTypes = $allowTypes ?? \App\Config::file_upload_allow_type;

        if (!in_array($fileExtension, $allowTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        if ($newFilename !== null) {
            $filename = $newFilename;
        } else {
            $filename = $sanitizeName ? self::sanitizeFileName($file['name']) : $file['name'];
        }

        $destinationPath = rtrim($destination, '/') . '/' . $filename;
        $counter = 1;

        while (file_exists($destinationPath)) {
            $filename = self::generateUniqueFilename($fileExtension, $counter);
            $destinationPath = rtrim($destination, '/') . '/' . $filename;
            $counter++;
        }

        if (move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return $destinationPath;
        }

        return false;
    }

    /**
     * Generate a unique filename with a counter.
     *
     * @param string $fileExtension The file extension
     * @param int $counter The counter value
     * @return string The generated unique filename
     */
    protected static function generateUniqueFilename(string $fileExtension, int $counter): string
    {
        $filename = 'file_' . $counter . '.' . $fileExtension;

        return $filename;
    }

    /**
     * Sanitize the file name by removing unwanted characters.
     *
     * @param string $filename The original file name
     * @return string The sanitized file name
     */
    protected static function sanitizeFileName(string $filename): string
    {
        $filename = preg_replace('/[^\w\s.-]/', '', $filename);
        $filename = str_replace(' ', '_', $filename);
        return $filename;
    }
}
