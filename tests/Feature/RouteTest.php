<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteTest extends TestCase
{

    use RefreshDatabase;

    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->user = User::where('email', 'rifat@email.com')->first();

        // dd(DB::connection()->getDatabaseName());

    }

    public function test_public_get_routes_works(): void
    {

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return in_array('GET', $route->methods()) &&
                !str_contains($route->uri, '{') &&
                !$this->routeUsesAuthMiddleware($route) &&
                !$this->isExcludedRoute($route->uri);
        });

        $routes->each(function ($route) {
            $uri = $route->uri();
            $response = $this->get($uri);

            if ($response->getStatusCode() !== 200) {
                dd([
                    'uri' => $uri,
                    'status' => $response->getStatusCode(),
                ]);
            }

            $response->assertStatus(200, "Failed for URI: $uri. Response content: " . $response->getContent());
        });
    }

    public function test_dashboard_get_routes(): void
    {

        $this->assertDatabaseHas('site_settings', ['id' => 1]);

        // check if db have user
        $this->assertDatabaseHas('users', ['email' => "rifat@email.com"]);

        $user = User::where('email', 'rifat@email.com')->first();
        $this->assertNotNull($user);

        //        $this->actingAs($user);

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return in_array('GET', $route->methods()) &&
                !str_contains($route->uri, '{') &&
                $this->routeUsesAuthMiddleware($route) &&
                !$this->isExcludedRoute($route->uri);
        });

        $routes->each(function ($route) {
            $uri = $route->uri();
            $response = $this->actingAs($this->user)->get($uri);

            if ($response->getStatusCode() !== 200) {
                dd([
                    'uri' => $uri,
                    'status' => $response->getStatusCode(),
                ]);
            }

            $response->assertStatus(200, "Failed for URI: $uri. Response content: " . $response->getContent());
        });
    }

    public function test_is_dashboard_protected_with_auth(): void
    {

        $this->assertDatabaseHas('site_settings', ['id' => 1]);

        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return in_array('GET', $route->methods()) &&
                !str_contains($route->uri, '{') &&
                $this->routeUsesAuthMiddleware($route) &&
                !$this->isExcludedRoute($route->uri);
        });

        $routes->each(function ($route) {
            $uri = $route->uri();
            $response = $this->get($uri);

            if ($response->getStatusCode() !== 302) {
                dd([
                    'uri' => $uri,
                    'status' => $response->getStatusCode(),
                ]);
            }

            $response->assertStatus(302);
        });
    }

    protected function routeUsesAuthMiddleware($route): bool
    {
        $middlewares = $route->gatherMiddleware();
        return in_array('auth', $middlewares) || collect($middlewares)->contains(function ($middleware) {
            return str_contains($middleware, 'auth');
        });
    }

    protected function isExcludedRoute($uri): bool
    {
        $excludedRoutes = [
            'livewire/livewire.min.js',
            'livewire/livewire.min.js.map',
            '_ignition/health-check',
            'clear',
            'storage-link',
        ];

        return in_array($uri, $excludedRoutes);
    }
}
