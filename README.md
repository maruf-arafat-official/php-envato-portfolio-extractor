# Envato Portfolio Extractor

Envato Portfolio Extractor is a PHP-based application that extracts product details from an Envato portfolio page, specifically from ThemeForest. It uses XPath to scrape product information such as product ID, name, image, category, sales, and price from the portfolio page.

## Features

- Extracts product details from an Envato portfolio URL
- Fetches details like product name, preview image, categories, sales count, and price
- Implements clean and modular code with the help of an `Extractor` class
- Simple interface for future extensions and improvements
- Includes unit tests for validation of functionality

## Requirements

- PHP 7.4+ (supports PHP 8+)
- Composer
- DOM extension (should be enabled by default in PHP)
- cURL extension (should be enabled by default in PHP)
- PHPUnit for running unit tests

## Installation

1. **Clone the repository:**

```bash
git clone https://github.com/your-username/envato-extractor.git
cd envato-extractor
   ```
   
2. **Install dependencies using Composer:**
  
Make sure you have Composer installed. Run the following command:

```bash
./composer install
```

3. **Set up PHPUnit for testing:**

PHPUnit is already configured in the phpunit.xml file. Run the following command to verify that the tests work:

```bash 
./vendor/bin/phpunit
```

## Usage

To extract products from an Envato portfolio, instantiate the ``EnvatoExtractor`` class. Here's a basic example:

```php 
use MAO\EnvatoExtractor;

try {
    $envatoExtractor = new EnvatoExtractor('https://themeforest.net/user/themestransmit/portfolio');
    $products = $envatoExtractor->getPortfolios();

    print_r($products);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

## Example Output

The output will be structured like this:

```aiignore
Array
(
    [12345678] => Array
        (
            [heading] => Example Product
            [preview] => https://preview-url
            [Categories] => Array
                (
                    [0] => Web Design
                    [1] => UI/UX
                )
            [sales] => 1,000 Sales
            [price] => $29
            ["url"] => https://themeforest.net/item/{portfolio}
        )
    ...
)
```

## Running Tests

To run the included tests, use:
```bash
./vendor/bin/phpunit
```

## Directory Structure

```bash
/src
    /App
        Extractor.php
        EnvatoExtractor.php
/tests
    EnvatoExtractorTest.php
/vendor
    (Composer-managed dependencies)
phpunit.xml
composer.json
```

## Contributing

Contributions are welcome! Please follow these steps:

- Fork the repository
- Create a feature branch (``git checkout -b feature/YourFeature``).
- Commit your changes (``git commit -m 'Add some feature'``)
- Push to the branch (``git push origin feature/YourFeature``)
- Open a pull request.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

