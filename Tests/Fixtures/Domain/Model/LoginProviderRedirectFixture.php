<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model
 */
trait LoginProviderRedirectFixture
{
    /**
     * @var string
     */
    protected $url = "http://foo.bar";

    /**
     * @return LoginProviderRedirect
     */
    protected function getLoginProviderRedirectFixture(): LoginProviderRedirect
    {
        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl($this->url);

        return $loginProviderRedirect;
    }
}
