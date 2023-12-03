<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GitHubMetricsService;

class GitHubMetricsController extends Controller
{
    protected $gitHubMetricsService;

    public function __construct(GitHubMetricsService $gitHubMetricsService)
    {
        $this->gitHubMetricsService = $gitHubMetricsService;
    }

    public function getCommits($username)
    {
        return $this->gitHubMetricsService->getCommits($username);
    }

    public function getPullRequests($username)
    {
        return $this->gitHubMetricsService->getPullRequests($username);
    }

    public function getIssuesResolved($username)
    {
        return $this->gitHubMetricsService->getIssuesResolved($username);
    }

    public function getCommitFrequency($username, $repoName)
    {
        return $this->gitHubMetricsService->getCommitFrequency($username, $repoName);
    }

    public function getCodeReviewInvolvement($username, $repoName)
    {
        return $this->gitHubMetricsService->getCodeReviewInvolvement($username, $repoName);
    }

    public function getIssueResolutionTime($username, $repoName)
    {
        return $this->gitHubMetricsService->getIssueResolutionTime($username, $repoName);
    }

    public function getCodeChurn($username, $repoName)
    {
        return $this->gitHubMetricsService->getCodeChurn($username, $repoName);
    }
}
