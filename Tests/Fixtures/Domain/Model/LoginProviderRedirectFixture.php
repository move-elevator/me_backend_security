<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Fixture\Domain\Model
 */
trait LoginProviderRedirectFixture
{
    /**
     * @var string
     */
    protected $url = "http://localhost";

    /**
     * @return LoginProviderRedirect
     */
    protected function getFixtureObject()
    {
        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl($this->url);

        return $loginProviderRedirect;
    }
}
