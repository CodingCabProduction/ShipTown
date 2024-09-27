<?php

namespace Database\Factories\Modules\Api2cart\src\Models;

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Database\Eloquent\Factories\Factory;

class Api2cartOrderImportsFactory extends Factory
{
    protected $model = Api2cartOrderImports::class;

    public function definition(): array
    {
        // this is a real example of the response from the API, copied raw
        return [
            'raw_import' => json_decode('
{
  "id": "1000365271",
  "total": {
    "total": 37.94,
    "total_tax": 0,
    "total_paid": 37.94,
    "total_discount": 0,
    "shipping_ex_tax": 7.95,
    "subtotal_ex_tax": 29.99,
    "wrapping_ex_tax": null,
    "additional_fields": {
      "shipping_tax": 0,
      "subtotal_tax": 0,
      "tax_discount": 0,
      "wrapping_tax": null,
      "shipping_discount_ex_tax": 0,
      "subtotal_discount_ex_tax": 0
    }
  },
  "status": {
    "id": "processing",
    "name": "Processing",
    "history": [
      {
        "id": "processing",
        "name": "Processing",
        "notify": false,
        "comment": "Captured amount of €37.94 online. Transaction ID: \"66f68f2e-00ca-ad19-8e8d-321321321321\"",
        "modified_time": {
          "value": "2024-09-27T11:57:56+0100",
          "format": "Y-m-d\\\\TH:i:sO"
        }
      }
    ],
    "refund_info": null
  },
  "totals": {
    "tax": 0,
    "total": 37.94,
    "discount": 0,
    "shipping": 7.95,
    "subtotal": 29.99,
    "additional_fields": {
      "hidden_tax": 0
    }
  },
  "bundles": [],
  "comment": null,
  "refunds": [],
  "currency": {
    "id": "EUR",
    "iso3": "EUR",
    "name": "Euro",
    "rate": 1,
    "avail": true,
    "default": true,
    "symbol_left": "€",
    "symbol_right": ""
  },
  "customer": {
    "id": "",
    "email": "demo-admin@ship.town",
    "phone": "0851234567",
    "last_name": "Artur",
    "first_name": "Hanusek"
  },
  "order_id": "374180",
  "store_id": "1",
  "basket_id": "914102",
  "create_at": {
    "value": "2024-09-27T11:57:55+0100",
    "format": "Y-m-d\\\\TH:i:sO"
  },
  "discounts": [],
  "channel_id": null,
  "modified_at": {
    "value": "2024-09-27T11:57:56+0100",
    "format": "Y-m-d\\\\TH:i:sO"
  },
  "gift_message": null,
  "finished_time": null,
  "order_products": [
    {
      "name": "6ft Artificial Christmas Tree Green with Stand",
      "model": "566193",
      "price": 29.99,
      "weight": null,
      "barcode": null,
      "options": [],
      "quantity": 1,
      "tax_value": 0,
      "product_id": "80174",
      "variant_id": null,
      "tax_percent": 0,
      "total_price": 29.99,
      "weight_unit": "lb",
      "price_inc_tax": 29.99,
      "discount_amount": 0,
      "order_product_id": "1616675",
      "parent_order_product_id": null,
      "tax_value_after_discount": 0
    }
  ],
  "payment_method": {
    "name": "revolut_form",
    "additional_fields": {
      "additional_payment_info": {
        "publicId": "b8b02fca-564a-432e-ab46-8b811ec88290",
        "method_title": "Pay with Debit or Credit Card",
        "revolutOrderId": "66f6828e-00c0-ad19-8e81-b5bft6b9c30b",
        "revolutOrderData": "{\"id\":\"66f6828e-00c0-ad19-8e81-32313123131\",\"public_id\":\"b8b02fca-564a-432e-ab46-33213131213\",\"type\":\"PAYMENT\",\"state\":\"AUTHORISED\",\"created_at\":\"2024-09-27T10:57:18.714564Z\",\"updated_at\":\"2024-09-27T10:57:53.731695Z\",\"capture_mode\":\"MANUAL\",\"customer_id\":\"cfca7c19-558c-4232-b4a9-32131221321\",\"email\":\"demo-admin@ship.town\",\"phone\":\"+353851234567\",\"order_amount\":{\"value\":3794,\"currency\":\"EUR\"},\"order_outstanding_amount\":{\"value\":3794,\"currency\":\"EUR\"},\"refunded_amount\":{\"value\":0,\"currency\":\"EUR\"},\"metadata\":[],\"checkout_url\":\"https:\\\\/\\\\/checkout.revolut.com\\\\/payment-link\\\\/b8b06fca-561a-4321-ab46-3213213132131\",\"cancel_authorised_after\":\"PT2M\",\"payments\":[{\"id\":\"66f68fb0-1612-a842-b4ae-d5111111f\",\"state\":\"AUTHORISED\",\"created_at\":\"2024-09-27T10:57:52.221557Z\",\"updated_at\":\"2024-09-27T10:57:53.701741Z\",\"token\":\"45f10e51-85ed-49b7-968a-32312312313\",\"amount\":{\"value\":3794,\"currency\":\"EUR\"},\"authorised_amount\":{\"value\":3794,\"currency\":\"EUR\"},\"settled_amount\":{\"value\":3794,\"currency\":\"EUR\"},\"payment_method\":{\"id\":\"66f68fb0-ef8b-af9a-8c23-321321312131\",\"type\":\"CARD\",\"card\":{\"card_brand\":\"VISA\",\"card_bin\":\"431940\",\"funding\":\"DEBIT\",\"card_country\":\"IE\",\"card_last_four\":\"4217\",\"card_expiry\":\"11\\\\/2027\",\"cardholder_name\":\"Demo Admin\",\"checks\":{\"three_ds\":{\"state\":\"VERIFIED\",\"version\":2},\"cvv_verification\":\"MATCH\",\"address\":\"N_A\",\"postcode\":\"N_A\",\"cardholder\":\"N_A\",\"raw_cavv_result\":\"2\",\"raw_cvv_result\":\"M\",\"raw_avs_result\":\"U\"}}},\"billing_address\":{\"street_line_1\":\"34 blue set Grove\",\"region\":\"CK\",\"city\":\"Cork\",\"country_code\":\"IE\",\"postcode\":\"T1123C1\"},\"risk_level\":\"LOW\",\"fees\":[]}]}",
        "IsTransactionPending": null
      }
    }
  },
  "warehouses_ids": [],
  "billing_address": {
    "id": "744319",
    "fax": "",
    "city": "Cork",
    "type": "billing",
    "phone": "0851234567",
    "state": {
      "code": "CK",
      "name": "Co. Cork"
    },
    "gender": null,
    "region": "Co. Cork",
    "tax_id": null,
    "company": "",
    "country": {
      "name": "Ireland",
      "code2": "IE",
      "code3": "IRL"
    },
    "default": false,
    "website": null,
    "address1": "34 blue  Grove",
    "address2": "Springhill",
    "postcode": "T23C1C2",
    "last_name": "Artur",
    "first_name": "Hanusek",
    "phone_mobile": "",
    "additional_fields": {
      "prefix": "",
      "suffix": "",
      "middlename": ""
    },
    "identification_number": null
  },
  "shipping_method": {
    "name": "Order before 12pm Noon Monday to Friday (excl. Bank Holiday) for Next Working Day in Ireland - Next Working Day",
    "additional_fields": {
      "code": "flatrate_flatrate",
      "provider_code": "flatrate"
    }
  },
  "shipping_address": {
    "id": "744318",
    "fax": "",
    "city": "Cork",
    "type": "shipping",
    "phone": "085123456789",
    "state": {
      "code": "CK",
      "name": "Co. Cork"
    },
    "gender": null,
    "region": "Co. Cork",
    "tax_id": null,
    "company": "",
    "country": {
      "name": "Ireland",
      "code2": "IE",
      "code3": "IRL"
    },
    "default": false,
    "website": null,
    "address1": "34 Blue Grove",
    "address2": "Springhill",
    "postcode": "T11C1C1",
    "last_name": "Artur",
    "first_name": "Hanusek",
    "phone_mobile": "",
    "additional_fields": {
      "prefix": "",
      "suffix": "",
      "middlename": ""
    },
    "identification_number": null
  },
  "shipping_methods": [
    {
      "name": "Order before 12pm Noon Monday to Friday (excl. Bank Holiday) for Next Working Day in Ireland - Next Working Day",
      "additional_fields": {
        "code": "flatrate_flatrate",
        "provider_code": "flatrate"
      }
    }
  ],
  "additional_fields": {
    "base_currency_code": "EUR"
  },
  "order_details_url": null
}
        ', true),
        ];
    }
}
