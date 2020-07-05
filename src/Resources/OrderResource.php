<?php
namespace Seshac\Shiprocket\Resources;

use Seshac\Shiprocket\Clients\Client;

class OrderResource
{
    protected $client;

    protected $token;
  
    public function __construct(Client $client, string $token)
    {
        $this->client = $client;
        $this->token = $token;
    }
    
    /**
     * Send order using post request
     *
     * @param array $order
     * @param bool $channelSpecificOrder
     * @return mixed
     */
    public function create(array $order, bool $channelSpecificOrder = false)
    {
        if ($channelSpecificOrder) {
            $endpoint = 'external/orders/create';
        } else {
            $endpoint = 'external/orders/create/adhoc';
        }

        return $this->client->setEndpoint($endpoint)
                    ->setHeaders($this->token)
                    ->post($order);
    }

    /**
     * TODO : ISSUE WITH CANCEL API
     *
     * @param array $ids
     * @return void
     */
    public function cancel(array $ids)
    {
        $endpoint = 'external/orders/cancel';
        return $this->client->setEndpoint($endpoint)
                ->setHeaders($this->token)
                ->post($ids);
    }


    public function updatePickupLocation(array $data)
    {
        $endpoint = 'external/orders/address/pickup';

        return $this->client->setEndpoint($endpoint)
                    ->setHeaders($this->token)
                    ->patch($data);
    }
}