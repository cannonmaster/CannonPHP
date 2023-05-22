<?php

namespace Engine;

use Core\BaseController;

/**
 * Class Language
 * Manages language-related operations and translations.
 */
class Language
{
    public string $code;
    protected string $directory;
    protected array $path = [];
    protected array $data = [];
    protected array $cache = [];
    protected \Engine\Di $di;

    /**
     * Language constructor.
     *
     * @param string       $code       The language code
     * @param string       $directory  The directory containing language files
     * @param \Engine\Di   $di         The dependency injection container
     */
    public function __construct(string $code, string $directory, \Engine\Di $di)
    {
        $this->directory = $directory;
        $this->di = $di;
        $this->code = $this->findLanguage();
    }

    /**
     * Determines the language code based on the request data.
     *
     * @return string The language code
     */
    private function findLanguage(): string
    {
        $request = $this->di->get('request');
        $code =  $request->get['language'] ?? $request->cookie['language'] ??  $request->server['HTTP_ACCEPT_LANGUAGE'];
        $code = $this->sanitizeLanguageCode($code);

        return $code;
    }

    /**
     * Sanitizes a language code, ensuring it is in a valid format and supported.
     *
     * @param string $language The language code to sanitize
     * @return string The sanitized language code
     */
    private function sanitizeLanguageCode(string $language): string
    {
        $language = strtolower($language);
        $language = preg_replace('/[^a-z\-]/', '', $language);
        $supported_languages = \App\Config::support_languages;
        if (!in_array($language, $supported_languages)) {
            $language = 'en';
        }
        return $language;
    }

    /**
     * Retrieves a translation value for the specified key.
     *
     * @param string $key The translation key
     * @return mixed|null The translation value, or null if not found
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Sets a translation value for the specified key.
     *
     * @param string $key   The translation key
     * @param mixed  $data  The translation value
     */
    public function set(string $key, $data)
    {
        $this->data[$key] = $data;
    }

    /**
     * Loads the translation file for the specified language and merges the translations with the existing data.
     *
     * @param string $filename The filename of the translation file
     * @param string $code     The language code (optional, defaults to the current language code)
     * @return array The merged translation data
     * @throws \Exception If the language file is not found
     */
    public function load(string $filename, string $code = ''): array
    {
        $code = $code ?: $this->code;

        if (!isset($this->cache[$code][$filename])) {
            $_ = [];
            $file = realpath($this->directory . $code . '/' . $filename . '.php');

            if ($file && is_file($file)) {
                require($file);
                $this->cache[$code][$filename] = $_;
            } else {
                throw new \Exception('Language file not found: ' . $filename);
            }
        } else {
            $_ = $this->cache[$code][$filename];
        }
        $this->data = array_merge($this->data, $_);

        return $this->data;
    }
}
