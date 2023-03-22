<?php

namespace App\Twig\Components;

use App\Pagination\Paginator;
use App\Repository\FruitRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'fruit_search')]
final class FruitSearchComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $query = '';

    #[LiveProp(writable: true)]
    public string $family = '';

    #[LiveProp(writable: true)]
    public int $page = 1;

    #[LiveProp(writable: false)]
    public bool $favorite = false;

    private $security;

    public function __construct(
        private readonly FruitRepository $fruitRepository,
        Security $security
    ) {
        $this->security = $security;
    }

    /**
     * @return Paginator
     */
    public function getPaginator(): Paginator
    {
        return $this->fruitRepository->findBySearchQuery($this->security->getUser(), $this->query, $this->page, $this->family, $this->favorite);
    }
}
