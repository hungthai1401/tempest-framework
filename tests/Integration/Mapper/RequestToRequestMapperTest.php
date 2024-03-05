<?php

declare(strict_types=1);

namespace Tests\Tempest\Integration\Mapper;

use App\Modules\Posts\PostRequest;
use Tempest\Http\GenericRequest;
use Tempest\Http\Request;
use Tempest\Mapper\PsrRequestToRequestMapper;
use Tempest\ORM\Exceptions\MissingValuesException;
use function Tempest\request;
use Tempest\Testing\IntegrationTest;

/**
 * @internal
 * @small
 */
class RequestToRequestMapperTest extends IntegrationTest
{
    public function test_can_map()
    {
        $mapper = new PsrRequestToRequestMapper();

        $this->assertTrue($mapper->canMap(from: request('/'), to: PostRequest::class));
        $this->assertFalse($mapper->canMap(from: request('/'), to: self::class));
    }

    public function test_map_with()
    {
        $mapper = new PsrRequestToRequestMapper();

        $request = $mapper->map(
            from: request('/', ['title' => 'a', 'text' => 'b']),
            to: PostRequest::class,
        );

        $this->assertInstanceOf(PostRequest::class, $request);
        $this->assertEquals('a', $request->title);
        $this->assertEquals('b', $request->text);
    }

    public function test_map_with_with_missing_data()
    {
        $this->expectException(MissingValuesException::class);

        $mapper = new PsrRequestToRequestMapper();

        try {
            $mapper->map(
                from: request('/'),
                to: PostRequest::class,
            );
        } catch (MissingValuesException $exception) {
            $this->assertStringContainsString('title', $exception->getMessage());
            $this->assertStringContainsString('text', $exception->getMessage());

            throw $exception;
        }
    }

    public function test_generic_request_is_used_when_interface_is_passed()
    {
        $mapper = new PsrRequestToRequestMapper();

        $request = $mapper->map(
            from: request('/'),
            to: Request::class,
        );

        $this->assertInstanceOf(GenericRequest::class, $request);
    }
}