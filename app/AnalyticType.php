<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalyticType extends Model
{
    protected $table = 'analytic_types';

    public static function list()
    {
        return self::all()->toArray();
    }

    public static function deleteByName($name)
    {
        $property = Property::where("name", $name)
            ->first();
        $property->delete();
    }

    public static function updateAs($name, $units, $isNumeric, $numDecimalPlaces)
    {
        $analyticType = self::where("name", $name)
            ->first();

        if ($analyticType == null) {
            $analyticType = new AnalyticType();
        }

        $analyticType->name = $name;
        $analyticType->units = $units;
        $analyticType->isNumeric = $isNumeric;
        $analyticType->numDecimalPlaces = $numDecimalPlaces;

        $analyticType->save();

        return ["status" => "success", "message" => "AnalyticType with name '$name' updated!"];
    }
}
