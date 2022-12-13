https://nouvelle-techno.fr/articles/symfony-5-on-va-creer-un-bundle-niveau-moyen-avance


add
config/bundlex.php
```
Iepg\Bundle\Cas\CasConnectionBundle::class => ['all' => true],
```

config/routes/cas_connection.yaml
```
cas_connection:
  resource: '@CasConnectionBundle/Resources/config/routes.yaml'
  prefix: /cas-connection
```


create => config/packages/cas_connection.yaml
Ici le 'iepg_test' vient de:
/DependencyInjection/Configuration/ =>  new TreeBuilder('iepg_test');
```
# Ici la base est l'alias du bundle !!
cas_connection:
    cas_host: "%env(CAS_HOST)%"
    cas_path: "%env(CAS_PATH)%"
    cas_port: '%env(int:CAS_PORT)%'
    cas_ca: '%env(bool:CAS_CA)%'
    cas_ca_path: "%env(CAS_CA_PATH)%"
```

add config/packages/twig.yaml
twig:
    paths:
        '%kernel.project_dir%/vendor/dsi-iepg/cas-connection/src/Resources/views': cas_connection

.env
```
## CAS ##
CAS_HOST=xx
CAS_PATH=xx
CAS_PORT=443
CAS_CA=false
CAS_CA_PATH=xx
## end CAS ##
```

```

```

add in config/packages/security.yaml
security:
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: login
    
    firewalls:
        main:
            provider: app_user_provider
            # TODO ajouter l'authentificateur du Bundle
            custom_authenticator: Iepg\Bundle\Cas\Controller\CasAuthenticator

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/cas-connection/cas-admin, roles: ROLE_ADMIN }
        - { path: ^/cas-connection/cas-user, roles: ROLE_USER }

Créer l'entity Account avec la propriété login
une page home pour les nom connecté
une page admin pour les admin
