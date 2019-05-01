# Sanctions listing

It loads Sanctions list XML files in database and then profive REST interface to do "Google search" by names.

## Requirements

- PHP 7.x
- Apache 2.x (for REST service)
- PostgreSQL database (empty, it will create necesarry table on its own)
- Composer

## Installation

- download and unzip this repo in webserver folder
- run `composer update`

## Import script usage

``` bash
php -e importer.php
``` 

## REST interface usage

Request:
```curl
GET https://host/SanctionsList/api/v1/search/muhammad+ali
``` 

Response:
```json
[
    {
        "rank": "0.1",
        "list": "ofac",
        "type": "individual",
        "name": "Bilal Ali Muhammad AL-WAFI",
        "country": "Yemen",
        "sync_time": {
            "date": "2019-01-08 23:45:48.000000",
            "timezone_type": 3,
            "timezone": "UTC"
        }
    },
    {
        "rank": "0.1",
        "list": "ofac",
        "type": "individual",
        "name": "Ali Muhammad QANSU",
        "country": "Lebanon",
        "sync_time": {
            "date": "2019-01-08 23:45:48.000000",
            "timezone_type": 3,
            "timezone": "UTC"
        }
    },
    {
        "rank": "0.05",
        "list": "ofac",
        "type": "individual",
        "name": "Muhammad Ahmad \u0027Ali AL-ISAWI",
        "country": "Egypt",
        "sync_time": {
            "date": "2019-01-08 23:45:47.000000",
            "timezone_type": 3,
            "timezone": "UTC"
        }
    }
]
``` 

Rank is higher if names are near each other.
For more info how rank is calculated see https://www.postgresql.org/docs/current/textsearch-controls.html (12.3.3)
