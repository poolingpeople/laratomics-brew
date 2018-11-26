<?php


namespace Unit;


use Exception;
use Illuminate\Filesystem\Filesystem;
use Laratomics\Tests\BaseTestCase;
use Laratomics\Tests\Traits\TestStubs;

class HelperMethodsTest extends BaseTestCase
{
    use TestStubs;

    /**
     * @test
     * @covers ::compile_blade_string
     */
    public function it_should_parse_a_php_template_to_html()
    {
        // arrange
        $template = '<h1>{{ $text }}</h1>';

        // act
        try {
            $html = compile_blade_string($template, ['text' => 'TEST']);
            $this->assertEquals('<h1>TEST</h1>', $html);
        } catch (Exception $e) {
            $this->fail();
        }
    }

    /**
     * @test
     * @covers ::pattern_path
     */
    public function it_should_return_the_pattern_base_path()
    {
        $this->preparePatternStub();

        // assert
        $expectedPatternPath = realpath(__DIR__ . '/../tmp/patterns');
        $this->assertEquals($expectedPatternPath, pattern_path());
    }

    /**
     * @test
     * @covers ::pattern_path
     */
    public function it_should_return_a_subpath_within_the_patttern_path()
    {
        // arrange
        $this->preparePatternStub();

        // assert
        $expectedPatternPath = realpath(__DIR__ . '/../tmp/patterns/atoms');
        $this->assertEquals($expectedPatternPath, pattern_path('atoms'));
    }

    /**
     * @test
     * @covers ::dir_is_empty
     */
    public function it_should_check_if_a_given_directory_is_empty()
    {
        // arrange
        $testDirectory = "{$this->tempDir}/empty/subdirectory";
        $fs = new Filesystem();
        $fs->makeDirectory($testDirectory, 0755, true);

        // assert
        $this->assertTrue(dir_is_empty($testDirectory));
    }

    /**
     * @test
     * @covers ::dir_is_empty
     */
    public function it_should_check_if_a_given_directory_is_not_empty()
    {
        // arrange
        $testDirectory = "{$this->tempDir}/empty/subdirectory";
        $fs = new Filesystem();
        $fs->makeDirectory($testDirectory, 0755, true);

        // assert
        $this->assertFalse(dir_is_empty("{$this->tempDir}/empty"));
    }

    /**
     * @test
     * @covers ::dir_is_empty
     */
    public function it_should_check_if_a_given_directory_contains_files()
    {
        // arrange
        $this->preparePatternStub();

        // assert
        $this->assertFalse(dir_is_empty("{$this->tempDir}/patterns"));
    }

    /**
     * @test
     * @covers ::dir_contains_any
     */
    public function it_should_check_that_a_directory_contains_any_files_of_a_given_extension()
    {
        // arrange
        $this->preparePatternStub();
        $directory = "{$this->tempDir}/patterns/atoms/text";

        // assert
        $this->assertTrue(dir_contains_any($directory, 'blade.php'));
    }

    /**
     * @test
     * @covers ::dir_contains_any
     */
    public function it_should_check_if_a_directory_not_contains_any_files_of_a_given_extension()
    {
        // arrange
        $this->preparePatternStub();
        $directory = "{$this->tempDir}/patterns/atoms/text";

        // assert
        $this->assertFalse(dir_contains_any($directory, 'txt'));
    }

    /**
     * @test
     * @covers ::dotted_path
     */
    public function it_should_convert_a_slash_separated_path_to_dotted_notation()
    {
        // arrange
        $path = '/var/some/test/path/';
        $expected = 'var.some.test.path';
        $this->assertEquals($expected, dotted_path($path));
    }

    /**
     * @test
     * @covers ::slash_path
     */
    public function it_should_convert_a_dot_separated_path_to_slash_notation()
    {
        // arrange
        $dottedPath = 'var.some.test.path';
        $expected = 'var/some/test/path';
        $this->assertEquals($expected, slash_path($dottedPath));
    }

    /**
     * @test
     * @covers ::parent_dir
     */
    public function it_should_return_the_directory_where_the_given_file_is_contained()
    {
        // arrange
        $file = '/some/path/to/a/file.txt';
        $expected = '/some/path/to/a';

        // assert
        $this->assertEquals($expected, parent_dir($file));
    }
}