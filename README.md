
docker compose down
docker compose down -v

docker compose up --watch

or

docker compose up -d

# Requests

```bash
### Get All
curl -X GET http://localhost:8080/donors

### Create Donation
curl -X POST http://localhost:8080/donors
-H "Content-Type: application/json"
-d '{
    "name": "John Doe",
    "email": "john@example.com",
    "cpf": "12345678901",
    "phone": "1234567890",
    "birth_date": "1990-01-01",
    "donation_interval": "Bimestral",
    "donation_value": 100.00,
    "payment_method": "Credito",
    "card_brand": "Visa",
    "card_number": "4111111111111111",
    "address": "123 Main St"
}'

### Update Donation
curl -X PUT http://localhost:8080/update.php?id=1 \
-H "Content-Type: application/json" \
-d '{
    "name": "Updated Name",
    "email": "updated@example.com",
    "donation_value": 150.00,
    "payment_method": "Crédito",
    "card_brand": "Mastercard",
    "card_number": "5555555555554444"
}'

### Delete Donation
curl -X DELETE http://localhost:8080/delete.php?id=1

### Create payment detail
curl -X POST http://localhost:8080/payment-details \
-H "Content-Type: application/json" \
-d '{
    "donor_id": 1,
    "payment_method": "Crédito",
    "card_info": {
        "brand": "Visa",
        "number": "4111111111111111"
    }
}'

### Create address
curl -X POST http://localhost:8080/addresses \
-H "Content-Type: application/json" \
-d '{
    "donor_id": 1,
    "street": "Main Street",
    "number": 123,
    "neighborhood": "Downtown",
    "city": "New York",
    "state": "NY",
    "zip_code": "10001"
}'

### Update payment detail
curl -X PUT http://localhost:8080/payment-details/1 \
-H "Content-Type: application/json" \
-d '{
    "payment_method": "Débito",
    "account_info": {
        "bank": "Bank of America",
        "account": "123456789"
    }
}'

### Delete address
curl -X DELETE http://localhost:8080/addresses/1
```
