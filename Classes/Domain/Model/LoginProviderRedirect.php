<?php

namespace MoveElevator\MeBackendSecurity\Domain\Model;

/**
 * @package MoveElevator\MeBackendSecurity\Domain\Model
 */
class LoginProviderRedirect
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
