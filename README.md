## Comment-system proof of concept
PHP backend API

Logic flow:  
1. All requests routed through `bootstrap.php`  
2. `bootstrap` parses the request and passes it to the appropriate `Controller` or returns `404`
3. `Controller` asks its appropriate `Service` for the necessary data
4. `Service` combines calls to different `repositories` and `factories` to map `DAO`s to the `Model` requested by the `Controller`
5. `Repository` makes the database connection, using `PDO` to prepare statements

### How to run
1. Clone the repository, `cd` into the new directory
2. Run `run.sh` - a small wrapper for `docker-compose` + `composer install`

### API endpoints
Endpoints envisioned for the proof of concept are listed here.  
Considered, but unimplemented endpoints are marked with `unimplemented`

#### GET
* articles/{id}
* comments/{id}
* comments/{id}/tree
* users/{id} `unimplemented`

#### POST `unimplemented`
* comments/
* comments/{id}
