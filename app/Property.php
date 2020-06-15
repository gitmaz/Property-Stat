<?php

namespace App;

use App\Services\RegionSelectorBuilder;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use RegionSelectorBuilder;

    protected $table = 'properties';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $connection;

    /**
     * Property constructor.
     * @param string $connection
     */
    public function __construct($connection = 'mysql')
    {
        $this->connection = $connection;
    }

    /**
     * @param string $connection
     * @return Property
     */
    public static function connect($connection = 'mysql')
    {
        $instance = new self($connection);
        $instance->setConnection($connection);
        return $instance;
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function analytics()
    {
        return $this->belongsToMany('AnalyticType', 'property_analytics', 'property_id', 'analytic_type_id')
            //->using('App\AnalyticType')
            ->withPivot(['analytic_type_id', 'value']);
    }

    /**
     * @param $propertyName
     * @return array
     */
    public function getAnalyticsAsKeyValuePairs($propertyName)
    {

        $user = Self::with(['analytic'])
            ->where('property_name', $propertyName)
            ->first();

        $keyVals = [];
        foreach ($user->analytics as $analytic) {
            $keyVals[$analytic->name] = $analytic->value;
        }

        return $keyVals;
    }

    /**
     * @param $commaSeparatedRegionName
     * @param $clearCache
     * @return mixed
     */
    public static function regionOrPropertyExists($commaSeparatedRegionName, $clearCache)
    {
        $regionSelectorPhrase = RegionSelectorBuilder::getSelectorCached($commaSeparatedRegionName, false, $clearCache);

        $result = Property::whereRaw($regionSelectorPhrase)
            ->exists();

        return $result;
    }

    /**
     * @param $commaSeparatedRegionName
     * @return mixed
     */
    public static function getPropertiesCountInRegion($commaSeparatedRegionName)
    {

        $regionSelectorPhrase = RegionSelectorBuilder::getSelectorCached($commaSeparatedRegionName);
        $propertiesCountTotal = Property::whereRaw($regionSelectorPhrase)
            ->count();
        return $propertiesCountTotal;
    }

    /**
     * @return array
     */
    public static function list()
    {
        return self::all()->toArray();
    }

    /**
     * @param $guid
     */
    public static function deleteByGuid($guid)
    {
        $property = Property::where("guid", $guid)
            ->first();
        $property->delete();
    }

    /**
     * @param $guid
     * @param $country
     * @param $state
     * @param $suburb
     * @return array
     */
    public static function updateAs($guid, $country, $state, $suburb)
    {
        $analyticType = self::where("guid", $guid)
            ->first();

        if ($analyticType == null) {
            $analyticType = new AnalyticType();
        }

        $analyticType->name = $guid;
        $analyticType->country = $country;
        $analyticType->state = $state;
        $analyticType->suburb = $suburb;

        $analyticType->save();

        return ["status" => "success", "message" => "Property with guid '$guid' updated!"];
    }
}
