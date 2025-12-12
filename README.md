# Cas connection Bundle

The bundle make and authenicator with and phpCas based on the login of user.
If you want multiple authenticator anser yes for `does need passwords => yes`.

[offical documentation](https://symfony.com/doc/current/security.html).

## installation
The authenticator use 2 role:
 - USER
 - ADMIN

Applications that use Symfony Flex
----------------------------------
Open a command console, enter your project directory and execute:

```console
$ composer require dsi-iepg/cas-connection
```

-------------------------------
Create `User` with `login`
```console
$ php bin/console make:user
```
   - name class => User
   - store user data in the database => yes
   - property name => login
   - does need passwords => no

-------------------------------
make and play migrations with Doctrine.

## files modifications

Add in `config/bundles.php`
```
    return [
      ...
        Iepg\Bundle\Cas\CasConnectionBundle::class => ['all' => true],
    ]
```

Add in `.env`
```
#.env
...
#### Parameters for CAS connection ###
CAS_HOST=cas-adresse.com
# This value is optional
# If it's empty the path will the base url"
# example: scheme://httpHost+BasePath"
CAS_PATH=
# Default value 443"
CAS_PORT=443
# This value is optional
# if it's 'false' you don't use ceretificat
# THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION!
CAS_CA=false
# The path start to the DocumentRoot generaly public
# example if your file was at the root of project 
# CAS_CA_PATH=../certificat
CAS_CA_PATH=
# this value is optional. You can custome the NAME of the dispatcher route
# Where does the user go according to his role 
# The default value is 'cas_dispatcher'
CAS_DISPATCHER_NAME=
# this value is optional. YOU CAN CUSTOMIZE THE PAGE WHEN A USER IS SUCCESSFULLY AUTHENTICATED 
# BUT DOES NOT HAVE ACCESS RIGHTS TO THIS APPLICATION
CAS_USER_UNKNOW=
#### end of Cas-connection ####
...
```

Add in `config/packages/security.yaml`

```
#config/packages/security.yaml
...
   firewalls:
      main:
         provider: app_user_provider
         custom_authenticator: Iepg\Bundle\Cas\Controller\CasAuthenticator
...
```

Or if you use multiple authenticators
```
#config/packages/security.yaml
   ...
   firewalls:
      main:
         ...
         #choose the first authenticator you want.
         entry_point: App\Security\AppAuthenticator
         custom_authenticator: 
               - Iepg\Bundle\Cas\Controller\CasAuthenticator
               - App\Security\AppAuthenticator
         ...
```
And add 
```
 access_control:
        - { path: ^/cas_, roles: IS_AUTHENTICATED_ANONYMOUSLY  }
        - ...
```
WARNING! For attention reasons, please avoid starting your own route with 'cas_'

## add Files

Add file in `config/packages/cas_connection.yaml`
```
#config/packages/cas_connection.yaml
cas_connection:
    cas_host: "%env(CAS_HOST)%"
    cas_path: "%env(CAS_PATH)%"
    cas_port: "%env(int:CAS_PORT)%"
    cas_ca: "%env(bool:CAS_CA)%"
    cas_ca_path: "%env(CAS_CA_PATH)%"
    cas_dispatcher_name: "%env(CAS_DISPATCHER_NAME)%"

twig:
    paths:
        "%kernel.project_dir%/vendor/dsi-iepg/cas-connection/src/Resources/views": cas_connection

```

<!-- plus nécessaire en php8 Add file in `config/routes/cas_connection.yaml`
```
#config/routes/cas_connection.yaml
cas_connection:
    resource: '@CasConnectionBundle/Resources/config/routes.yaml'
    prefix: /cas_connection

``` -->

Add in file `config/routes.yaml`

``` yaml
#config/routes/cas_connection.yaml
cas_login:
    path: /cas_login
    controller: Iepg\Bundle\Cas\Controller\CasAuthenticator

cas_logout:
    path: /cas_logout

cas_dispatcher:
    path: /cas_dispatcher
    controller: Iepg\Bundle\Cas\Controller\CasController::dispatcher
```

Add file in `src/EventListener/logoutSubcriber.php`

``` yaml
//src/EventListener/logoutSubcriber.php
namespace App\EventListener;

use Iepg\Bundle\Cas\Controller\CasLogout;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    private $casLogout;
    
    public function __construct(CasLogout $casLogout)
    {
        $this->casLogout = $casLogout;
    }

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {

        $this->casLogout->logout($event->getRequest());

    }
}

```