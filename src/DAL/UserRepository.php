<?php

namespace Commentsystem\DAL;

use Commentsystem\Container;
use PDO;

class UserRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Container::getInstance()->getDatabase();
    }

    public function find($id): UserDAO
    {
        $stmt = $this->connection->prepare('
            SELECT "UserDAO", user.* 
             FROM user
             WHERE id = :id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Set the fetchmode to populate an instance of 'UserDAO'
        $stmt->setFetchMode(PDO::FETCH_CLASS, UserDAO::class);
        return $stmt->fetch();
    }

    public function save(UserDAO $user): bool
    {
        // If the ID is set, we're updating an existing record
        if (isset($user->id)) {
            return $this->update($user);
        }

        $stmt = $this->connection->prepare('
            INSERT INTO users 
                (username, email) 
            VALUES 
                (:username, :email)
        ');
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':email', $user->email);
        return $stmt->execute();
    }

    public function update(UserDAO $user): bool
    {
        if (!isset($user->id)) {
            // We can't update a record unless it exists...
            throw new \LogicException(
                'Cannot update user that does not yet exist in the database.'
            );
        }
        $stmt = $this->connection->prepare('
            UPDATE users
            SET username = :username,
                email = :email
            WHERE id = :id
        ');
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':id', $user->id);
        return $stmt->execute();
    }
}