<?php

namespace App\ContentProvider;

interface ContentProviderInterface
{
    /**
     * @return string
     */
    public function getContent(): string;
}
