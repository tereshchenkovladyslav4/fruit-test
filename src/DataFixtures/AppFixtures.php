<?php

namespace App\DataFixtures;

use App\Entity\Fruit;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadFruits($manager);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$fullName, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullName);
            $user->setUsername($username);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function loadFruits(ObjectManager $manager): void
    {
        foreach ($this->getFruitsData() as [$id, $name, $family, $genus, $plant_order, $calories, $carbohydrates, $fat, $protein, $sugar]) {
            $fruit = new Fruit();

            $fruit->setId($id);
            $fruit->setName($name);
            $fruit->setFamily($family);
            $fruit->setGenus($genus);
            $fruit->setPlantOrder($plant_order);
            $fruit->setCalories($calories);
            $fruit->setCarbohydrates($carbohydrates);
            $fruit->setFat($fat);
            $fruit->setProtein($protein);
            $fruit->setSugar($sugar);

            $manager->persist($fruit);
        }

        $manager->flush();
    }

    /**
     * @return array<array{string, string, string, string, array<string>}>
     */
    private function getUserData(): array
    {
        return [
            [ 'Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', [ 'ROLE_ADMIN' ] ],
            [ 'Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', [ 'ROLE_ADMIN' ] ],
            [ 'John Doe', 'john_user', 'kitten', 'john_user@symfony.com', [ 'ROLE_USER' ] ],
        ];
    }

    /**
     * @return array<array{int, string, string, string, string, int,  int, int, int, int}>
     */
    private function getFruitsData(): array
    {
        $result = [];
        for ($index = 1; $index < 100; $index++) {
            $result[] = [
                $index,
                'Apple ' . $index,
                'Rosaceae ' . $index,
                'Malus ' . $index,
                'Rosales ' . $index,
                5200,
                1140,
                40,
                30,
                1030
            ];
        }

        return $result;
    }
}
