<?php

namespace AppBundle\Entity;

/**
 * AppActivity
 */
class AppActivity
{
    /**
     * @var string
     */
    private $app;

    /**
     * @var string
     */
    private $activity;

    /**
     * @var integer
     */
    private $id;


    /**
     * Set app
     *
     * @param string $app
     *
     * @return AppActivity
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return string
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set activity
     *
     * @param string $activity
     *
     * @return AppActivity
     */
    public function setActivity($activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return string
     */
    public function getActivity()
    {
        return $this->activity;
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
}

