# Benford's Law Compliance Checker

## Introduction
This Laravel project provides an API endpoint to check if a given set of integers conforms to [Benford's Law](https://en.wikipedia.org/wiki/Benford%27s_law). The project is structured following standard Laravel practices and includes a comprehensive suite of unit tests for robustness and reliability.

## Setup Instructions

### Requirements
- PHP >= 7.3
- Composer
- Laravel >= 8.x

### Installation
1. Clone the repository:
   ```sh
   git clone https://github.com/adanzweig/laravel-benfords-law.git
   ```
2. Navigate to the project directory:
   ```sh
   cd your-repo
   ```
3. Install dependencies:
   ```sh
   composer install
   ```
4. Generate the application key:
   ```sh
   php artisan key:generate
   ```
5. Start the development server:
   ```sh
   php artisan serve
   ```
   The API will be available at `http://localhost:8000`.

## Usage

### API Endpoint

Documentation:
`http://localhost:8000/swaggerUI`

The API provides the following endpoint:

- `POST /api/check-benfords-law`
  - Accepts a JSON payload with an array of integers.
  - Returns a JSON response indicating whether the set conforms to Benford's Law.

Example request:
```json
{
  "numbers": [123, 111, 145, 160, 174, 182, 191, 202, 210]
}
```

Example response:
```json
{
  "conforms_to_benford": true,
  "observed_distribution": { ... },
  "benfords_distribution": { ... }
}
```

### Running Tests
Execute the PHPUnit tests with the following command:
```sh
./vendor/bin/phpunit
```

## Error Handling
The API will return appropriate HTTP status codes for various error states:

- `400 Bad Request`: Input validation errors (e.g., non-integer or negative numbers).

## Contact Information
For any queries or further assistance, please contact [Adan Zweig] at [adanzweig@gmail.com].

## License
This project is open-sourced software licensed under the [MIT license](LICENSE).
