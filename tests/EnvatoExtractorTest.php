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
        $portfolios = $extractor->getPortfolios();

        // Check that the returned value is an array
        $this->assertIsArray($portfolios);

        // Check that the products array is not empty
        $this->assertNotEmpty($portfolios);
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