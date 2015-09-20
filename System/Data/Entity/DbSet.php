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
     * Gets a new SelectQuery instance.
     * 
     * @method  select
     * @param   string $fields = '*'
     * @return  System.Data.Entity.SelectQuery
     */
    public function select($fields = '*'){
        return new SelectQuery(new SqlQuery($this->dbContext->getDatabase()), $fields, $this->meta->getEntityName());
    }
    
    /**
     * Finds an entity using the specified $params. If the entity is found it is
     * attached to the context.
     * 
     * @method  select
     * @param   mixed $params
     * @param   bool $default = false
     * @return  System.Data.Entity.SelectQuery
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
    }
    
    /**
     * Finds all entities using the specified $params. If the entities are 
     * found they are attached to the context.
     * 
     * @method  select
     * @param   string $fields = '*'
     * @return  System.Data.Entity.SelectQuery
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
    
    public function add($entity){
        if(is_object($entity)){
            $entityContext = new EntityContext($entity);
            $this->entities[$entityContext->getHashCode()] = $entityContext;
            return $entityContext;
        }
    }
    
    public function remove($entity){
        if(is_object($entity)){
            $objHash = spl_object_hash($entity);
            if(array_key_exists($objHash, $this->entities)){
                $this->entities[$objHash]->setState(EntityContext::DELETE);
            }
        }
    }
    
    public function detach($entity){
        if(is_object($entity)){
            $objHash = spl_object_hash($entity);
            unset($this->entities[$objHash]);
        }
    }
    
    public function getEntities(){
        return $this->entities;
    }
    
    public function getMeta(){
        return $this->meta;
    }
}