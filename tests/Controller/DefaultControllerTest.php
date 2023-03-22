<?php

namespace App\Tests\Controller;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Functional test that implements a "smoke test" of all the public and secure
 * URLs of the application.
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * PHPUnit's data providers allow to execute the same tests repeated times
     * using a different set of data each time.
     * See https://symfony.com/doc/current/testing.html#testing-against-different-sets-of-data.
     *
     * @dataProvider getPublicUrls
     */
    public function testPublicUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful(sprintf('The %s public URL loads correctly.', $url));
    }

    /**
     * The application contains a lot of secure URLs which shouldn't be
     * publicly accessible. This tests ensures that whenever a user tries to
     * access one of those pages, a redirection to the login form is performed.
     *
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects(
            'http://localhost/en/login',
            Response::HTTP_FOUND,
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function getPublicUrls(): \Generator
    {
        yield [ '/en/login' ];
    }

    public function getSecureUrls(): \Generator
    {
        yield [ '/en/' ];
    }
}
