<?php

namespace App\Exceptions;

use App\Services\UserLayoutSettingsService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }


    /**
     * Prepare exception for rendering.
     *
     * @param  \Throwable  $e
     * @return \Throwable
     */
    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (!app()->environment(['local', 'testing']) && in_array($response->status(), [500, 503, 404, 403])) {

            $share_data = [];
            $UserLayoutSettingsService = new UserLayoutSettingsService();
            $layout_default_settings = $UserLayoutSettingsService->getUserLayoutSettings();
            $share_data = array_merge($share_data, $layout_default_settings);

            return Inertia::render('errors/ErrorPage', ['status' => $response->status()] + $share_data)
                ->toResponse($request)
                ->setStatusCode($response->status());

        } else if ($response->status() === 419) {
            return back()->with([
                'message' => 'The page expired, please try again.',
            ]);
        }

        return $response;
    }

}
