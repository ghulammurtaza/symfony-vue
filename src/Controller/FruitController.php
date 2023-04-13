<?php

namespace App\Controller;

use App\Service\FruitService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FruitController extends AbstractController
{
    private FruitService $fruitService;

    public function __construct(FruitService $fruitService)
    {
        $this->fruitService = $fruitService;
    }

    /**
     * @Route("/", name="fruit_index")
     */
    public function index(): Response
    {
        return $this->render('fruit/index.html.twig');
    }

    /**
     * @Route("/fruit/list", name="fruit_list")
     */
    public function list(Request $request): Response
    {
        return $this->fruitService->getPaginatedItems($request);
    }

    /**
     * @Route("/add-favorite/{fruitId}", name="add_favorite", methods="POST")
     */
    public function addFavourite(Request $request, $fruitId): JsonResponse
    {
        return $this->fruitService->addFavorite($request, $fruitId);
    }

    /**
     * @Route("/favourites", name="favourites")
     */
    public function favorites(Request $request): Response
    {
        return $this->render('fruit/favourites.html.twig', [
            'favourites' => $this->fruitService->getFavourites($request)
        ]);
    }

}
