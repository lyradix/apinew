<?php
// filepath: src/Security/LoginSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        // Generate a random token
        $apiToken = bin2hex(random_bytes(32));
        
        // Store token in session
        $session = $request->getSession();
        $session->set('api_token', $apiToken);
        
        // Store token in a JavaScript variable
        $request->getSession()->set('js_token', sprintf(
            'window.apiToken = "%s";',
            $apiToken
        ));
        
        // Redirect to /admin-concert after successful login
        return new RedirectResponse($this->router->generate('app_adminConcerts'));
   
    }
}