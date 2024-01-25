# Web Scraping Script in PHP

This PHP script allows you to perform web scraping of a website, starting from a specified URL. It follows links within the main domain and scrapes information from web pages. The scraped data is saved in JSON format.

## Prerequisites

- PHP 8 and above
- [Composer](https://getcomposer.org/)

## Installation

1. Install the required dependencies using Composer:

```bash
composer install
```

## Usage

To use the web scraping script, follow these steps:

1. Open a terminal or command prompt.
2. Run the script with the desired start URL as a command-line argument:
```bash
php scraper.php https://example.com filename.json
```
Replace scraper.php with the name of your PHP script file and https://example.com with the URL you want to start scraping from.

Replace filename.json with a whatever filename you want.

3. The script will scrape the specified website, following links within the main domain, and save the scraped data in a JSON file named `filename.json`.

## Configuration

You can configure the script by modifying the following variables in the PHP script:

- maxDepth: Set the maximum depth for recursion to control how many linked pages are scraped.

## Disclaimer

Please use this script responsibly and ensure that you have the right to access and scrape content from websites. Respect websites' terms of service and legal regulations regarding web scraping.
