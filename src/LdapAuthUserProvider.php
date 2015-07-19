<?php namespace kcchung\L5OpenldapAuth;

/**
 * @package   kcchung/l5-opendldap-auth
 * @author    kcchung <ken8203@gmail.com>
 * @copyright Copyright (c) kcchung
 * @licence   http://mit-license.org/
 * @link      https://github.com/ken8203/l5-openldap-auth
 */

use Log;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as UserProviderInterface;

class LdapAuthUserProvider implements UserProviderInterface {
    /**
     * Stores connection to LDAP.
     *
     * @var openLDAP
     */
    protected $openLDAP;
    /**
     * Creates a new LdapUserProvider and connect to Ldap
     *
     * @param array $config
     * @return void
     */
    public function __construct()
    {
        $this->openLDAP = new openLDAP();
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return Authenticatable
     */
    public function retrieveById($identifier)
    {
        $userInfo = $this->openLDAP->getUserData($identifier);        
        $credentials = array();
        $credentials['username'] = $identifier;
        $credentials['group'] = $this->openLDAP->whichGroup($credentials['username']);

        foreach($userInfo as $key => $value)
            $credentials[$key] = $value[0];

        return new LdapUser($credentials);
    }
    /**
     * Retrieve a user by by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }
    /**
     * @param Authenticatable $user
     * @param string $token
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if ($this->openLDAP->authenticate($credentials['username'], $credentials['password']))
        {
            $userInfo = $this->openLDAP->getUserData($credentials['username']);
            $credentials['group'] = $this->openLDAP->whichGroup($credentials['username']);
            foreach($userInfo as $key => $value)
                $credentials[$key] = $value[0];

            return new LdapUser($credentials);
        }
    }
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];
        return $this->openLDAP->authenticate($username, $password);
    }
}