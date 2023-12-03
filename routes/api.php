<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GitHubMetricsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/metrics/{username}/{repoName}/commit-frequency', [GitHubMetricsController::class, 'getCommitFrequency']);
Route::get('/metrics/{username}/{repoName}/code-review-involvement', [GitHubMetricsController::class, 'getCodeReviewInvolvement']);
Route::get('/metrics/{username}/{repoName}/issue-resolution-time', [GitHubMetricsController::class, 'getIssueResolutionTime']);
Route::get('/metrics/{username}/{repoName}/code-churn', [GitHubMetricsController::class, 'getCodeChurn']);
Route::get('/metrics/VirajeeAmarasinghe/commits', [GitHubMetricsController::class, 'getCommits']);
Route::get('/metrics/VirajeeAmarasinghe/pull-requests', [GitHubMetricsController::class, 'getPullRequests']);
Route::get('/metrics/VirajeeAmarasinghe/issues-resolved', [GitHubMetricsController::class, 'getIssuesResolved']);
Route::get('/metrics/VirajeeAmarasinghe/code-churn', [GitHubMetricsController::class, 'getCodeChurn']);
