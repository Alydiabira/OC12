<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class MeteoService
{
    private string $apiKey;

    public function __construct(
        private HttpClientInterface $http,
        private CacheInterface $cache,
        string $openWeatherApiKey
    ) {
        $this->apiKey = $openWeatherApiKey;
    }

    public function getMeteo(string $ville): array
    {
        $cacheKey = 'meteo_' . strtolower($ville);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($ville) {
            $item->expiresAfter(3600); // 1h de cache

            $response = $this->http->request(
                'GET',
                'https://api.openweathermap.org/data/2.5/weather',
                [
                    'query' => [
                        'q' => $ville,
                        'appid' => $this->apiKey,
                        'units' => 'metric',
                        'lang' => 'fr'
                    ]
                ]
            );

            if ($response->getStatusCode() !== 200) {
                throw new \Exception("Ville introuvable");
            }

            $data = $response->toArray();

            return [
                'ville' => $data['name'],
                'temperature' => $data['main']['temp'],
                'description' => $data['weather'][0]['description'],
                'humidite' => $data['main']['humidity'],
                'vent' => $data['wind']['speed']
            ];
        });
    }
}
