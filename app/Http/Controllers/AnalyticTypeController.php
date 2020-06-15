<?php

namespace App\Http\Controllers;

use App\AnalyticType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //todo: complete if later required
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //todo: complete if later required
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name=$request->input("name");
        $units=$request->input("units");
        $isNumeric=$request->input("is_numeric");
        $numDecimalPlaces=$request->input("num_decimal_places");
        $result=AnalyticType::updateAs($name, $units, $isNumeric, $numDecimalPlaces);

        return new JsonResponse($result);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\AnalyticType $analyticType
     * @return \Illuminate\Http\Response
     */
    public function show(AnalyticType $analyticType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\AnalyticType $analyticType
     * @return \Illuminate\Http\Response
     */
    public function edit(AnalyticType $analyticType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AnalyticType $analyticType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AnalyticType $analyticType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\AnalyticType $analyticType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AnalyticType $analyticType)
    {
        //
    }
}
