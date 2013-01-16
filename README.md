# Services Generator bundle for Symfony2

This package contains a bundle to easily create basic services efficiently and without effort from command line.

The package add a new command line <strong>app/console generate:service</strong> that create all the config and code for a basic service:

- Modify the services.xml (only works for xml language only)
- Create a Lib Class
- Inject dependencies (Entity Manager)

## Installation

### Step 1: Install vendors

Installation depends on your version of Symfony:

#### Symfony 2.1.x: Composer

[Composer](http://packagist.org/about-composer) is a project dependency manager for PHP. You have to list
your dependencies in a `composer.json` file:

``` json
{
    "require-dev": {
        "jalis/service-generator": "*"
    }
}
```
To actually install Service Generator in your project, download the composer binary and run it:

``` bash
wget http://getcomposer.org/composer.phar
# or
curl -O http://getcomposer.org/composer.phar

php composer.phar install
```

#### Symfony 2.0.x: `bin/vendors.php` method

If you're using the `bin/vendors.php` method to manage your vendor libraries,
add the following entries to the `deps` in the root of your project file:

```
[JalisServiceGeneratorBundle]
    git=https://github.com/javijalis/JalisServiceGeneratorBundle.git
    target=/bundles/Jalis/Bundle/ServiceGeneratorBundle
```

Next, update your vendors by running:

``` bash
$ ./bin/vendors
```

Finally, add the following entries to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
  
    'Jalis'        => __DIR__.'/../vendor/bundles',
));
```


### Step 2: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Jalis\Bundle\ServiceGeneratorBundle\JalisServiceGeneratorBundle(), 
    );
}
```

## How to use:

Execute the command:

``` php

$ app/console generate:service
```
Follow the instructions:

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

The class for your code used in the service is in the folder BundleGiven/Manager/exampleManager.php 
``` php
<?php
namespace BundleGiven\Manager;                                                                                                                                                                                                                                             
                                                                                
use Doctrine\ORM\EntityManager;                                                 
                                                                                                                                                                                                                                          
class exampleManager                                                               
{                                                                               
    protected $em;                                                              
                                                                                
    public function __construct(EntityManager $em){                             
                                                                                
        $this->em = $em;                                              
    }                                                                  
                                                                                                                                                            
    public function getInfo() {                                                 
                                                                      
        return "name: exampleManager";                                                                                                                          
    }                                                                           
    
    //... your code
                                                                                
}             
```

### ToDo

- Option for create Twig extensions
- Add more options for inject more native services (monolog, mailer) and own services
- Refactor Command class code
- What about services in yml?
