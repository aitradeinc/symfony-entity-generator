<?php

namespace EntityGenerator\Generator;

use EntityGenerator\Driver\DriverInterface;
use EntityGenerator\Mapper\MapperInterface;
use Symfony\Component\Yaml\Yaml;

class Generator
{
    /** @var DriverInterface */
    private $driver;

    /** @var MapperInterface */
    private $mapper;

    private array $entityGroups;

    public function __construct(DriverInterface $driver, MapperInterface $mapper)
    {
        $this->driver = $driver;
        $this->mapper = $mapper;

        $doctrineEntityGenerator = __DIR__ . '/../../../../../config/packages/doctrine_entity_generator.yaml';
        $doctrineEntityGeneratorGroups = Yaml::parseFile($doctrineEntityGenerator);
        $this->entityGroups = $doctrineEntityGeneratorGroups['doctrine_entity_generator']['tables'];
    }

    public function generateEntities(): array
    {
        $tables = $this->driver->getTables();
        /** @var array<string, Entity> $entities */
        $entities = [];

        foreach ($tables as $table) {
            $entities[$table] = $this->mapper->map($this->driver->getCreateStatement($table));
        }

        /**
         * @var string $table
         * @var Entity $entity
         */
        foreach ($entities as $table => $entity) {
            $this->setGroup($table, $entity);
            foreach ($entity->getReferences() as $reference) {
                if ($reference->isOwningSide()) {
                    /** @var Entity $referencedEntity */
                    $referencedEntity = $entities[$reference->getTable()];
                    $referencedEntity->addReference($reference->invert($table));
                }
            }
        }

        return array_values($entities);
    }

    private function setGroup(string $table, Entity $entity)
    {
        if(key_exists($table, $this->entityGroups)) {
            $fields = $this->entityGroups[$table]['fields'];
            foreach ($fields as $column => $value) {
                if(in_array($column, $entity->getColumns())) {
                    $entity->getColumn($column)->setGroup($value);
                }

                foreach ($entity->getReferences() as $reference) {
                    if($reference->getTable() == $column) {
                        $reference->setGroup($value);
                    }
                }
            }
        }
    }
}
