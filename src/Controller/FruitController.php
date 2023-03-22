<?php

namespace App\Controller;

use App\Entity\Fruit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/')]
class FruitController extends AbstractController
{

    #[Route('/', name: 'fruit_index', methods: [ 'GET' ])]
    #[Route('/favorite', name: 'fruit_favorite', defaults: [ 'favorite' => true ], methods: [ 'GET' ])]
    #[Cache(smaxage: 10)]
    #[IsGranted('IS_AUTHENTICATED')]
    public function search(
        Request $request,
        bool $favorite = false
    ): Response {
        return $this->render('fruit/index.html.twig', [
            'family'   => (string) $request->query->get('family', ''),
            'query'    => (string) $request->query->get('q', ''),
            'favorite' => $favorite,
        ]);
    }

    #[Route('/{id}/like', name: 'fruit_like', methods: [ 'POST' ])]
    #[IsGranted('IS_AUTHENTICATED')]
    #[IsGranted('edit', subject: 'fruit')]
    public function like(#[CurrentUser] User $user, Request $request, Fruit $fruit, EntityManagerInterface $entityManager): Response
    {
        /** @var string|null $token */
        $token = $request->request->get('token');
        $backTo = (string) $request->request->get('backTo');

        if (!$this->isCsrfTokenValid('like', $token)) {
            return $this->redirectToRoute($backTo);
        }

        $fruit->addLike($user);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully liked');

        return $this->redirectToRoute($backTo);
    }

    #[Route('/{id}/unlike', name: 'fruit_unlike', methods: [ 'POST' ])]
    #[IsGranted('IS_AUTHENTICATED')]
    #[IsGranted('edit', subject: 'fruit')]
    public function unlike(#[CurrentUser] User $user, Request $request, Fruit $fruit, EntityManagerInterface $entityManager): Response
    {
        /** @var string|null $token */
        $token = $request->request->get('token');
        $backTo = (string) $request->request->get('backTo');

        if (!$this->isCsrfTokenValid('unlike', $token)) {
            return $this->redirectToRoute($backTo);
        }

        $fruit->removeLike($user);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully removed');

        return $this->redirectToRoute($backTo);
    }
}
