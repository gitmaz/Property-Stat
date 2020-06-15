<?php

namespace App\Http\Controllers;

use App\PropertyAnalytic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyAnalyticController extends Controller
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
        $propGuid = $request->input("prop_guid");
        $analyticTypeName = $request->input("analytic_name");
        $value = $request->input("value");

        $result = PropertyAnalytic::assignAnalyticTypeValueToProperty($propGuid, $analyticTypeName, $value);

        return new JsonResponse($result);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\PropertyAnalytic $propertyAnalytic
     * @return \Illuminate\Http\Response
     */
    public function show(PropertyAnalytic $propertyAnalytic)
    {
        //todo: complete if later required
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\PropertyAnalytic $propertyAnalytic
     * @return \Illuminate\Http\Response
     */
    public function edit(PropertyAnalytic $propertyAnalytic)
    {
        //todo: complete if later required
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PropertyAnalytic $propertyAnalytic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PropertyAnalytic $propertyAnalytic)
    {
        //todo: complete if later required
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\PropertyAnalytic $propertyAnalytic
     * @return \Illuminate\Http\Response
     */
    public function destroy(PropertyAnalytic $propertyAnalytic)
    {
        //todo: complete if later required
    }
}
