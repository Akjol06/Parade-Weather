<?php

namespace App\Controller\Api\Weather;

use App\Entity\Event;
use App\Repository\EventRepository;
use App\Service\Weather\WeatherService as WeatherWeatherService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class WeatherController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private WeatherWeatherService $weatherService,
        private EventRepository $eventRepository,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $location = trim($data['location']);
        $start = new \DateTimeImmutable($data['startDate']);
        $end = new \DateTimeImmutable($data['endDate']);
        $user = $this->getUser();

        // Проверяем, есть ли уже прогноз в БД
        $event = $this->eventRepository->findOneBy([
            'user' => $user,
            'location' => $location,
            'startDate' => $start,
            'endDate' => $end,
        ]);

        if ($event) {
            return $this->json([
                'source' => 'db',
                'forecast' => $event->getForecast(),
            ]);
        }

        // Получаем данные из API
        $forecast = $this->weatherService->getForecast($location, $start, $end);

        // dd($forecast);

        $event = new Event();
        $event->setUser($user);
        $event->setLocation($location);
        $event->setStartDate($start);
        $event->setEndDate($end);

        $event->setForecast($forecast);

        $this->em->persist($event);
        $this->em->flush();

        return $this->json([
            'source' => 'api',
            'forecast' => $forecast,
        ]);
    }
}
