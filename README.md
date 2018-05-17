# Laravel 5 OpenLDAP Auth
An OpenLDAP authentication driver for Laravel 5.

## Installation
Add to `composer.json` and install with `composer install` / `composer update`.
```
{
  require: {
    "kcchung/l5-openldap-auth": "dev-master"
  }
}
```

## Add to Laravel
Open your `config/app.php` file and add the service provider to the providers array.
```
kcchung\L5OpenldapAuth\LdapAuthServiceProvider::class
```
Update your `config/auth.php` to use `ldap` driver.
```
'driver' => 'ldap'
```

## Configuration
Manually create a `config/ldap.php` file and add the following:
```
<?php

return [
    'host'      => 'ldaps://example.com',
    'rdn'       => 'ou=System,dc=example,dc=com', // rdn used by the user configured below, optional
    'version'   => '3', // LDAP protocol version (2 or 3)
    
    'basedn'    => 'ou=People,dc=example,dc=com', // basedn for users
    'login_attribute' => 'uid', // login attributes for users
];

?>
```

## Extending
If you wish to add your own functions, just modify any of the classes.

## About
Created by [Kuan-Chien Chung(kcchung)](http://jaychung.tw)
