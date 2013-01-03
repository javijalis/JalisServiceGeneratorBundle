<?php
namespace Jalis\Bundle\ServiceGeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Jalis\Bundle\ServiceGeneratorBundle\Generator\ServiceGenerator;
use Symfony\Component\HttpKernel\KernelInterface;

class GenerateServiceCommand extends ContainerAwareCommand
{

    private $generator;

    protected function configure()
    {
        $this
            ->setName('generate:service')
            ->setDescription('Create a basic Service')
            ->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'The namespace of the bundle to create the service')
            ->addOption('service', null, InputOption::VALUE_REQUIRED, 'The name of the service')
            ->addOption('em', null, InputOption::VALUE_NONE, 'With Entity Manager')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
  
        $dialog = $this->getDialogHelper();
        $path = dirname(__DIR__); 
        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'));

        $bundle = strtr($namespace, array('\\' => ''));
        $service_name =   $input->getOption('service');
       $em = $input->getOption('em'); 

       $dir = Validators::validateTargetDir(dirname($this->getContainer()->getParameter('kernel.root_dir')).'/src/'.$namespace, $bundle, $namespace);
       $dir = strtr($dir, '\\', '/');

        $dialog->writeSection($output, 'Service Generator');
        $parameters = array(
            'prueba' => 'Hola',
            'service'   => $input->getOption('service'),
            'bundle' => $bundle,
            'namespace' => $namespace,
            'em' => $em
             );
        // lib class for service
        $target = $path.'/Manager';
        if (!is_dir(dirname($target))) {
            mkdir($target, 0777, true);
        }

        $this->renderFile($path.'/Resources/views', 'serviceTemplate.php.twig', $dir.'/Manager/'.$service_name.'Manager.php', $parameters);
        
        // service.xml processor
        $file = $dir.'Resources/config/services.xml';
        $xml = simplexml_load_file($file);
        $xse = new \SimpleXMLElement($xml->asXML());


        $xml_parameters = $xse->parameters[0];
        if(!$xml_parameters) { $xml_parameters = $xse->addChild('parameters',''); }
        $xml_parameter = $xml_parameters->addChild('parameter', $namespace.'\\Manager\\'.$service_name.'Manager');
        $xml_parameter->addAttribute('key', $bundle.'.'.$service_name.'.class');



        $servicios = $xse->services[0];
        if(!$servicios) { $servicios= $xse->addChild('services',''); }
        $servicio = $servicios->addChild('service', '');
        $servicio->addAttribute('id',$service_name.'Manager');
        $servicio->addAttribute('class', '%'.$bundle.'.'.$service_name.'.class%');
        if($em  == true) {
            $argument = $servicio->addChild('argument','');
            $argument->addAttribute('type','service');
            $argument->addAttribute('id','doctrine.orm.entity_manager');
            $dialog->writeSection($output, 'Entity Manager injected');
        }
        $xse->asXML($file);
        // fin service xml processor



        $dialog->writeSection($output, 'Service "'.$service_name.'Manager" created in '.$dir.' for use in your controller: $this->get("'.$service_name.'Manager");');

    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Symfony2 service generator');

        // Namespace
        $output->writeln(array(
                    '',
                    'Your service code must be written in <comment>Manager directory</comment>. This command helps',
                    'you generate them easily.',
                    '',
                    'Each service is hosted under a namespace (like <comment>Acme/Bundle/BlogBundle</comment>).',
                    '(which must have <comment>Bundle</comment> as a suffix).',
                    '',
                    ));

        $namespace = $dialog->askAndValidate($output, $dialog->getQuestion('Bundle namespace', $input->getOption('namespace')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateBundleNamespace'), false, $input->getOption('namespace'));
        $input->setOption('namespace', $namespace);


        // Bundle
        $bundle = strtr($namespace, array('\\' => ''));

        //Service name
        $service = $input->getOption('service');
        $output->writeln(array(
                    '',
                    'Your service must have a <comment>name</comment> for call it',
                    '',
                    ));

        $service = $dialog->askAndValidate($output, $dialog->getQuestion('Service Name', $service), function ($service) use ($bundle, $namespace) { return $service; }, false, $input->getOption('service'));
        $input->setOption('service', $service);
       // Entity Manager
        $output->writeln(array(
                    '',
                    'Your service need <comment>EntityManager</comment>?',
                    '',
                    ));
        $em = $input->getOption('em');
     if (!$em && $dialog->askConfirmation($output, $dialog->getQuestion('Do you need entity Manager in your service', 'no', '?'), false)) {
            $em = true;
        }

        $input->setOption('em', $em);



    }


    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new ServiceGenerator();
        }

        return $this->generator;
    }

    public function setGenerator(ServiceGenerator $generator)
    {
        $this->generator = $generator;
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
    protected function render($skeletonDir, $template, $parameters)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($skeletonDir), array(
                    'debug'            => true,
                    'cache'            => false,
                    'strict_variables' => true,
                    'autoescape'       => false,
                    ));

        return $twig->render($template, $parameters);
    }

    protected function renderFile($skeletonDir, $template, $target, $parameters)
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($skeletonDir, $template, $parameters));
    }


}
