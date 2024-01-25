<?php

require 'vendor/autoload.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

// Function to scrape a single page
function scrapePage($url, $client)
{
    $crawler = $client->request('GET', $url);

    if ($crawler->filter('title')->count() < 1) {
        return false;
    }

    $title = $crawler->filter('title')->text();
    $body = '';
    $crawler->filter('p')->each(function (Crawler $node, $i) use (&$body) {
        $body .= $node->text() . "\n";
    });

    $links = $crawler->filter('a')->links();

    $result = [
        'title' => $title,
        'body' => $body,
        'url' => $url,
        'links' => []
    ];

    foreach ($links as $link) {
        $result['links'][] = $link->getUri();
    }

    return $result;
}

// Function to recursively scrape linked pages
function scrapeRecursive($url, $maxDepth, $mainDomain, $visitedUrls, $client)
{
    if ($maxDepth == 0) {
        return [];
    }

    $results = [];
    $scrapedData = scrapePage($url, $client);

    if ($scrapedData) {
        echo "Scraping: " . $url . PHP_EOL;
        $results[] = $scrapedData;

        foreach ($scrapedData['links'] as $link) {
            $parsedUrl = parse_url($link);
            
            // Check if the link is within the main domain
            if (isset($parsedUrl['host']) && $parsedUrl['host'] == $mainDomain) {
                if (!in_array($link, $visitedUrls)) {
                    $visitedUrls[] = $link;
                    $results = array_merge($results, scrapeRecursive($link, $maxDepth - 1, $mainDomain, $visitedUrls, $client));
                }
            }
        }
    }

    return $results;
}

if (isset($argv[1])) {
    $startUrl = $argv[1];
} else {
    // $startUrl = "https://prometsource.com";
    echo "Please provide a start URL as a command-line argument." . PHP_EOL;
    exit(1);
}

$filename = "scraped_data.json";
if (isset($argv[2])) {
    $filename = $argv[2];
}

// Set the maximum depth for recursion (adjust as needed)
$maxDepth = 3;

// Set to keep track of visited URLs
$visitedUrls = [];

// Parse the main domain from the start URL
$parsedStartUrl = parse_url($startUrl);
$mainDomain = 'www.' . $parsedStartUrl['host'];

// Create a Goutte client
$client = new Client();

// Start the scraping process
$scrapedResults = scrapeRecursive($startUrl, $maxDepth, $mainDomain, $visitedUrls, $client);

// Save the scraped data in a JSON file
$filecontent = [];
foreach($scrapedResults as $result) {
    $filecontent[] = [
        'url' => $result['url'],
        'title' => $result['title'],
        'body' => $result['body'],
    ];
}
file_put_contents($filename, json_encode($filecontent, JSON_PRETTY_PRINT));

echo "Scraped data saved in '$filename'." . PHP_EOL;
