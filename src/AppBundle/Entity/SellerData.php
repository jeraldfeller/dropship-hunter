<?php

namespace AppBundle\Entity;

/**
 * SellerData
 */
class SellerData
{
    /**
     * @var integer
     */
    private $productListId;

    /**
     * @var string
     */
    private $sellerId;

    /**
     * @var string
     */
    private $sellerLocation;

    /**
     * @var integer
     */
    private $sellersRank;

    /**
     * @var string
     */
    private $memberSince;

    /**
     * @var integer
     */
    private $positive;

    /**
     * @var integer
     */
    private $neutral;

    /**
     * @var integer
     */
    private $negative;

    /**
     * @var integer
     */
    private $itemsForSale;

    /**
     * @var string
     */
    private $sellerPage;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set productListId
     *
     * @param integer $productListId
     *
     * @return SellerData
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
     * Set sellerId
     *
     * @param string $sellerId
     *
     * @return SellerData
     */
    public function setSellerId($sellerId)
    {
        $this->sellerId = $sellerId;

        return $this;
    }

    /**
     * Get sellerId
     *
     * @return string
     */
    public function getSellerId()
    {
        return $this->sellerId;
    }

    /**
     * Set sellerLocation
     *
     * @param string $sellerLocation
     *
     * @return SellerData
     */
    public function setSellerLocation($sellerLocation)
    {
        $this->sellerLocation = $sellerLocation;

        return $this;
    }

    /**
     * Get sellerLocation
     *
     * @return string
     */
    public function getSellerLocation()
    {
        return $this->sellerLocation;
    }

    /**
     * Set sellersRank
     *
     * @param integer $sellersRank
     *
     * @return SellerData
     */
    public function setSellersRank($sellersRank)
    {
        $this->sellersRank = $sellersRank;

        return $this;
    }

    /**
     * Get sellersRank
     *
     * @return integer
     */
    public function getSellersRank()
    {
        return $this->sellersRank;
    }

    /**
     * Set memberSince
     *
     * @param string $memberSince
     *
     * @return SellerData
     */
    public function setMemberSince($memberSince)
    {
        $this->memberSince = $memberSince;

        return $this;
    }

    /**
     * Get memberSince
     *
     * @return string
     */
    public function getMemberSince()
    {
        return $this->memberSince;
    }

    /**
     * Set positive
     *
     * @param integer $positive
     *
     * @return SellerData
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;

        return $this;
    }

    /**
     * Get positive
     *
     * @return integer
     */
    public function getPositive()
    {
        return $this->positive;
    }

    /**
     * Set neutral
     *
     * @param integer $neutral
     *
     * @return SellerData
     */
    public function setNeutral($neutral)
    {
        $this->neutral = $neutral;

        return $this;
    }

    /**
     * Get neutral
     *
     * @return integer
     */
    public function getNeutral()
    {
        return $this->neutral;
    }

    /**
     * Set negative
     *
     * @param integer $negative
     *
     * @return SellerData
     */
    public function setNegative($negative)
    {
        $this->negative = $negative;

        return $this;
    }

    /**
     * Get negative
     *
     * @return integer
     */
    public function getNegative()
    {
        return $this->negative;
    }

    /**
     * Set itemsForSale
     *
     * @param integer $itemsForSale
     *
     * @return SellerData
     */
    public function setItemsForSale($itemsForSale)
    {
        $this->itemsForSale = $itemsForSale;

        return $this;
    }

    /**
     * Get itemsForSale
     *
     * @return integer
     */
    public function getItemsForSale()
    {
        return $this->itemsForSale;
    }

    /**
     * Set sellerPage
     *
     * @param string $sellerPage
     *
     * @return SellerData
     */
    public function setSellerPage($sellerPage)
    {
        $this->sellerPage = $sellerPage;

        return $this;
    }

    /**
     * Get sellerPage
     *
     * @return string
     */
    public function getSellerPage()
    {
        return $this->sellerPage;
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
     * @return SellerData
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
     * @var integer
     */
    private $productListLinksId;


    /**
     * Set productListLinksId
     *
     * @param integer $productListLinksId
     *
     * @return SellerData
     */
    public function setProductListLinksId($productListLinksId)
    {
        $this->productListLinksId = $productListLinksId;

        return $this;
    }

    /**
     * Get productListLinksId
     *
     * @return integer
     */
    public function getProductListLinksId()
    {
        return $this->productListLinksId;
    }
    /**
     * @var boolean
     */
    private $toExport;


    /**
     * Set toExport
     *
     * @param boolean $toExport
     *
     * @return SellerData
     */
    public function setToExport($toExport)
    {
        $this->toExport = $toExport;

        return $this;
    }

    /**
     * Get toExport
     *
     * @return boolean
     */
    public function getToExport()
    {
        return $this->toExport;
    }
    /**
     * @var integer
     */
    private $usedCount;


    /**
     * Set usedCount
     *
     * @param integer $usedCount
     *
     * @return SellerData
     */
    public function setUsedCount($usedCount)
    {
        $this->usedCount = $usedCount;

        return $this;
    }

    /**
     * Get usedCount
     *
     * @return integer
     */
    public function getUsedCount()
    {
        return $this->usedCount;
    }
}
