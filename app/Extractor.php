<?php

namespace MAO;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Exception;

class Extractor
{
    protected $url;
    protected $xpath;

    /**
     * Set the URL for the extractor.
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Load the HTML from the set URL and initialize the DOM and XPath.
     *
     * @throws Exception
     * @return $this
     */
    public function loadHTML()
    {
        if (empty($this->url)) {
            throw new Exception("No URL set. Use setUrl() to define the target URL.");
        }

        $html = $this->fetchHTML($this->url);

        $dom = new DOMDocument();

        // Disable warnings from invalid HTML
        libxml_use_internal_errors(true);

        if (@$dom->loadHTML($html) === false) {
            throw new Exception("Failed to load HTML from the provided URL.");
        }

        libxml_clear_errors(); // Clear any parsing errors

        $dom->preserveWhiteSpace = false;
        $this->xpath = new DOMXpath($dom); // Initialize XPath correctly

        return $this;
    }

    /**
     * Fetch the HTML content from a URL using cURL.
     *
     * @param string $url
     * @throws Exception
     * @return string
     */
    protected function fetchHTML($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set timeout for the request

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        if (empty($html)) {
            throw new Exception("Empty response from URL: $url");
        }

        return $html;
    }

    /**
     * Get an attribute value from the first matching node for the given XPath.
     *
     * @param string $xpath
     * @param string $attribute
     * @return string|false
     */
    public function getAttribute($xpath, $attribute)
    {
        $result = $this->getFirstNode($xpath);
        return $result ? $result->getAttribute($attribute) : false;
    }

    /**
     * Get the value (node text) from the first matching node for the given XPath.
     *
     * @param string $xpath
     * @return string|false
     */
    public function getValue($xpath)
    {
        $result = $this->getFirstNode($xpath);
        return $result ? trim($result->nodeValue) : false;
    }

    /**
     * Get the value (node text) from the first matching node for the given XPath relative to a parent node.
     *
     * @param  string  $xpath
     * @param  DOMNode  $parentNode
     * @param  null  $attribute
     * @return string
     */
    public function getValueFromNode($xpath, DOMNode $parentNode, $attribute = null)
    {
        // Use the current XPath instance
        $result = $this->xpath->query($xpath, $parentNode);

        if ($result->length == 0) {
            // Try the parent node
            $result = $this->xpath->query($xpath, $parentNode->parentNode);
        }

        if ($attribute) {
            return trim($result->item(0)->getAttribute($attribute));
        }

        return trim($result->item(0)->nodeValue);
    }

    /**
     * Get all matching nodes for the given XPath.
     *
     * @param string $xpath
     * @return DOMNodeList|false
     */
    public function getValues($xpath)
    {
        $result = $this->xpath->query($xpath);
        return ($result && $result->length > 0) ? $result : false;
    }

    /**
     * Get the first matching node for the given XPath.
     *
     * @param string $xpath
     * @return DOMNode|false
     */
    public function getFirstNode($xpath)
    {
        $result = $this->xpath->query($xpath);
        return ($result && $result->length > 0) ? $result->item(0) : false;
    }
}
