<?php

namespace AppBundle\Entity;

/**
 * GProductListLinks
 */
class GProductListLinks
{
    /**
     * @var string
     */
    private $productUrl;

    /**
     * @var string
     */
    private $status;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\GProductList
     */
    private $gProductList;


    /**
     * Set productUrl
     *
     * @param string $productUrl
     *
     * @return GProductListLinks
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
     * Set status
     *
     * @param string $status
     *
     * @return GProductListLinks
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
     * Set gProductList
     *
     * @param \AppBundle\Entity\GProductList $gProductList
     *
     * @return GProductListLinks
     */
    public function setGProductList(\AppBundle\Entity\GProductList $gProductList = null)
    {
        $this->gProductList = $gProductList;

        return $this;
    }

    /**
     * Get gProductList
     *
     * @return \AppBundle\Entity\GProductList
     */
    public function getGProductList()
    {
        return $this->gProductList;
    }
}

