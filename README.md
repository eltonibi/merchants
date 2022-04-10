**PSP Gateway**
----
  Create a payment by charging a payment card

* **URL** example

  http://{host}:{port}/api/charge/{api_token}

* **Method:**  `POST`
  
*  **URL Params**  `api_token`

* **Data Params** 
```json
{
  "number": "5560000000000001",
  "year": "2023",
  "month": "4",
  "holder_name": "Elton Ibi",
  "cvc": "234",
  "amount": "100",
  "description": "Order #123"
}
```
* `number` (required) Card number
* `year` (required) Card expiration year
* `month` (required) Card expiration month
* `holder_name` (required) Cardholder name
* `cvc` (required) Card verification code
* `amount` (required) Charged amount
* `description` (required) A description of the charge.


* **Success Response:**

  * **Code:** 200 <br />
```json
{
  "status": "success",
  "message": "Success",
  "body": {
    "token": "ch_PbntajbO_eU8bGK7QEcwiQ",
    "success": true,
    "amount": 10000,
    "currency": "AUD",
    "description": "My First Test Charge (created for API docs)",
    "email": "ibi.elton@gmail.com",
    "ip_address": null,
    "created_at": "2022-04-10T18:20:52Z",
    "status_message": "Success",
    "error_message": null,
    "card": {
      "token": "card_l-Ut4fQdVP9BMalANzma0w",
      "scheme": "visa",
      "display_number": "XXXX-XXXX-XXXX-0000",
      "issuing_country": "AU",
      "expiry_month": 12,
      "expiry_year": 2030,
      "name": "Nikos Ibi",
      "address_line1": "Optasias 6",
      "address_line2": "",
      "address_city": "Athens",
      "address_postcode": "",
      "address_state": "",
      "address_country": "AU",
      "customer_token": "cus_CpfpZFuEbN9voaTWBQ0WrQ",
      "primary": true,
      "network_type": null
    },
    "transfer": [],
    "amount_refunded": 0,
    "total_fees": 205,
    "merchant_entitlement": 9795,
    "refund_pending": false,
    "authorisation_token": null,
    "authorisation_expired": false,
    "authorisation_voided": false,
    "captured": true,
    "captured_at": "2022-04-10T18:20:52Z",
    "settlement_currency": "AUD",
    "active_chargebacks": false,
    "metadata": []
  }
}
```

* **Failed Response:**

  * **Code:** 400 <br />
```json
{
    "message": "invalid_card",
    "errors": {
        "holder_name": "cannot be empty",
        "month": "is not a valid month"
    }
}
```

* **Sample Call:**
```code
curl --location --request POST 'http://{host}:{port}/api/charge/{api_token}' \
--header 'Content-Type: application/json' \
--data-raw '{
  "number": "5520000000000000",
  "year": "2023",
  "month": "4",
  "holder_name": "Elton Ibi",
  "cvc": "234",
  "amount": "100",
  "description": "Order #123"
}'
```
