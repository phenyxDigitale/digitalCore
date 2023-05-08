<?php

/**
 * Class Core_Foundation_Database_EntityManager
 */
// @codingStandardsIgnoreStart
class Core_Foundation_Database_EntityManager {

    // @codingStandardIgnoreEnd

    protected $db;
    protected $configuration;

    protected $entityMetaData = [];

    /**
     * Core_Foundation_Database_EntityManager constructor.
     *
     * @param Core_Foundation_Database_DatabaseInterface $db
     * @param Core_Business_ConfigurationInterface       $configuration
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct(
        Core_Foundation_Database_DatabaseInterface $db,
        Core_Business_ConfigurationInterface $configuration
    ) {

        $this->db = $db;
        $this->configuration = $configuration;
    }

    /**
     * Return current database object used
     *
     * @return Core_Foundation_Database_DatabaseInterface
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getDatabase() {

        return $this->db;
    }

    /**
     * Return current repository used
     *
     * @param string $className
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.0.0 Intial version
     */
    public function getRepository($className) {

        if (is_callable([$className, 'getRepositoryClassName'])) {
            $repositoryClass = call_user_func([$className, 'getRepositoryClassName']);
        } else {
            $repositoryClass = null;
        }

        if (!$repositoryClass) {
            $repositoryClass = 'Core_Foundation_Database_EntityRepository';
        }

        $repository = new $repositoryClass(
            $this,
            $this->configuration->get('_DB_PREFIX_'),
            $this->getEntityMetaData($className)
        );

        return $repository;
    }

    /**
     * Return entity's meta data
     *
     * @param string $className
     *
     * @return mixed
     * @throws Adapter_Exception
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getEntityMetaData($className) {

        if (!array_key_exists($className, $this->entityMetaData)) {
            $metaDataRetriever = new Adapter_EntityMetaDataRetriever();
            $this->entityMetaData[$className] = $metaDataRetriever->getEntityMetaData($className);
        }

        return $this->entityMetaData[$className];
    }

    /**
     * Flush entity to DB
     *
     * @param Core_Foundation_Database_EntityInterface $entity
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function save(Core_Foundation_Database_EntityInterface $entity) {

        $entity->save();

        return $this;
    }

    /**
     * DElete entity from DB
     *
     * @param Core_Foundation_Database_EntityInterface $entity
     *
     * @return $this
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function delete(Core_Foundation_Database_EntityInterface $entity) {

        $entity->delete();

        return $this;
    }

}
