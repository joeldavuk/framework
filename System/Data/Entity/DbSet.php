<?php

namespace System\Data\Entity;

class DbSet {
    
    protected $dbContext;
    protected $meta = array();
    protected $entities = array();
    
    /**
     * Initializes an instance of DbSet.
     * 
     * @method  __construct
     * @param   System.Data.Entity.DbContext $dbContext
     * @param   System.Data.Entity.EntityMeta $meta
     */
    public function __construct(DbContext $dbContext, EntityMeta $meta){
        $this->dbContext = $dbContext;
        $this->meta = $meta;
    }
    
    /**
     * Gets a new SelectQuery instance that has been initilazied to select 
     * from the database table represented by the entity type for this DbSet.
     * 
     * @method  select
     * @param   string $fields = '*'
     * @return  System.Data.Entity.SelectQuery
     */
    public function select($fields = '*'){
        return new SelectQuery(new SqlQuery($this->dbContext->getDatabase(), $this->dbContext->getMetaReader()), $fields, $this->meta->getEntityName());
    }
    
    /**
     * Finds an entity using the specified $params. If the entity is found, it is
     * attached to the context. Returns false if the entity is not found. 
     * The optional $default argument determines if a default entity should be 
     * returned with empty property values.
     * 
     * @method  find
     * @param   mixed $params
     * @param   bool $default = false
     * @return  mixed
     */
    public function find($params, $default = false){

        if(is_scalar($params)){
            $params = array($this->meta->getKey()->getKeyName() => $params); 
        }
        
        if(is_array($params)){
            $select = $this->select('*', $this->meta->getEntityName());
            
            foreach($params as $key=>$param){
                $select->where($key.'=:'.$key);
            }

            $entity = $select->single($params, $this->meta->getEntityName(), $default);
            
            if($entity){
                $entityContext = $this->add($entity);
                $entityContext->setState(EntityContext::PERSISTED);
                $this->dbContext->getPersistedEntities()->add($entityContext->getHashCode(), $entityContext);

                return $entity;
            }
        }
        return false;
    }
    
    /**
     * Finds all entities using the specified $params. If the entities are 
     * found, they are attached to the context.
     * 
     * @method  findAll
     * @param   mixed $params
     * @return  System.Data.Entity.DbListResult
     */
    public function findAll($params = array()){

        if(is_scalar($params)){
            $params = array($this->meta->getKey()->getKeyName() => $params);
        }

        if(is_array($params)){
            $select = $this->select('*', $this->meta->getEntityName());
            
            foreach($params as $key=>$param){
                $select->where($key.'=:'.$key);
            }

            $entityCollection = $select->toList($params, $this->meta->getEntityName());
            
            foreach($entityCollection as $entity){
                $entityContext = $this->add($entity);
                $entityContext->setState(EntityContext::PERSISTED);
                $this->dbContext->getPersistedEntities()->add($entityContext->getHashCode(), $entityContext);
            }
            
            return $entityCollection;
        }
    }
    
    /**
     * Adds an entity to the DbSet collection by creating a new EntityContext
     * object. The EntityContext object is then returned.
     * 
     * @method  add
     * @param   mixed $entity
     * @return  System.Data.Entity.EntityContext
     */
    public function add($entity){
        if(is_object($entity)){
            $entityContext = new EntityContext($entity);
            $this->entities[$entityContext->getHashCode()] = $entityContext;
            return $entityContext;
        }
    }
    
    /**
     * Changes the state of an entity such that when saveChanges() is called, the
     * entity will be deleted from the data store.
     * 
     * @method  remove
     * @param   mixed $entity
     * @return  void
     */
    public function remove($entity){
        if(is_object($entity)){
            $objHash = spl_object_hash($entity);
            if(array_key_exists($objHash, $this->entities)){
                $this->entities[$objHash]->setState(EntityContext::DELETE);
            }
        }
    }
    
    /**
     * Removes an entity from the DbSet collection.
     * 
     * @method  detach
     * @param   mixed $entity
     * @return  bool
     */
    public function detach($entity){
        if(is_object($entity)){
            $objHash = spl_object_hash($entity);
            unset($this->entities[$objHash]);
            return true;
        }
        return false;
    }
    
    /**
     * Gets the underlying array that holds the entities.
     * 
     * @method  getEntities
     * @return  array
     */
    public function getEntities(){
        return $this->entities;
    }
    
    /**
     * Gets the EntityMeta object for the DbSet.
     * 
     * @method  getMeta
     * @return  System.Data.Entity.EntityMeta
     */
    public function getMeta(){
        return $this->meta;
    }
}