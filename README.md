# Generator Services bundle for Symfony2

This package contains a bundle to easily create basic services efficiently and without effort by line command, ready for use.

This add a new command line app/console generate:service that create all the config and code for a basic service:

- Modify the services.xml
- Create a Lib Class

## HOW TO:

``` php

$ app/console generate:service
```

``` php
Your service code must be written in Manager directory. This command helps
you generate them easily.

Each service is hosted under a namespace (like Acme/Bundle/BlogBundle).
(which must have Bundle as a suffix).

Bundle namespace: myFolder/Bundle/myBundle

Your service must have a name for call it

Service Name: example

Your service need EntityManager?

Do you need entity Manager in your service [no]? yes

```
After that, you only have to use the service where ever you want:

``` php
$my_service = $this->get('exampleManager');
```

You can write your code for the service in the folder "Manager" created, in the class nameManager.php
