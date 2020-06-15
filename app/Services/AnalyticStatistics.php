<?php


namespace App\Services;


use App\Property;
use App\PropertyAnalytic;

class AnalyticStatistics
{
    use RegionSelectorBuilder;

    public static function getStats($commaSeparatedRegionName)
    {

        $regionOrPropertyExists = Property::regionOrPropertyExists($commaSeparatedRegionName, true);
        if (!$regionOrPropertyExists) {
            return (["status" => "error", "message" => "county[,state[,suburb]] ($commaSeparatedRegionName) does not exist in our records!"]);
        }
        $propertiesCountTotal = Property::getPropertiesCountInRegion($commaSeparatedRegionName);
        $stats = PropertyAnalytic::getStatisticsReportOnRegion($commaSeparatedRegionName, $propertiesCountTotal);

        return (["status" => "success", "message" => "completed", "results" => $stats]);
    }


}
