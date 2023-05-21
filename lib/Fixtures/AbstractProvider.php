<?php

namespace Eph\Jaybizzle\CrawlerDetect\Fixtures;

abstract class AbstractProvider {

    /**
     * The data set.
     *
     * @var array
     */
    protected $data;
    /**
     * Return the data set.
     *
     * @return array
     */
    public function getAll() {

        return $this->data;
    }
}
