<?php

namespace App\Http\Controllers;

use App\PropertyAnalytic;
use App\Services\AnalyticStatistics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyAnalyticStatsController extends Controller
{

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getStatistics(Request $request, $region)
    {

        $commaSeparatedRegionName = $region;//$request->input("region");
        $validRegionFormat = preg_match("/^\w+(,\w+){0,2}$/", $commaSeparatedRegionName);
        if (!$validRegionFormat) {
            $result = ["status" => "error", "message" => "wrong data entered for region ($commaSeparatedRegionName) the right format is [country[,state[,suburb]]"];
        } else {
            $result = AnalyticStatistics::getStats($commaSeparatedRegionName);
        }
        return new JsonResponse($result);

    }


}
