<?php

namespace App\Console\Commands\TestUtils;

use App\AnalyticType;
use App\Property;
use App\PropertyAnalytic;
use App\Services\AnalyticStatistics;

use Illuminate\Console\Command;
use app\Console\Commands\TestUtils\InputMock;
use app\Console\Commands\TestUtils\OutputMock;


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
    protected $description = 'This Command is used as a utility to directly change WP websites data through direct database connection (not through Woo).';

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
