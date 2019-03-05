<?php

namespace Integration\Http\Controllers;

use Tests\BaseTestCase;

class ApplicationControllerTest extends BaseTestCase
{
    /**
     * @test
     * @covers \Oloid\Http\Controllers\ApplicationController
     */
    public function it_should_get_info_about_the_app_name()
    {
        $this->getJson('workshop/api/v1/info')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'appName' => 'testApp'
                ]
            ]);
    }
}
