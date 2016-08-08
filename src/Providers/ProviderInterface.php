<?php

namespace NFSocial\Providers;

interface ProviderInterface
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();
    /**
     * Get the User instance for the authenticated user.
     *
     * @return \NFSocial\Providers\User
     */
    public function user();
}
