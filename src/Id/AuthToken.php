<?php

namespace TotalCRM\TinkoffAcquiring\Id;

class AuthToken
{

    public string $accessToken;
    public int $expires;
    public ?string $refreshToken;

    /**
     * AuthToken constructor.
     * @param string $accessToken
     * @param int $expires
     * @param string|null $refreshToken
     */
    public function __construct(string $accessToken, int $expires, ?string $refreshToken = null)
    {
        $this->accessToken = $accessToken;
        $this->expires = $expires;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if ($this->expires > time()) {
            $isExpired = false;
        } else {
            $isExpired = true;
        }

        return $isExpired;
    }

}