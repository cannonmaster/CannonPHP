<?php

namespace Engine;

class Theme
{
    public \Engine\Di $di;
    public string $theme;

    /**
     * Theme constructor.
     *
     * @param string   $theme The theme name.
     * @param Di       $di    The dependency injection container.
     */
    public function __construct(string $theme, \Engine\Di $di)
    {
        $this->theme = $theme;
        $this->di = $di;
    }
}
