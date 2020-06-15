<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyAnalyticStatsControllerTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function testCanGetStatsForSuburb()
    {
        //$response = $this->get('/');
        //$response->assertStatus(200);

        $region = 'Au,NSW,Ingleburn';
        //$this->json('GET', 'api/v1/get-stats', $region)
        $this->get( "http://localhost:8000/api/v1/stats/$region")
            ->assertStatus(200);
            /*->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ]
            ]);*/
    }

    /**
     *
     * @return void
     */
    public function testCanGetStatsForState()
    {
        $region = 'Au,NSW';
        $this->get( "http://localhost:8000/api/v1/stats/$region")
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'results'
            ]);

        //=>["name","analytic_type_id","countWithValue","minVal","maxVal","percentageWithValue","percentageWithoutValue","median"]

    }

    /**
     *
     * @return void
     */
    public function testCanGetStatsForCountry()
    {
        $region = 'Au';
        $this->get( "http://localhost:8000/api/v1/stats/$region")
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'results'
            ]);
    }

    //todo: write tests that exactly checks expected min max and median values for records currently in our sample data
}
