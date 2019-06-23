<?php
namespace App\Makers;

use ReflectionMethod;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Symfony\Component\Inflector\Inflector;

final class MakeCrudTest extends AbstractMaker
{
    private $doctrineHelper;

    /**
     * MakeCrudTest constructor.
     * @param $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }


    /**
     * Return the command name for your maker (e.g. make:report).
     *
     * @return string
     */
    public static function getCommandName(): string
    {
        return 'make:crud-test';
    }

    /**
     * Configure the command: set description, input arguments, options, etc.
     *
     * By default, all arguments will be asked interactively. If you want
     * to avoid that, use the $inputConfig->setArgumentAsNonInteractive() method.
     *
     * @param Command $command
     * @param InputConfiguration $inputConfig
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates tests for a CRUD controller')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('Class name of the entity to generate CRUD tests for (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
        ;
    }

    /**
     * Called after normal code generation: allows you to do anything.
     *
     * @param InputInterface $input
     * @param ConsoleStyle $io
     * @param Generator $generator
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $entityClassDetails = $generator->createClassNameDetails(
            Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()),
            'Entity\\'
        );

        $setters = $this->getSetters($entityClassDetails->getFullName());

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryClassDetails = $generator->createClassNameDetails(
            '\\'.$entityDoctrineDetails->getRepositoryClass(),
            'Repository\\',
            'Repository'
        );

        $repositoryVars = [
            'repository_full_class_name' => $repositoryClassDetails->getFullName(),
            'repository_class_name' => $repositoryClassDetails->getShortName(),
            'repository_var' => lcfirst(Inflector::singularize($repositoryClassDetails->getShortName())),
        ];

        $controllerClassDetails = $generator->createClassNameDetails(
            $entityClassDetails->getRelativeNameWithoutSuffix().'Controller',
            'Controller\\',
            'Controller'
        );

        $controllerTestClassDetails = $generator->createClassNameDetails(
            $controllerClassDetails->getRelativeName(),
            'Tests\\',
            'Test'
        );

        $generator->generateClass(
            $controllerTestClassDetails->getFullName(),
            __DIR__ . '/Resources/skeleton/test/CrudTest.tpl.php',
            array_merge([
                'route_path' => Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()),
                'entity_name' => $entityClassDetails->getShortName(),
                'entity_variable' => Str::asLowerCamelCase($entityClassDetails->getShortName()),
                'entity_setters' => $setters,
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_form_fields' => $entityDoctrineDetails->getFormFields(),
            ], $repositoryVars)
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new test class and start customizing it.',
            'Find the documentation at <fg=yellow>https://symfony.com/doc/current/testing.html#functional-tests</>',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            Client::class,
            'browser-kit',
            true,
            true
        );
        $dependencies->addClassDependency(
            CssSelectorConverter::class,
            'css-selector',
            true,
            true
        );
    }

    private function getSetters(string $class): array
    {
        if (!class_exists($class)) {
            return [];
        }

        $reflClass = new \ReflectionClass($class);

        return array_reduce(
            array_filter($reflClass->getMethods(ReflectionMethod::IS_PUBLIC), function (\ReflectionMethod $method) {
                return $method->isPublic()
                    && $method->getNumberOfParameters() > 0;
            }),
            function($setters, \ReflectionMethod $method) {

                $setters[$method->getName()] = array_map(
                    function(\ReflectionParameter $parameter) {
                        return '$' . $parameter->getName();
                    },
                    $method->getParameters()
            );

                return $setters;
            }, []);
    }
}