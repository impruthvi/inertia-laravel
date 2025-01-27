<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

uses(RefreshDatabase::class);

describe('get_ability function', function () {

    function mockRequest(?string $routeName)
    {
        $route = mock(Route::class)->shouldReceive('getName')->andReturn($routeName)->getMock();

        $request = mock(Request::class)->shouldReceive('route')->andReturn($route)->getMock();

        // Override the global helper for `request()`
        app()->singleton('request', fn () => $request);
    }

    it('returns empty string if route is null', function () {
        mockRequest(null);

        expect(get_ability('read'))->toBe('');
    });

    it('returns empty string if route name is null', function () {
        mockRequest(null);

        expect(get_ability('read'))->toBe('');
    });

    it('returns access prefix with first segment if route name has one part', function () {
        mockRequest('dashboard');

        expect(get_ability('read'))->toBe('read_dashboard');
    });

    it('returns access prefix with first segment if route name has two parts', function () {
        mockRequest('user.profile');

        expect(get_ability('update'))->toBe('update_user');
    });

    it('returns access prefix with second segment if route name has three parts', function () {
        mockRequest('admin.user.create');

        expect(get_ability('create'))->toBe('create_user');
    });

    it('returns empty string for route names with more than three parts', function () {
        mockRequest('admin.user.settings.advanced');

        expect(get_ability('manage'))->toBe('');
    });

    it('returns an empty string if request()->route() returns null', function () {
        // Mock a Request instance where the route() method returns null
        $request = mock(Request::class)
            ->shouldReceive('route')
            ->andReturn(null)
            ->getMock();

        // Override the global helper for `request()`
        app()->singleton('request', fn () => $request);

        // Call the function and assert it returns an empty string
        expect(get_ability('read'))->toBe('');
    });
});
