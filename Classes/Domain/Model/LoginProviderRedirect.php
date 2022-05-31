<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Domain\Model;

class LoginProviderRedirect
{
    protected string $url;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
