<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test for the controllers defined inside the UserController used
 * for managing the current logged user.
 */
class UserControllerTest extends WebTestCase
{
    /**
     * @dataProvider getUrlsForAnonymousUsers
     */
    public function testAccessDeniedForAnonymousUsers(string $httpMethod, string $url): void
    {
        $client = static::createClient();
        $client->request($httpMethod, $url);

        $this->assertResponseRedirects(
            'http://localhost/en/login',
            Response::HTTP_FOUND,
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function getUrlsForAnonymousUsers(): \Generator
    {
        yield [ 'GET', '/en/profile/edit' ];
        yield [ 'GET', '/en/profile/change-password' ];
    }

    public function testEditUser(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneByUsername('jane_admin');

        $newUserEmail = 'admin_jane@symfony.com';

        $client->loginUser($user);

        $client->request('GET', '/en/profile/edit');
        $client->submitForm('Save changes', [
            'user[email]' => $newUserEmail,
        ]);

        $this->assertResponseRedirects('/en/profile/edit', Response::HTTP_FOUND);

        /** @var User $user */
        $user = $userRepository->findOneByEmail($newUserEmail);

        $this->assertNotNull($user);
        $this->assertSame($newUserEmail, $user->getEmail());
    }

    public function testChangePassword(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneByUsername('jane_admin');

        $newUserPassword = 'new-password';

        $client->loginUser($user);
        $client->request('GET', '/en/profile/change-password');
        $client->submitForm('Save changes', [
            'change_password[currentPassword]'     => 'kitten',
            'change_password[newPassword][first]'  => $newUserPassword,
            'change_password[newPassword][second]' => $newUserPassword,
        ]);

        $this->assertResponseRedirects();
        $this->assertStringStartsWith(
            '/en/logout',
            $client->getResponse()->headers->get('Location') ?? '',
            'Changing password logout the user.'
        );
    }
}
