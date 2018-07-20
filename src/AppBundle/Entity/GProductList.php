<?php

namespace AppBundle\Entity;

/**
 * GProductList
 */
class GProductList
{
    /**
     * @var string
     */
    private $productTitle;

    /**
     * @var string
     */
    private $status;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\GSellerData
     */
    private $gSeller;


    /**
     * Set productTitle
     *
     * @param string $productTitle
     *
     * @return GProductList
     */
    public function setProductTitle($productTitle)
    {
        $this->productTitle = $productTitle;

        return $this;
    }

    /**
     * Get productTitle
     *
     * @return string
     */
    public function getProductTitle()
    {
        return $this->productTitle;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return GProductList
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
     * Set gSeller
     *
     * @param \AppBundle\Entity\GSellerData $gSeller
     *
     * @return GProductList
     */
    public function setGSeller(\AppBundle\Entity\GSellerData $gSeller = null)
    {
        $this->gSeller = $gSeller;

        return $this;
    }

    /**
     * Get gSeller
     *
     * @return \AppBundle\Entity\GSellerData
     */
    public function getGSeller()
    {
        return $this->gSeller;
    }
}
