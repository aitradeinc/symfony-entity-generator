<?php

namespace EntityGenerator\Generator;

use ReflectionClass;
use ReflectionException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

class Renderer
{
    /** @var Environment */
    private $twig;

    public function __construct(string $templatePath)
    {
        $loader = new FilesystemLoader($templatePath);
        $this->twig = new Environment($loader, [
            'cache' => false
        ]);

        $camelizeFilter = new TwigFilter('camelize', [Namer::class, 'camelize']);
        $entityNameFilter = new TwigFilter('entity_name', [Namer::class, 'entityName']);
        $relate = new TwigFilter('relate', [Namer::class, 'relate']);
        $pluralize = new TwigFilter('pluralize', [Namer::class, 'pluralize']);

        $phpTypeFilter = new TwigFilter('php_type', [TypeMapper::class, 'phpType']);
        $doctrineTypeFilter = new TwigFilter('doctrine_type', [TypeMapper::class, 'doctrineType']);
        $ucfirst = new TwigFilter('ucfirst', 'ucfirst');

        $this->twig->addFilter($phpTypeFilter);
        $this->twig->addFilter($doctrineTypeFilter);
        $this->twig->addFilter($camelizeFilter);
        $this->twig->addFilter($entityNameFilter);
        $this->twig->addFilter($relate);
        $this->twig->addFilter($ucfirst);
        $this->twig->addFilter($pluralize);
    }

    /**
     * @throws SyntaxError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render(Entity $entity, string $namespace, string $collectionInterface, string $collectionImplementation): string
    {
        $repositoryNamespace = str_replace('Entity', 'Repository', $namespace);
        $repository = $entity->getTable() . 'Repository';

        $reflectCollectionInterface = new ReflectionClass($collectionInterface);
        $collectionInterfaceShortName = $reflectCollectionInterface->getShortName();

        $reflectCollectionImplementation = new ReflectionClass($collectionImplementation);
        $collectionImplementationShortName = $reflectCollectionImplementation->getShortName();

        return $this->twig->render('entity.php.twig', [
            'namespace' => $namespace,
            'repositoryNamespace' => $repositoryNamespace,
            'entity' => $entity,
            'repository' => $repository,
            'collectionInterface' => $collectionInterface,
            'collectionImplementation' => $collectionImplementation,
            'collectionInterfaceShortName' => $collectionInterfaceShortName,
            'collectionImplementationShortName' => $collectionImplementationShortName,
        ]);
    }
}
