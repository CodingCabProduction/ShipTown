<?php


namespace App\Modules\ScurriAnpost\src;

use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\ShippingLabel;
use App\Modules\ScurriAnpost\src\Api\Client;
use Exception;

class Scurri
{
    /**
     * @param Order $order
     * @return ShippingLabel
     * @throws Exception
     */
    public static function createShippingLabel(Order $order): ShippingLabel
    {
        $consignment_id = Client::createSingleConsignment([
            "order_number" => $order->order_number,
            "recipient" => [
                "address" => [
                    "country" => $order->shippingAddress->country_code,
                    "postcode" => $order->shippingAddress->postcode,
                    "city" => $order->shippingAddress->city,
                    "address1" => $order->shippingAddress->address1,
                    "address2" => $order->shippingAddress->address2,
                    "state" => $order->shippingAddress->state_code
                ],
                "contact_number" => $order->shippingAddress->phone,
                "email_address" => "",
                "company_name" => $order->shippingAddress->company,
                "name" => $order->shippingAddress->full_name,
            ],
            "packages" => [
                [
                    "items" => [
                        [
                            "sku" => "n/a",
                            "quantity" => 1,
                            "name" => "Shipment",
                            "value" => 1,
                        ]
                    ],
                    "length" => 50,
                    "height" => 50,
                    "width" => 50,
                    "reference" => ""
                ],
            ],
        ]);

        // in order to obtain shipping number we need to generate documents
        $documents = Client::getDocuments($consignment_id);

        // we need to refresh it in order to obtain shipping number
        $consignment = Client::getSingleConsignment($consignment_id)->json();

        $orderShipment = new ShippingLabel([
            'order_id' => $order->getKey(),
            'carrier' => $consignment['carrier'],
            'service' => $consignment['service'],
            'shipping_number' => $consignment['consignment_number'],
            'tracking_url' => $consignment['tracking_url'],
            'base64_pdf_labels' => base64_encode($documents->getLabels()),
        ]);

        $orderShipment->save();

        return $orderShipment;
    }
}
