Route::group(['middleware' => ['auth', 'employe']], function () {
Route::get('aa/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('employe.dashboard');
Route::get('aa/dashboardwwww', [App\Http\Controllers\HomeController::class, 'indexwww'])->name('employe.dashboardeee');
});

Route::group(['middleware' => ['auth', 'employer']], function () {
Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index2'])->name('employer.dashboard');
});



<!--namespace App\Http\Middleware;  -->

class RedirectIfAuthenticated
{
/**
* Handle an incoming request.
*
* @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
*/
public function handle(Request $request, Closure $next, string ...$guards): Response
{
$guards = empty($guards) ? [null] : $guards;

foreach ($guards as $guard) {
if (Auth::guard($guard)->check()) {



if (Auth::guard($guard)->check() && Auth::user()->role == 1) {
return redirect()->route("employe.dashboard");
} elseif (Auth::guard($guard)->check() && Auth::user()->role == 2) {
return redirect()->route("employer.dashboard");
}
// return redirect(RouteServiceProvider::HOME);
}
}

return $next($request);
}
}

<!--  -->