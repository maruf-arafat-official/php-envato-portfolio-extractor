<?php

namespace Tests;

use MAO\EnvatoExtractor;
use PHPUnit\Framework\TestCase;

class EnvatoExtractorTest extends TestCase
{
    protected $validUrl = 'https://themeforest.net/user/themestransmit/portfolio'; // Replace with a valid URL for testing
    protected $invalidUrl = 'https://your-invalid-url.com';

    public function testConstructorWithValidUrl()
    {
        $extractor = new EnvatoExtractor($this->validUrl);
        $this->assertInstanceOf(EnvatoExtractor::class, $extractor);
    }

    public function testConstructorWithInvalidUrl()
    {
        $this->expectException(\Exception::class);
        new EnvatoExtractor($this->invalidUrl);
    }

    public function testGetProductsReturnsArray()
    {
        $extractor = new EnvatoExtractor($this->validUrl);
        $products = $extractor->getProducts();

        // Check that the returned value is an array
        $this->assertIsArray($products);

        // Check that the products array is not empty
        $this->assertNotEmpty($products);
    }

    /**
     * @throws \Exception
     */
    public function testSafeTrim()
    {
        $extractor = new EnvatoExtractor($this->validUrl);

        $this->assertEquals('Hello World', $extractor->safeTrim('   Hello World   '));
        $this->assertEquals('', $extractor->safeTrim('   '));
        $this->assertEquals('123', $extractor->safeTrim(123)); // Check non-string input
    }
}