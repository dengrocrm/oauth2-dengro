<?php

namespace Dengro\OAuth2\Client\Provider;

use League\OAuth2\Client\Tool\ArrayAccessorTrait;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class DengroResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;
    
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;
    
    /**
     * Creates new resource owner.
     *
     * @param array  $response
     */
    public function __construct(array $response = array())
    {
        $this->response = $response;
    }
    
    /**
     * Get resource owner id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getValueByKey($this->response, 'uuid');
    }
    
    /**
     * Get resource owner email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getValueByKey($this->response, 'email');
    }
    
    /**
     * Get resource owner name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getValueByKey($this->response, 'full_name');
    }
    
    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }
}
