<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SimpleResourceController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\CvController;
use App\Http\Controllers\Public\PortfolioController;
use App\Http\Controllers\Public\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortfolioController::class, 'home'])->name('home');
Route::get('/about', [PortfolioController::class, 'about'])->name('about');
Route::get('/projects', [PortfolioController::class, 'projects'])->name('projects.index');
Route::get('/projects/{project:slug}', [PortfolioController::class, 'project'])->name('projects.show');
Route::get('/experience', [PortfolioController::class, 'experience'])->name('experience');
Route::get('/certificates', [PortfolioController::class, 'certificates'])->name('certificates');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/cv', [CvController::class, 'show'])->name('cv.show');
Route::get('/cv/download', [CvController::class, 'download'])->name('cv.download');
Route::get('/chatbot/suggestions', [ChatbotController::class, 'suggestions'])->middleware('throttle:30,1')->name('chatbot.suggestions');
Route::post('/chatbot/message', [ChatbotController::class, 'message'])->middleware('throttle:8,1')->name('chatbot.message');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('projects', ProjectController::class)->except('show');
    Route::delete('projects/{project}/images/{projectImage}', [ProjectController::class, 'destroyImage'])->name('projects.images.destroy');

    Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::patch('messages/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.status');
    Route::delete('messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    $resources = [
        'project-categories', 'technologies', 'skill-categories', 'skills', 'experiences',
        'educations', 'certificate-categories', 'certificates', 'chatbot-faqs',
    ];

    foreach ($resources as $resource) {
        Route::get($resource, [SimpleResourceController::class, 'index'])->defaults('resource', $resource)->name("{$resource}.index");
        Route::get("{$resource}/create", [SimpleResourceController::class, 'create'])->defaults('resource', $resource)->name("{$resource}.create");
        Route::post($resource, [SimpleResourceController::class, 'store'])->defaults('resource', $resource)->name("{$resource}.store");
        Route::get("{$resource}/{record}/edit", [SimpleResourceController::class, 'edit'])->defaults('resource', $resource)->name("{$resource}.edit");
        Route::put("{$resource}/{record}", [SimpleResourceController::class, 'update'])->defaults('resource', $resource)->name("{$resource}.update");
        Route::delete("{$resource}/{record}", [SimpleResourceController::class, 'destroy'])->defaults('resource', $resource)->name("{$resource}.destroy");
    }
});

require __DIR__.'/auth.php';
