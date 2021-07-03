# URL Shortener
### Laravel API example project: URL Shortener

Run tests:
```./artisan test --coverage-html coverage```

Check out postman collection: ```/URL Shortener.postman_collection.json```

### Description
Single Controller/Service solution.
No repository pattern applied. `ShortenerService` is a single domain-aware object.
No authorization/authentication, no middleware. It is assumed that the service is designed for internal use only. 

### How it works
Endpoints return 8-character long hashes that identify website addresses. The responsibility of generating actual shortened URL has been deferred to consumer.
URL would look something like this `https://shortner.com/<hash>`

### Endpoints

#### List shortened urls (todo: pagination)
```curl --location -g --request GET '{{url-shortener}}/api/website/list'```

Response 200
```
[
    {
        "hash": "6sawhkic",
        "url": "http://localhost.local/my-long-link/to/my/website"
    },
    {
        "hash": "9fx0kla8",
        "url": "https://google.com"
    }
]
```

#### Create URL shortener hash

```
curl --location -g --request POST '{{url-shortener}}/api/website?XDEBUG_SESSION_START=13352' \
--header 'Content-Type: application/json' \
--data-raw '{
    "url": "https://google.com"
}'
```

Response 201 or 400 or 200

```
{
    "hash": "ykcy6amo"
}
```

#### Delete stored website

```
curl --location -g --request DELETE '{{url-shortener}}/api/website' \
--header 'Content-Type: application/json' \
--data-raw '{
    "url": "https://google.com"
}'
```

Response 200 or 404

```
{
    "message": "URL deleted"
}
```
OR
```
{
    "message": "URL not found"
}
```

## UPDATE
### 2021-07-02

Added token authentication

```
curl --location --request DELETE 'http://url-shortener.local/api/website?XDEBUG_SESSION_START=19293' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 7b18eaae-f1db-4e0f-b5e8-957a04c57187s' \
--header 'Content-Type: application/json' \
--header 'Cookie: XDEBUG_SESSION=19293' \
--data-raw '{
    "url": "https://google.com"
}'
```

### To create user run command 
```
./artisan user:create <name>
```

User will be created and token will be generated and presented.

### Regenerate token for a user
```
./artisan user:regen <name>
```

## Make user admin
DELETE endpoint requires user to have is_admin flag up
```
./artisan user:admin <name>
```
