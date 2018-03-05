<?php

require_once 'ConnectionFactory.php';
class AbstractDAO
{
    protected static function fetchQuery($query, $bindParams = array(), $fetchParams = PDO::FETCH_ASSOC, $classname = "")
    {
        try {
            $connection = ConnectionFactory::getFactory()->getConnection();
            $stmt = $connection->prepare($query);
            $stmt->execute($bindParams);
            if ($classname) {
                $results = $stmt->fetchAll($fetchParams, $classname);
            } else {
                $results = $stmt->fetchAll($fetchParams);
            }
        } catch (PDOException $pde) {
            throw $pde;
        }
        return $results;

    }

    protected static function updateQuery($query, $bindParams = array())
    {
        try {
            $connection = ConnectionFactory::getFactory()->getConnection();
            $stmt = $connection->prepare($query);
            return $stmt->execute($bindParams);

        } catch (PDOException $pde) {
            throw $pde;
        }

    }

    protected static function insertQuery($query, $bindParams = array())
    {
        try {


            $connection = ConnectionFactory::getFactory()->getConnection();
            $stmt = $connection->prepare($query);
            $stmt->execute($bindParams);
            $lastInsertId = $connection->lastInsertId();


        } catch (PDOException $pde) {
            throw $pde;
        }
        return $lastInsertId;

    }

    protected static function deleteQuery($query, $bindParams = array())
    {
        try {
            $connection = ConnectionFactory::getFactory()->getConnection();
            $stmt = $connection->prepare($query);
            $stmt->execute($bindParams);

        } catch (PDOException $pde) {
            throw $pde;
        }

    }
}
