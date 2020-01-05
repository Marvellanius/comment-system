<?php


namespace Commentsystem\Services;


use Commentsystem\DAL\UserRepository;
use Commentsystem\Factories\UserFactory;
use Commentsystem\Models\User;

class UserService
{
    private UserRepository $repository;
    private UserFactory $factory;

    public function __construct()
    {
        $this->repository = new UserRepository();
        $this->factory = new UserFactory();
    }
    public function findById(string $id): User
    {
        return $this->factory->createFromDAO($this->repository->find($id));
    }
}