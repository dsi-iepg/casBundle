Cas connection Bundle
============

The bundle make and authenicator with and phpCas based on the login of user.
If you want multiple authenticator anser yes for `does need passwords => yes`.

[offical documentation](https://symfony.com/doc/current/security.html?target=_blank).

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
