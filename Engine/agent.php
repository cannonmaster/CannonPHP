<?php

namespace Engine;

class Agent
{
    /**
     * @var string User agent string
     */
    protected string $user_agent;

    /**
     * @var \Engine\Di Dependency Injection container
     */
    protected \Engine\Di $di;

    /**
     * Agent constructor.
     *
     * @param \Engine\Di $di The DI container
     * @param string|null $user_agent User agent string (optional)
     */
    public function __construct(\Engine\Di $di, ?string $user_agent = null)
    {
        $this->di = $di;
        $this->user_agent = $user_agent ?? $this->di->get('request')->server['HTTP_USER_AGENT'];
    }

    /**
     * Get the user agent string.
     *
     * @return string The user agent string
     */
    public function getUserAgentString(): string
    {
        return $this->user_agent;
    }

    /**
     * Check if the user agent represents a mobile device.
     *
     * @return bool True if the user agent is from a mobile device, false otherwise
     */
    public function isMobile(): bool
    {
        return (bool) preg_match('/(android|iphone|ipad|windows phone)/i', $this->user_agent);
    }

    /**
     * Check if the user agent represents a tablet device.
     *
     * @return bool True if the user agent is from a tablet device, false otherwise
     */
    public function isTablet(): bool
    {
        return (bool) preg_match('/(ipad)/i', $this->user_agent);
    }

    /**
     * Get the browser name from the user agent string.
     *
     * @return string The browser name
     */
    public function getBrowser(): string
    {
        $browser = 'Unknown';

        if (preg_match('/msie|trident/i', $this->user_agent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/firefox/i', $this->user_agent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/chrome|crios/i', $this->user_agent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $this->user_agent)) {
            $browser = 'Safari';
        } elseif (preg_match('/opera|opr/i', $this->user_agent)) {
            $browser = 'Opera';
        }

        return $browser;
    }

    /**
     * Get the operating system name from the user agent string.
     *
     * @return string The operating system name
     */
    public function getOperatingSystem(): string
    {
        $os = 'Unknown';

        if (preg_match('/windows/i', $this->user_agent)) {
            $os = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $this->user_agent)) {
            $os = 'Mac';
        } elseif (preg_match('/android/i', $this->user_agent)) {
            $os = 'Android';
        } elseif (preg_match('/iphone/i', $this->user_agent)) {
            $os = 'iPhone';
        } elseif (preg_match('/ipad/i', $this->user_agent)) {
            $os = 'iPad';
        } elseif (preg_match('/linux/i', $this->user_agent)) {
            $os = 'Linux';
        }

        return $os;
    }
}
