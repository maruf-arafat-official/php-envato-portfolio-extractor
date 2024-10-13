<?php


namespace MAO;

use DOMNode;
use Exception;

class EnvatoExtractor extends Extractor
{
    /**
     * EnvatoExtractor constructor.
     *
     * @param  string  $envatoPortfolioUrl  The URL of the Envato portfolio to extract data from.
     * @throws Exception If the provided URL is not valid.
     */
    public function __construct(string $envatoPortfolioUrl)
    {
        if (!filter_var($envatoPortfolioUrl, FILTER_VALIDATE_URL)) {
            throw new Exception("Invalid URL: $envatoPortfolioUrl");
        }

        $this->setUrl($envatoPortfolioUrl)->loadHTML();
    }

    /**
     * Retrieves portfolio details from the Envato portfolio.
     *
     * @return array An associative array of portfolio details, with portfolio IDs as keys.
     * @throws Exception If no portfolio list is found.
     */
    public function getPortfolios()
    {
        $portfoliosNode = $this->getFirstNode('//ul[@class="product-list"]');

        if (!$portfoliosNode) {
            throw new Exception('No portfolio found!');
        }

        $portfolioDetails = [];

        foreach ($portfoliosNode->getElementsByTagName('li') as $portfolio) {
            $portfolioId = $portfolio->getAttribute("data-item-id");

            if ($portfolioId) {
                $portfolioDetails[$portfolioId] = $this->extractPortfolioData($portfolio);
            }
        }

        return $portfolioDetails;
    }

    /**
     * Trims a string safely, checking if the input is a string.
     *
     * @param  mixed  $item  The item to be trimmed.
     * @return string The trimmed string, or the original item if it's not a string.
     */
    public function safeTrim($item): string
    {
        return is_string($item) ? trim($item) : $item;
    }

    /**
     * Retrieves the heading of a portfolio item.
     *
     * @param  \DOMElement  $portfolio  The portfolio item DOM element.
     * @return string The portfolio heading.
     */
    public function getPortfolioHeading(\DOMElement $portfolio): string
    {
        return $this->getValueFromNode('.//h3[@class="product-list__heading"]', $portfolio);
    }

    /**
     * Retrieves the preview image URL of a portfolio item.
     *
     * @param  \DOMElement  $portfolio  The portfolio item DOM element.
     * @return string The URL of the portfolio's preview image.
     */
    public function getPortfolioPreviewImage(\DOMElement $portfolio): string
    {
        return $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a/img',
            $portfolio, "data-preview-url");
    }

    /**
     * Retrieves the categories of a portfolio item, split into an array.
     *
     * @param  \DOMElement  $portfolio  The portfolio item DOM element.
     * @return array An array of categories for the portfolio item.
     */
    public function getPortfolioCategories(\DOMElement $portfolio): array
    {
        $categories = $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a/img',
            $portfolio, "data-item-category");

        return array_map([$this, 'safeTrim'], explode('/', $categories));
    }

    /**
     * Retrieves the sales count of a portfolio item.
     *
     * @param  \DOMElement  $portfolio  The portfolio item DOM element.
     * @return string The number of sales for the portfolio item.
     */
    public function getPortfolioSales(\DOMElement $portfolio): string
    {
        return $this->getValueFromNode('.//div[@class="product-list__sales-desktop"]', $portfolio);
    }

    /**
     * Retrieves the price of a portfolio item.
     *
     * @param  \DOMElement  $portfolio  The portfolio item DOM element.
     * @return string The price of the portfolio item.
     */
    public function getPortfolioPrice(\DOMElement $portfolio): string
    {
        return $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a/img',
            $portfolio, "data-item-cost");
    }

    /**
     * Extracts the base URL (scheme and host) from a given URL.
     *
     * @param string $url The full URL to extract the base from.
     * @return string The base URL (scheme and host).
     */
    public function getBaseUrl(string $url): string
    {
        // Parse the URL and extract its components
        $parsedUrl = parse_url($url);

        // Return the base URL (scheme and host)
        return $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    }

    /**
     * Retrieves the URL of a portfolio item.
     *
     * @param  \DOMElement  $portfolio  The portfolio item DOM element.
     * @return string The URL of the portfolio item.
     */
    public function getPortfolioUrl(\DOMElement $portfolio): string
    {
        $portfolioPath =  $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a',
            $portfolio, "href");

        return $this->getBaseUrl($this->url) . $portfolioPath;
    }

    /**
     * @param $portfolio
     * @return array
     */
    public function extractPortfolioData($portfolio): array
    {
        return [
            'heading' => $this->getPortfolioHeading($portfolio),
            'preview' => $this->getPortfolioPreviewImage($portfolio),
            'categories' => $this->getPortfolioCategories($portfolio),
            'sales' => $this->getPortfolioSales($portfolio),
            'price' => $this->getPortfolioPrice($portfolio),
            'url' => $this->getPortfolioUrl($portfolio),
        ];
    }
}
