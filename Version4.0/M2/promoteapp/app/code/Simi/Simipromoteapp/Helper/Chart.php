<?php

/**
 * Promoteapp Content helper
 */

namespace Simi\Simipromoteapp\Helper;

class Chart extends Data
{
    const PATH_TEXT_BY_APP = 'chart/by_app';
    const PATH_TEXT_BY_WEBSITE = 'chart/by_website';
    const PATH_CHART_TITLE = 'chart/chart_title';
    const PATH_PERCENT = 'chart/percent';
    const PATH_CHART_ENABLE = 'chart/enable';
    

    public function getStoreConfig($path)
    {
        return parent::getStoreConfig('simipromoteapp/'.$path);
    }
    
    public function isEnable() {
        return $this->getStoreConfig(self::PATH_CHART_ENABLE);
    }

    public function getTextByApp(){
        return $this->getStoreConfig(self::PATH_TEXT_BY_APP);
    }

    public function getTextByWebsite(){
        return $this->getStoreConfig(self::PATH_TEXT_BY_WEBSITE);
    }

    public function getChartTitle(){
        return $this->getStoreConfig(self::PATH_CHART_TITLE);
    }

    public function getPercent(){
        return $this->getStoreConfig(self::PATH_PERCENT);
    }
}
