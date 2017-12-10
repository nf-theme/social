<?php
namespace NightFury\Social\Contracts;

interface Provider
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
     * @return \NightFury\Social\Contracts\User
     */
    public function user();
}
