<?php


namespace App\Services;


use App\Entities\Address;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

class AddressService
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var Client
     */
    private Client $guzzle;

    /**
     * AddressService constructor.
     * @param EntityManager $entityManager
     * @param Client $guzzle
     */
    public function __construct(EntityManager $entityManager, Client $guzzle)
    {
        $this->entityManager = $entityManager;
        $this->guzzle = $guzzle;
    }

    /**
     * @param string $address
     * @return Address|object|null
     * @throws GuzzleException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getAddress(string $address)
    {
        $addressEntity = $this->entityManager->getRepository(Address::class)->findOneBy(['address' => $address]);

        if (!$addressEntity) {
            $addressEntity = $this->createAddress($address);
        }

        return $addressEntity;
    }

    /**
     * @param string $address
     * @return Address
     * @throws ORMException|OptimisticLockException|GuzzleException
     */
    private function createAddress(string $address)
    {
        $addressEntity = new Address();
        $addressEntity->setAddress($address);
        $this->setGeometry($addressEntity);

        $this->entityManager->persist($addressEntity);
        $this->entityManager->flush();

        return $addressEntity;
    }

    /**
     * @param Address $address
     * @throws GuzzleException
     * @throws Exception
     */
    private function setGeometry(Address $address): void
    {
        $params = [
            'address' => $this->prepare($address->getAddress()),
            'key' => Config::get('google.api_key')
        ];

        $response = $this->guzzle->get(Config::get('google.geoapi_url'), ['query' => $params]);

        $body = json_decode($response->getBody()->getContents(), 1);

        if (!$body || !is_array($body) || !array_key_exists('results', $body)) {
            throw new Exception(sprintf('Google Geo API error for address "%s".', $address));
        }

        $geo = array_shift($body['results']);

        $address->setLat(Arr::get($geo, 'geometry.location.lat', 0.0));
        $address->setLng(Arr::get($geo, 'geometry.location.lng', 0.0));
    }

    /**
     * @param string $address
     * @return string
     */
    private function prepare(string $address): string
    {
        return preg_replace('/(\w+)\s/', '$1+', $address);
    }
}
