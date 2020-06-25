<?php

namespace App;

use App\Services\RegionSelectorBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PropertyAnalytic extends Model
{
    use RegionSelectorBuilder;

    protected $table = 'property_analytics';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function analyticType()
    {
        return $this->belongsTo('App\AnalyticType', 'analytic_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function property()
    {
        return $this->belongsTo('App\Property', 'property_id');
    }

    /**
     * @param $commaSeparatedRegionName
     * @param $propertiesCountTotal
     * @return array
     */
    public static function getStatisticsReportOnRegion($commaSeparatedRegionName, $propertiesCountTotal)
    {

        $regionSelectorPhraseNoAlias = RegionSelectorBuilder::getSelectorCached($commaSeparatedRegionName, true);
        /* sample sql for calculating min, max
         select a.name, count(pa.analytic_type_id) as countWithValue, pa.analytic_type_id, min(pa.value) as minVal, max(pa.value) as maxVal
                        from analytic_types a
                                 left join property_analytics pa
                                           on a.id = pa.analytic_type_id
                                 left join properties p
                                            on p.id=pa.property_id
                                  where ( p.suburb = 'Ingleburn' or p.suburb is null)
                        group by pa.analytic_type_id
         */


        $analyticsStatRecords = DB::table('analytic_types as a')
            ->leftjoin('property_analytics as pa', 'a.id', '=', 'pa.analytic_type_id')
            ->leftjoin('properties as p', 'p.id', '=', 'pa.property_id')
            ->whereRaw("(($regionSelectorPhraseNoAlias) OR p.suburb is null)")
            ->select('a.name', 'pa.analytic_type_id', DB::raw('count(pa.analytic_type_id) as countWithValue'), 'pa.analytic_type_id', DB::raw('min(pa.value) as minVal'), DB::raw('max(pa.value) as maxVal'))
            ->groupBy('a.name', 'pa.analytic_type_id')
            ->get()
            ->toArray();


        $analytics = [];//this array is used as aaray of buckets ( putting analytic records of same name on its own bucket -with name of analytic as array key)
        foreach ($analyticsStatRecords as &$analyticsStatRecord) {

            $analyticsStatRecord->percentageWithValue = ($propertiesCountTotal == 0 ? 0 : round($analyticsStatRecord->countWithValue / $propertiesCountTotal, 3) * 100);
            $analyticsStatRecord->percentageWithoutValue = 100 - $analyticsStatRecord->percentageWithValue;
        }


        /* sample sql needed for finding median record. Note: finding median in pure SQl using @rowId  was possible but a bit dirty to implement, I traverse ORM instead)
         select a.name, pa.id, pa.value
         from analytic_types a
                  left join property_analytics pa
                            on a.id = pa.analytic_type_id
                  left join properties p
                             on p.id=pa.property_id and p.state = 'NSW'
         order by pa.analytic_type_id, pa.value
         */

        $analyticsRecords = DB::table('analytic_types as a')
            ->leftjoin('property_analytics as pa', 'a.id', '=', 'pa.analytic_type_id')
            ->leftjoin('properties as p', 'p.id', '=', 'pa.property_id')
            ->whereRaw("(($regionSelectorPhraseNoAlias) OR p.suburb is null)")
            ->select('a.name', 'pa.id', 'pa.value')
            ->orderBy(DB::raw('pa.analytic_type_id, pa.value'))
            ->get()
            ->toArray();


        //group analytics of the same type in its own bucket (used for finding middle record as median)
        foreach ($analyticsRecords as $analyticsRecord) {

            if (!isset($analytics[$analyticsRecord->name])) {
                $analytics[$analyticsRecord->name] = [];
            }
            $analytics[$analyticsRecord->name][] = $analyticsRecord->value;
        }


        //find the middle record or average of semi-middles in each bucket and save it as the median of the bucket anlyticType'
        $ind = 0;
        foreach ($analytics as $bucketName => $analyticBucket) {

            $countRecordsInBucket = count($analyticBucket);
            $midRecordIndex = (ceil($countRecordsInBucket / 2)) - 1;

            //find the item in the middle of this bucket
            if ($countRecordsInBucket % 2 == 0) {
                $bucketMedian = ($analyticBucket[$midRecordIndex] + $analyticBucket[$midRecordIndex + 1]) / 2;
            } else {
                $bucketMedian = $analyticBucket[$midRecordIndex];
            }

            $analyticsStatRecords[$ind]->median = $bucketMedian;
            $ind++;
        }

        return $analyticsStatRecords;

    }

    /**
     * @return array
     */
    public static function list()
    {
        $propertyAnalytics = self::with([
            "property" => function ($query) {
                $query->select("guid");
            },
            "analyticType" => function ($query) {
                $query->select("name");
            }])->get();

        return $propertyAnalytics->toArray();
    }

    /**
     * @param $propGuid
     * @param $analyticName
     */
    public static function deleteByPropGuidAndAnalyticName($propGuid, $analyticName)
    {

        $propertyAnalytic = Property::whereHas("property", function ($query) use ($propGuid) {
            $query->where('guid', $propGuid);
        })
            ->whereHas("property", function ($query) use ($analyticName) {
                $query->where('name', $analyticName);
            })
            ->first();
        $propertyAnalytic->delete();
    }

    /**
     * @param $propGuid
     * @param $analyticTypeName
     * @param $value
     * @return array
     */
    public static function assignAnalyticTypeValueToProperty($propGuid, $analyticTypeName, $value)
    {

        if ($propGuid === null) {
            return ["status" => "error", "message" => "passing an prop_guid key value is mandatory"];
        }

        if ($analyticTypeName === null) {
            return ["status" => "error", "message" => "passing an analytic_name key value is mandatory"];
        }

        if ($value === null) {
            return ["status" => "error", "message" => "passing an value for analytic '$analyticTypeName' is mandatory ('value'=>some_value)"];
        }

        $analyticType = AnalyticType::where("name", $analyticTypeName)
            ->first();

        if ($analyticType == null) {
            return ["status" => "error", "message" => "analytic '$analyticTypeName' not found in our database!"];

        }

        $prop = Property::where("guid", $propGuid)
            ->select("id")
            ->first();

        if ($prop == null) {
            return ["status" => "error", "message" => "property with guid '$propGuid' not found in our database!"];

        }


        if ($analyticType->is_numeric) {
            if (!is_numeric($value)) {
                return ["status" => "error", "message" => "analytic '$analyticTypeName' should be numeric ('$value' given)!"];
            }
        } else {
            if (is_numeric($value)) {
                return ["status" => "error", "message" => "analytic '$analyticTypeName' should be contain letter ('$value' given)!"];
            }
        }

        $propertyAnalytic = PropertyAnalytic::whereHas("property", function ($query) use ($propGuid) {
            $query->where('guid', $propGuid);
        })
            ->whereHas("analyticType", function ($query) use ($analyticTypeName) {
                $query->where('name', $analyticTypeName);
            })
            ->first();

        //dd($propertyAnalytic->toArray());
        if ($propertyAnalytic != null) {
            $propertyAnalytic->value = $value;
            $propertyAnalytic->save();
            return ["status" => "success", "message" => "for property '$propGuid' existing analytic '$analyticTypeName' is assigned a value of '$value'!"];
        } else {

            $propId = $prop->id;


            $analyticTypeId = $analyticType->id;

            $PropertyAnalytic = new PropertyAnalytic();
            $propertyAnalytic->property_id = $propId;
            $propertyAnalytic->analytic_type_id = $analyticTypeId;
            $propertyAnalytic->value = $value;
            $propertyAnalytic->save();

            return ["status" => "success", "message" => "for property '$propGuid' new analytic '$analyticTypeName' is assigned a value of '$value'!"];
        }


    }


}
