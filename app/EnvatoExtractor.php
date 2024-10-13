<?php
namespace MAO;

use Exception;

class EnvatoExtractor extends Extractor
{
    public function __construct($envatoPortfolioUrl)
    {
        try {
            if (!filter_var($envatoPortfolioUrl, FILTER_VALIDATE_URL)) {
                throw new Exception("Invalid URL: $envatoPortfolioUrl");
            }
            $this->setUrl($envatoPortfolioUrl)->loadHTML();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getProducts()
    {
        // Get the products list node
        $productsListNode = $this->getFirstNode('//ul[@class="product-list"]');

        if ($productsListNode === false) {
            throw new Exception('No products found');
        }

        $productDetails = array();

        // Loop through each product <li> element
        foreach ($productsListNode->getElementsByTagName('li') as $product) {

            // Get the product ID
            $productId = $product->getAttribute("data-item-id");

            if($productId) {

                // Get product heading
                $productHeading = $this->getValueFromNode('.//h3[@class="product-list__heading"]', $product);

                // get product preview image
                $productPreviewImage = $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a/img', $product, "data-preview-url");

                // Get product categories
                $productCategories = $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a/img', $product, "data-item-category");

                // Get product sales
                $productSales = $this->getValueFromNode('.//div[@class="product-list__sales-desktop"]', $product);

                // Get product price
                $productPrice = $this->getValueFromNode('.//div[@class="item-thumbnail"]/div[@class="item-thumbnail__image"]/a/img', $product, "data-item-cost");

                // categories
                $categories = array_map(function($category) {
                    return $this->safeTrim($category);
                }, explode('/', $productCategories));

                $productDetails[$productId] = array(
                    'heading' => $productHeading,
                    'preview' => $productPreviewImage,
                    'Categories' => $categories,
                    'sales' => $productSales,
                    'price' => $productPrice,
                );
            }
        }

        return $productDetails; // Return all product details
    }


    // Define a safe trim function
    function safeTrim($item) {
        // Check if the item is a string before trimming
        if (is_string($item)) {
            return trim($item);
        }
        // If not a string, return it unchanged or handle the error
        return $item; // or you could return '' or throw an exception
    }
}
