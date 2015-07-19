<?php namespace kcchung\L5OpenldapAuth;

/**
 * @package   kcchung/l5-opendldap-auth
 * @author    kcchung <ken8203@gmail.com>
 * @copyright Copyright (c) kcchung
 * @licence   http://mit-license.org/
 * @link      https://github.com/ken8203/l5-openldap-auth
 */

use Log;
use Config;

class openLDAP {

    private $LDAP_SERVER;
    private $LDAP_RDN;
    private $LDAP_BASEDN;
    private $LDAP_GROUPDN;
    private $LDAP_VERSION;
    private $LDAP_LOGINATTR;
    private $groupList;
    private static $ldapConnectId = null;

    public function __construct()
    {
        $this->LDAP_SERVER = Config::get('ldap.host');
        $this->LDAP_RDN = Config::get('ldap.rdn');
        $this->LDAP_VERSION = Config::get('ldap.version');
        $this->LDAP_BASEDN = Config::get('ldap.basedn');
        $this->LDAP_GROUPDN = Config::get('ldap.groupdn');
        $this->LDAP_LOGINATTR = Config::get('ldap.login_attribute');

        if (is_null(self::$ldapConnectId))
            $this->connect();

        if (!empty($this->LDAP_GROUPDN))
            $this->groupList = $this->getGroupList();
    }

    public function __destruct()
    {
        if (! is_null(self::$ldapConnectId))
            ldap_unbind(self::$ldapConnectId);
    }

    public function connect()
    {
        if (($ldapconn = @ldap_connect($this->LDAP_SERVER)))
        {
            @ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, intval($this->LDAP_VERSION));
            @ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
            self::$ldapConnectId = $ldapconn;
        }
        else
            die("Connecting LDAP server failed.\n");
    }

    public function authenticate($username, $password)
    {
        if (empty($username) or empty($password))
        {
            Log::error('Error binding to LDAP: username or password empty');
            return false;
        }

        $ldapRdn = $this->LDAP_LOGINATTR . "=" . $username . "," . $this->LDAP_BASEDN;
        $isConnected = @ldap_bind(self::$ldapConnectId, $ldapRdn, $password);

        return $isConnected;
    }

    public function getUserData($identifier, $attr='')
    {
        $ldapFilter = "(&(" . $this->LDAP_LOGINATTR . "=". $identifier . ")(sn=" . $identifier . "))";
        if (!is_array($attr))
            $attr = array();

        $searchId = @ldap_search(self::$ldapConnectId, $this->LDAP_BASEDN, $ldapFilter, $attr);
        if (!$searchId)
            return -1;

        $userInfo = @ldap_get_entries(self::$ldapConnectId, $searchId);
        if ($userInfo['count'] == 1)
            return $userInfo[0];
        else
            return false;
    }

    public function getGroupList()
    {
        $ldapFilter = "(cn=*)";
        $attr = array("cn", "gidNumber");
        $searchId = @ldap_search(self::$ldapConnectId, $this->LDAP_GROUPDN, $ldapFilter, $attr);

        if (!$searchId)
            return false;

        $info = @ldap_get_entries(self::$ldapConnectId, $searchId);
        $groupList = array();
        foreach ($info as $each)
        {
            if (!empty($each["cn"][0]))
                $groupList[$each["gidnumber"][0]] = $each["cn"][0];
        }

        return $groupList;
    }

    public function whichGroup($identifier)
    {
        $gidnumber = strval($this->getUserData($identifier)['gidnumber'][0]);
        return $this->groupList[$gidnumber];
    }

    public function groupIsOK()
    {
        return false;
    }
}

?>