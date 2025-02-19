<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you register web routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "web"
| middleware group. You can edit the default route and add new routes here.
|
*/

/**
 * Default Route (Home Page)
 *
 * The original route that rendered the "Welcome" component is commented out.
 * It passed along the following props:
 *  - 'canLogin': Indicates whether a login route exists.
 *  - 'canRegister': Indicates whether a register route exists.
 *  - 'laravelVersion': The current Laravel version.
 *  - 'phpVersion': The PHP version running on your server.
 *
 * This has been replaced so that when a user visits the root URL ('/'),
 * the Dashboard component is rendered by default.
 */

// Original Welcome route (commented out):
// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin'       => Route::has('login'),
//         'canRegister'    => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion'     => PHP_VERSION,
//     ]);
// });

// New default route rendering the Dashboard component:
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');



/*
 * Protected Routes (Require Authentication)
 *
 * Uncomment the following middleware group when you're ready to protect these
 * routes with authentication (e.g., using Sanctum and Jetstream's auth_session).
 */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/bmedle', function () {
        return Inertia::render('Bmedle');
    })->name('bmedle');
});

/**
 * Public Inertia Routes
 *
 * These routes do not require authentication and render the respective Vue 3
 * components using Inertia. Replace the component names with the corresponding
 * Vue components you have in your project.
 */
// Route::get('/bmedle', function () {
//     return Inertia::render('Bmedle');
// })->name('bmedle');

Route::get('/meddebate', function () {
    return Inertia::render('MedDebate');
})->name('meddebate');

Route::get('/ourteam', function () {
    return Inertia::render('OurTeam');
})->name('ourteam');

Route::get('/3dprinting', function () {
    return Inertia::render('3DPrinting');
})->name('3dprinting');

Route::get('/vrdev', function () {
    return Inertia::render('VRDev');
})->name('vrdev');

Route::get('/arduino', function () {
    return Inertia::render('Arduino');
})->name('arduino');

Route::get('/studentguides', function () {
    return Inertia::render('StudentGuides');
})->name('studentguides');

Route::get('/bmepaths', function () {
    return Inertia::render('BmePaths');
})->name('bmepaths');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');
