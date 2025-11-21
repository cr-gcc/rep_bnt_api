<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
  /**
   * El path predeterminado de redirección después del login (puedes cambiarlo si quieres).
   *
   * @var string
   */
  public const HOME = '/dashboard';

  /**
   * Define tus bindings de modelos, filtros de patrones, etc.
   */
  public function boot(): void
  {
    $this->routes(function () {
      Route::prefix('api')
        ->middleware('api')
        ->group(base_path('routes/api.php'));

      Route::middleware('web')
        ->group(base_path('routes/web.php'));
    });
  }
}
