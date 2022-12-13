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


Créer l'entity Account avec la propriété login
une page home pour les nom connecté
une page admin pour les admin
