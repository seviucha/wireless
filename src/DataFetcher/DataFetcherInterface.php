<?php

namespace App\DataFetcher;

interface DataFetcherInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}
