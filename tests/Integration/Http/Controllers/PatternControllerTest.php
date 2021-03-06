<?php

namespace Integration\Http\Controllers;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\JsonResponse;
use Tests\BaseTestCase;
use Tests\Traits\TestStubs;

class PatternControllerTest extends BaseTestCase
{
    use TestStubs;

    /**
     * @var string
     */
    private $name = 'atoms.text.headline1';

    /**
     * @var string
     */
    private $description = 'That\'s a test';

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_create_a_new_pattern()
    {
        // arrange
        $data = [
            'name' => 'pages.testpage',
            'description' => 'This is a test pattern'
        ];

        $expectedJson = [
            'data' => [
                'name' => 'pages.testpage'
            ]
        ];

        // act
        /** @var TestResponse $response */
        $response = $this->post('workshop/api/v1/pattern', $data);

        // assert
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $response->assertJson($expectedJson);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_be_an_invalide_request_name_missing()
    {
        // arrange
        $data = [
            'description' => 'This is a test pattern'
        ];

        // act
        /** @var TestResponse $response */
        $response = $this->postJson('workshop/api/v1/pattern', $data);

        // assert
        $this->assertEquals(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_not_create_pattern_if_it_already_exists()
    {
        // arrange
        $this->preparePatternStub();

        // act
        $data = [
            'name' => 'atoms.text.headline1',
            'description' => 'Our h1 headline'
        ];

        /** @var TestResponse $response */
        $response = $this->postJson('workshop/api/v1/pattern', $data);

        // assert
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_load_all_pattern_information()
    {
        // arrange
        $this->preparePatternStub();

        // act
        /** @var TestResponse $response */
        $response = $this->getJson('workshop/api/v1/pattern/preview/atoms.text.headline1');

        // assert
        $expected = [
            'data' => [
                'name' => 'atoms.text.headline1',
                'type' => 'atoms',
                'description' => 'Our h1 for testing',
                'status' => 'review',
                'usage' => '@atoms(\'text.headline1\', [\'text\' => \'Testing\'])',
                'template' => "<h1>{{ \$text }}</h1>",
                'html' => "<h1>Testing</h1>",
                'sass' => "h1 {\n  color: red;\n}",
            ]
        ];
        $response->assertSuccessful();
        $response->assertJson($expected);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_return_404_if_a_pattern_does_not_exist()
    {
        // act
        /** @var TestResponse $response */
        $response = $this->getJson('workshop/api/v1/pattern/preview/atoms.not.existing');

        // assert
        $response->assertStatus(404);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_get_a_html_preview_of_a_pattern()
    {
        // arrange
        $this->preparePatternStub();

        // act
        /** @var TestResponse $response */
        $response = $this->get("/workshop/preview/{$this->name}");

        // assert
        $response->assertSuccessful(200);
        $response->assertViewIs('workshop::preview');
        $response->assertSee('Testing');
        $response->assertViewHas('preview', "<h1>Testing</h1>");
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_remove_a_pattern()
    {
        // arrange
        $this->preparePatternStub();

        // act
        /** @var TestResponse $response */
        $response = $this->deleteJson('workshop/api/v1/pattern/atoms.text.headline1');

        // assert
        $response->assertSuccessful();
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_get_404_error_if_nonexistend_pattern_is_deleted()
    {
        // arrange

        // act
        /** @var TestResponse $response */
        $response = $this->deleteJson('workshop/api/v1/pattern/not.existing.pattern');

        // assert
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_update_the_status_of_a_pattern()
    {
        // arrange
        $this->preparePatternStub();

        $data = [
            'status' => 'TESTED'
        ];

        // act
        /** @var TestResponse $response */
        $response = $this->putJson("workshop/api/v1/pattern/status/{$this->name}", $data);

        // assert
        $response->assertSuccessful();
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_change_the_description_of_a_pattern()
    {
        // arrange
        $this->preparePatternStub();

        $data = [
            'description' => 'A new description'
        ];

        $oldContent = file_get_contents("{$this->tempDir}/patterns/atoms/text/headline1.md");
        $this->assertContains('Our h1 for testing', $oldContent);

        // act
        /** @var TestResponse $response */
        $response = $this->putJson("workshop/api/v1/pattern/{$this->name}", $data);

        // assert
        $response->assertSuccessful();

        $newContent = file_get_contents("{$this->tempDir}/patterns/atoms/text/headline1.md");
        $this->assertContains('A new description', $newContent);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_not_change_the_description_if_request_input_is_missing()
    {
        // arrange
        $this->preparePatternStub();

        $data = [
            'name' => 'new.name.for.pattern'
        ];

        $oldContent = file_get_contents("{$this->tempDir}/patterns/atoms/text/headline1.md");
        $this->assertContains('Our h1 for testing', $oldContent);

        // act
        /** @var TestResponse $response */
        $response = $this->putJson("workshop/api/v1/pattern/{$this->name}", $data);

        // assert
        $response->assertSuccessful();

        $newContent = file_get_contents("{$this->tempDir}/patterns/new/name/for/pattern.md");
        $this->assertContains('Our h1 for testing', $newContent);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_not_rename_a_pattern_if_name_already_exists()
    {
        // arrange
        $this->preparePatternStub();

        // act
        $data = [
            'name' => 'atoms.text.headline2'
        ];

        /** @var TestResponse $response */
        $response = $this->putJson('workshop/api/v1/pattern/atoms.text.headline1', $data);

        // assert
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_check_that_a_pattern_exists()
    {
        // arrange
        $this->preparePatternStub();

        // act
        /** @var TestResponse $response */
        $response = $this->getJson("workshop/api/v1/pattern/exists/{$this->name}");

        // assert
        $response->assertSuccessful();
        $expectedJson = [
            'data' => [
                'exists' => true
            ]
        ];
        $response->assertJson($expectedJson);
    }

    /**
     * @test
     * @covers \Oloid\Http\Controllers\PatternController
     */
    public function it_should_check_that_a_pattern_does_not_exists()
    {
        // arrange
        $this->preparePatternStub();

        /** @var TestResponse $response */
        $response = $this->getJson("workshop/api/v1/pattern/exists/not.existing.pattern");

        // assert
        $response->assertSuccessful();
        $expectedJson = [
            'data' => [
                'exists' => false
            ]
        ];
        $response->assertJson($expectedJson);
    }
}
