<?php

namespace App\Service\Weather;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherService
{
    public function __construct(private HttpClientInterface $client, private string $apiKey)
    {
    }

    public function getForecast(string $city, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/forecast', [
            'query' => [
                'q' => $city,
                'units' => 'metric',
                'appid' => $this->apiKey,
            ],
        ]);

        $data = $response->toArray();

        $startTimestamp = $start->getTimestamp();
        $endTimestamp = $end->getTimestamp();

        $filtered = array_filter($data['list'], function ($item) use ($startTimestamp, $endTimestamp) {
            $itemTimestamp = (new \DateTimeImmutable($item['dt_txt'], new \DateTimeZone('UTC')))->getTimestamp();

            return $itemTimestamp >= $startTimestamp && $itemTimestamp <= $endTimestamp;
        });

        // dd($filtered);
        return array_values($filtered);
    }
}
