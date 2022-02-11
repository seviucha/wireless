<?php

namespace App\ContentProvider;

class ComesconnectedContentProvider implements ContentProviderInterface
{
    /**
     * @var string
     */
    private string $src;

    /**
     * ComesconnectedContentProvider constructor.
     *
     * @param string $src
     */
    public function __construct(string $src)
    {
        $this->src = $src;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(): string
    {
        return file_get_contents($this->src);
    }
}
