<?php

declare(strict_types=1);

namespace Jorijn\Bitcoin\Dca\Client;

interface Bl3pClientInterface
{
    /**
     * To make a call to BL3P API.
     *
     * @param string $path       path to call
     * @param array  $parameters parameters to add to the call
     *
     * @return array result of call
     *@throws \Exception
     *
     */
    public function apiCall($path, $parameters = []): array;
}
