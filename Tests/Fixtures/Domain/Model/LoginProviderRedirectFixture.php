<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;

trait LoginProviderRedirectFixture
{
    protected string $url = "http://foo.bar";

    protected function getLoginProviderRedirectFixture(): LoginProviderRedirect
    {
        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl($this->url);

        return $loginProviderRedirect;
    }
}
