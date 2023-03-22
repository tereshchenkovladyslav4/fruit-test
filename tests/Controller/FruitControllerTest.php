<?php

namespace App\Tests\Controller;

use App\Pagination\Paginator;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional test for the controllers defined inside FruitController.
 */
class FruitControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneByUsername('jane_admin');

        $client->loginUser($user);

        $client->followRedirects();

        $crawler = $client->request('GET', '/en/');

        $this->assertResponseIsSuccessful();

        $this->assertCount(
            Paginator::PAGE_SIZE,
            $crawler->filter('tr.fruit'),
            'The homepage displays the right number of fruits.'
        );
    }

    public function testAjaxSearch(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneByUsername('jane_admin');

        $client->loginUser($user);

        $client->followRedirects();

        $crawler = $client->request('GET', '/en/', [ 'q' => 'Apple Test' ]);

        $this->assertResponseIsSuccessful();
        $this->assertCount(10, $crawler->filter('tr.fruit'));
    }
}
