<?php

namespace App\Console\Commands\TestUtils;

use App\AnalyticType;
use App\Property;
use App\PropertyAnalytic;
use App\Services\AnalyticStatistics;

use Illuminate\Console\Command;
use app\Console\Commands\TestUtils\InputMock;
use app\Console\Commands\TestUtils\OutputMock;

/*
 * this class is used for data updates and also visually checking the result of queries we require for development as well as testing of this application
 *  with simple console commands
 */
class DbQueryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * general usage:
     * php artisan DB:Q  "(json object of all required parameters for this action with two mandatory ones 'action': (entity action)  and ' subject' : (entity to act on))"
     *
     * Products:
     * php artisan DB:Q  "{'action': 'stats', 'region' : 'AU,NSW'}"
     * php artisan DB:Q  "{'action': 'list', 'subject': 'Property'}"
     * php artisan DB:Q  "{'action': 'list', 'subject': 'AnalyticType'}"
     * php artisan DB:Q  "{'action': 'list', 'subject': 'PropertyAnalytic'}"
     * php artisan DB:Q  "{'action': 'update', 'subject': 'Property', 'guid' : '1', 'country': 'Au', 'state': 'VIC', 'suburb': 'cbd'}"
     * php artisan DB:Q  "{'action': 'update', 'subject': 'AnalyticType', 'name' : 'land_tax', 'units': 'AUD', 'is_numeric': 1, 'num_decimal_places': 2}"
     * php artisan DB:Q  "{'action': 'update', 'subject': 'PropertyAnalytic', 'prop_guid' : '1', 'analytic_name': 'Price', 'value': 650000}"
     * php artisan DB:Q  "{'action': 'remove', 'subject': 'Property', 'guid' : '1'}"
     *  parameters
     *
     * @var string
     */
    protected $signature = 'DB:Q
        {parameters}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is used as a utility to directly add\change data for properties and analytic types as well as assigning new analytic values to properties.
    you can also calculate statistics on properties here. Please use comment on the DbQueryCommands for typical usage syntax.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $paramsStr = $this->argument('parameters');
        //prepare for real json
        $paramsStr = str_replace("'", "\"", $paramsStr);
        $parameters = json_decode($paramsStr, true);

        $subject = (!isset($parameters['subject']) ? "none" : $parameters['subject']);
        $action = (!isset($parameters['action']) ? "none" : $parameters['action']);
        $commaSeparatedRegionName = (!isset($parameters['region']) ? "none" : $parameters['region']);

        switch ($subject) {
            case "none":
                switch ($action) {
                    case "stats":
                        $stats = AnalyticStatistics::getStats($commaSeparatedRegionName);
                        dd($stats);
                        break;
                    case "flush":

                        break;
                }
            case "Property":
                switch ($action) {
                    case "list":
                        $props = Property::list();
                        dd($props);
                        break;
                    case "remove":
                        $guid = (!isset($parameters['guid']) ? "none" : $parameters['guid']);
                        Property::deleteByGuid($guid);
                        break;
                    case "flush":
                        //todo: write delete all code here
                        break;
                    case "update":
                        $guid = (!isset($parameters['guid']) ? null : $parameters['guid']);
                        $country = (!isset($parameters['country']) ? null : $parameters['country']);
                        $state = (!isset($parameters['state']) ? null : $parameters['state']);
                        $suburb = (!isset($parameters['suburb']) ? null : $parameters['suburb']);

                        $result=Property::updateAs($guid, $country, $state, $suburb);
                        dd($result);
                        break;
                }
                break;
            case "AnalyticType":
                switch ($action) {
                    case "list":
                        $props = AnalyticType::list();
                        dd($props);
                        break;
                    case "remove":
                        $name = (!isset($parameters['name']) ? "none" : $parameters['name']);
                        AnalyticType::deleteByName($name);
                        break;
                    case "flush":
                        //todo: write delete all code here
                        break;
                    case "update":
                        $name = (!isset($parameters['name']) ? null : $parameters['name']);
                        $units = (!isset($parameters['units']) ? null : $parameters['units']);
                        $isNumeric = (!isset($parameters['is_numeric']) ? null : $parameters['is_numeric']);
                        $numDecimalPlaces = (!isset($parameters['num_decimal_places']) ? null : $parameters['num_decimal_places']);

                        $result=AnalyticType::updateAs($name, $units, $isNumeric, $numDecimalPlaces);
                        dd($result);
                        break;
                }
                break;
                break;
            case "PropertyAnalytic":
                switch ($action) {
                    case "list":
                        $propsAnalytics = PropertyAnalytic::list();
                        dd($propsAnalytics);
                    break;
                    case "update":
                        $analyticTypeName = (!isset($parameters['analytic_name']) ? null : $parameters['analytic_name']);
                        $propertyGuid = (!isset($parameters['prop_guid']) ? null : $parameters['prop_guid']);
                        $value = (!isset($parameters['value']) ? null : $parameters['value']);

                        $result = PropertyAnalytic::assignAnalyticTypeValueToProperty($propertyGuid, $analyticTypeName, $value);
                        dd($result);
                        break;
                }
                break;


        }

        echo "\n\nCommand completed for action: $action on subject: $subject";

    }


    /*
     * this is needed when trying to call command class directly for debugging purposes (in unit tests)
     */
    public function prepareCommandForTesting($optionsKeyValuePairs)
    {

        $this->input = new InputMock($optionsKeyValuePairs);
        $this->output = new OutputMock();
        $this->launchedFromUnitTest = true;

    }


}
