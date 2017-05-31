<?php

interface IPersistentManager
{

    public function retrieveObject(PersistentObject $object);

    public function retrieveObjectFromQuery(PersistentObject $object, Database\MQuery $query);

    public function retrieveObjectFromCriteria(PersistentObject $object, PersistentCriteria $criteria, $parameters);

    public function retrieveAssociation(PersistentObject $object, $associationName);

    public function retrieveAssociationAsCursor(PersistentObject $object, $target);

    public function getClassMap($className, $mapClassName);

    public function getConnection($dbName);

    public function getDeleteCriteria(PersistentObject $object);

    public function getRetrieveCriteria(PersistentObject $object, $command);

    public function getUpdateCriteria(PersistentObject $object);

    public function getValue($object, $attribute);

    public function saveObjectRaw(PersistentObject $object);

    public function saveObject(PersistentObject $object);

    public function saveAssociation(PersistentObject $object, $associationName);

    public function saveAssociationById(PersistentObject $object, $associationName, $id);

    public function deleteObject(PersistentObject $object);

    public function deleteAssociation(PersistentObject $object, $associationName);

    public function deleteAssociationObject(PersistentObject $object, $associationName, PersistentObject $refObject);

    public function deleteAssociationById(PersistentObject $object, $associationName, $id);
}
