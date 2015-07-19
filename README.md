# Laravel 5 OpenLDAP Auth
An OpenLDAP authentication driver for Laravel 5.

##Installation
Add to `composer.json` and install with `composer update` / `composer update`
```
{
  require: {
    "kcchung/l5-openldap-auth": "dev-master"
  }
}
```
or use `composer require kcchung/l5-openldap-auth` to install this package.

##Add to Laravel
Modify your `config/app.php` file and add the service provider to the providers array
```
kcchung\L5OpenldapAuth\LdapAuthServiceProvider::class,
```


