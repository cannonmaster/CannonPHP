<?php

namespace Engine\Interface;

interface ServiceProviderInterface
{
    public function register(\Engine\Di $di);
}
