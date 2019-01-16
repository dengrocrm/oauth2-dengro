<?php

namespace Dengro\OAuth2\Client\Provider;

use Dengro\OAuth2\Client\Provider\Exception\DengroIdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Provider\AbstractProvider;
use Psr\Http\Message\ResponseInterface;

class Dengro extends AbstractProvider
{
    use BearerAuthorizationTrait;
    
    /**
     * DenGro API endpoint to retrieve logged in user information.
     *
     * @var string
     */
    const PATH_API_DETAILS = '/api/details';
    
    /**
     * DenGro OAuth server authorization endpoint.
     *
     * @var string
     */
    const PATH_AUTHORIZE = '/oauth/auth/';
    
    /**
     * DenGro OAuth server token request endpoint.
     *
     * @var string
     */
    const PATH_TOKEN = '/oauth/token/';
    
    /**
     * Domain
     *
     * @var string
     */
    protected $domain;
    
    /**
     * @param array $options
     * @param array $collaborators
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        if (empty($options['domain'])) {
            $message = 'The "domain" option not set. Please set a domain.';
            throw new \InvalidArgumentException($message);
        }
    }
    
    /**
     * Get domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
    
    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->domain.self::PATH_AUTHORIZE;
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->domain.self::PATH_TOKEN;
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->domain.self::PATH_API_DETAILS;
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return ["user-read"];
    }

    /**
     * Check a provider response for errors.
     *
     * @link   https://developer.github.com/v3/#client-errors
     * @link   https://developer.github.com/v3/oauth/#common-errors-for-the-access-token-request
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw DengroIdentityProviderException::clientException($response, $data);
        } elseif (isset($data['error'])) {
            throw DengroIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $user = new DengroResourceOwner($response);

        return $user->setDomain($this->domain);
    }
}
