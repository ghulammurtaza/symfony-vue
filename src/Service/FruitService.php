<?php

namespace App\Service;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @property mixed $fromEmail
 * @property mixed $toEmail
 */
class FruitService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private HttpClientInterface $httpClient;
    private FruitRepository $fruitRepository;
    private MailerInterface $mailer;
    private Environment $twig;
    public function __construct(
        EntityManagerInterface $entityManager,
        FruitRepository $fruitRepository,
        HttpClientInterface $httpClient,
        ValidatorInterface $validator,
        MailerInterface $mailer,
        Environment $twig,
        $fromEmail,
        $toEmail
    )
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->validator = $validator;
        $this->fruitRepository = $fruitRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->fromEmail = $fromEmail;
        $this->toEmail = $toEmail;
    }

    /**
     * @param $request
     * @return JsonResponse
     */
    public function getPaginatedItems($request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);
        $name = $request->query->get('name', '');
        $family = $request->query->get('family', '');

        $paginator = $this->fruitRepository->getPaginatedFruits($page, $limit, [
            'name' => $name,
            'family' => $family
        ]);

        $fruits = [];
        foreach ($paginator as $fruit) {
            $fruits[] = $fruit;
        }

        $totalFruits = count($paginator);

        return new JsonResponse([
            'fruits' => $fruits,
            'page' => $page,
            'limit' => $limit,
            'totalFruits' => $totalFruits,
            'totalPages' => ceil($totalFruits / $limit)
        ]);
    }

    /**
     * @param $request
     * @param $fruitId
     * @return JsonResponse
     */
    public function addFavorite($request, $fruitId): JsonResponse
    {
        $session = $request->getSession();

        $favourites = $session->get('favourites', []);

        if (!in_array($fruitId, $favourites)) {
            $fruit = $this->fruitRepository->find($fruitId);

            if (!$fruit) {
                return new JsonResponse(['success' => false, 'message' => 'Fruit not found'], 404);
            }
            $favourites[] = $fruitId;
            $session->set('favourites', $favourites);

            if (count($favourites) > 10) {
                array_shift($favourites);
                $session->set('favourites', $favourites);
            }

            return new JsonResponse(['success' => true, 'message' => 'Fruit added to favourites'], 200);
        }

        return new JsonResponse(['success' => false, 'message' => 'Fruit already in favourites'], 400);
    }

    /**
     * @param $request
     * @return array
     */
    public function getFavourites($request): array
    {
        $session = $request->getSession();

        $favourites = $session->get('favourites', []);
        if(empty($favourites)) return [];

        return $this->fruitRepository->getFavouriteFruits($favourites);
    }

    /**
     * Fetches data from an API and saves it to the database.
     *
     * @return bool true if the data was fetched and saved successfully, false otherwise.
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function fetchAndSaveData(): bool
    {
        try {
            $response = $this->httpClient->request('GET', 'https://fruityvice.com/api/fruit/all');

            if ($response->getStatusCode() !== 200) {
                return false;
            }
            $data = json_decode($response->getContent(), true);

            foreach ($data as $item) {
                $fruit = $this->fruitRepository->findOneBy(['name' => $item['name']]);
                if (!$fruit) {
                    $fruit = new Fruit();
                }
                // Validate API data
                $errors = $this->validator->validate($item, [
                    //validation needed to added
                ]);

                if (count($errors) > 0) {
                    // Handle validation errors
                    continue;
                }

                // Map API data to entity properties
                $fruit->setName($item['name']);
                $fruit->setGenus($item['genus']);
                $fruit->setFamily($item['family']);
                $fruit->setFruitOrder($item['order']);
                $fruit->setNutritions($item['nutritions']);

                // Persist entity to database
                $this->entityManager->persist($fruit);
            }
            // Flush changes to database
            $this->entityManager->flush();
            $this->sendEmail();
            return true;
        } catch (TransportExceptionInterface $e) {
            // Handle API request error
            return false;
        }
    }

    /**
     * @return null
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendEmail()
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($this->toEmail)
            ->subject('Fruit Status Update!')
            ->html($this->twig->render('emails/fruits_fetch.html.twig', [
                'name' => 'Admin'
            ]));

        return $this->mailer->send($email);
    }
}