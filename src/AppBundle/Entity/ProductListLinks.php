<?php

namespace AppBundle\Entity;

/**
 * ProductListLinks
 */
class ProductListLinks
{
    /**
     * @var integer
     */
    private $productListId;

    /**
     * @var string
     */
    private $productUrl;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set productListId
     *
     * @param integer $productListId
     *
     * @return ProductListLinks
     */
    public function setProductListId($productListId)
    {
        $this->productListId = $productListId;

        return $this;
    }

    /**
     * Get productListId
     *
     * @return integer
     */
    public function getProductListId()
    {
        return $this->productListId;
    }

    /**
     * Set productUrl
     *
     * @param string $productUrl
     *
     * @return ProductListLinks
     */
    public function setProductUrl($productUrl)
    {
        $this->productUrl = $productUrl;

        return $this;
    }

    /**
     * Get productUrl
     *
     * @return string
     */
    public function getProductUrl()
    {
        return $this->productUrl;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var string
     */
    private $status;


    /**
     * Set status
     *
     * @param string $status
     *
     * @return ProductListLinks
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
