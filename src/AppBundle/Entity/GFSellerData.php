<?php

namespace AppBundle\Entity;

/**
 * GFSellerData
 */
class GFSellerData
{
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
     * @var string
     */
    private $status;

    /**
     * @var boolean
     */
    private $toExport = '1';

    /**
     * @var integer
     */
    private $usedCount;

    /**
     * @var integer
     */
    private $newCount;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\GProductListLinks
     */
    private $gProductListLinks;


    /**
     * Set sellerId
     *
     * @param string $sellerId
     *
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * @return GFSellerData
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
     * Set status
     *
     * @param string $status
     *
     * @return GFSellerData
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
     * Set toExport
     *
     * @param boolean $toExport
     *
     * @return GFSellerData
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
     * Set usedCount
     *
     * @param integer $usedCount
     *
     * @return GFSellerData
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

    /**
     * Set newCount
     *
     * @param integer $newCount
     *
     * @return GFSellerData
     */
    public function setNewCount($newCount)
    {
        $this->newCount = $newCount;

        return $this;
    }

    /**
     * Get newCount
     *
     * @return integer
     */
    public function getNewCount()
    {
        return $this->newCount;
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
     * Set gProductListLinks
     *
     * @param \AppBundle\Entity\GProductListLinks $gProductListLinks
     *
     * @return GFSellerData
     */
    public function setGProductListLinks(\AppBundle\Entity\GProductListLinks $gProductListLinks = null)
    {
        $this->gProductListLinks = $gProductListLinks;

        return $this;
    }

    /**
     * Get gProductListLinks
     *
     * @return \AppBundle\Entity\GProductListLinks
     */
    public function getGProductListLinks()
    {
        return $this->gProductListLinks;
    }
}

