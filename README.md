# Services Generator bundle for Symfony2

This package contains a bundle to easily create basic services efficiently and without effort by command line.

The package add a new command line <strong>app/console generate:service</strong> that create all the config and code for a basic service:

- Modify the services.xml (only works for xml language)
- Create a Lib Class
- Inject dependencies (Entity Manager)

## How to:

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
