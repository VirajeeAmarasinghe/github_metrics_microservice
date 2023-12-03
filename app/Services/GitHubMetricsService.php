<?php

namespace App\Services;

use App\Models\Commit;
use App\Models\CodeReview;
use App\Models\Issue;
use App\Models\CodeChange;
use Illuminate\Support\Facades\Http;

class GitHubMetricsService
{
    public function getCommitFrequency($username, $repoName)
    {
        // Logic to calculate commit frequency for the specified user
        // Make a GET request to the GitHub API to fetch commit data for the specified repository
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('BEARER_TOKEN'),
        ])->get("https://api.github.com/repos/$username/$repoName/commits");

        $returnArray = [];

        // Check if the request was successful
        if ($response->successful()) {
            // Parse the response to get commit count
            $commits = $response->json();

            foreach ($commits as $commit) {
                $isUserNameAlreadyExists = false;
                foreach ($returnArray as &$userCount) {
                    if ($commit['commit']['committer']['name'] === $userCount['username']) {
                        $isUserNameAlreadyExists = true;
                        $userCount['commit_frequency'] = $userCount['commit_frequency'] + 1;
                        break;
                    }
                }
                if (!$isUserNameAlreadyExists) {
                    $record = [];
                    $record['username'] = $commit['commit']['committer']['name'];
                    $record['commit_frequency'] = 1;
                    $record['repo_name'] = $repoName;
                    array_push($returnArray, $record);
                }
            }

            return $returnArray;
        }

        return [];
    }

    public function getCodeReviewInvolvement($username, $repoName)
    {
        // Logic to calculate code review involvement for the specified user
        // for each pull request, it retrieves the associated reviews and calculates the total count of reviews. 
        // This count can be considered as code review involvement.
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('BEARER_TOKEN'),
        ])->get("https://api.github.com/repos/$username/$repoName/pulls");

        $returnArray = [];

        if ($response->successful()) {
            $pullRequests = $response->json();

            foreach ($pullRequests as $pullRequest) {
                $reviewsResponse = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('BEARER_TOKEN'),
                ])->get($pullRequest['url'] . '/reviews');
                if ($reviewsResponse->successful()) {
                    $reviews = $reviewsResponse->json();
                    $totalReviews = count($reviews);
                    $record = [];
                    $record['username'] = $pullRequest['user']['login'];
                    $record['repo_name'] = $repoName;
                    $record['pr_url'] = $pullRequest['url'];
                    $record['review_count'] = $totalReviews;

                    array_push($returnArray, $record);
                }
            }

            return $returnArray;
        }

        return 0;
    }

    public function getIssueResolutionTime($username, $repoName)
    {
        // Logic to calculate issue resolution time for the specified user
        // This code fetches closed issues for a specific repository owned by the user. 
        // For each closed issue, it calculates the resolution time and aggregates this data for each user 
        // involved in those issues. Finally, it calculates the average resolution time for each user.
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('BEARER_TOKEN'),
        ])->get("https://api.github.com/repos/$username/$repoName/issues?state=closed");

        if ($response->successful()) {
            $issues = $response->json();

            $usersResolutionTime = [];

            foreach ($issues as $issue) {
                $user = $issue['user']['login'];

                $createdAt = new \DateTime($issue['created_at']);
                $closedAt = new \DateTime($issue['closed_at']);
                $resolutionTime = $closedAt->diff($createdAt)->days;

                if (!isset($usersResolutionTime[$user]['totalTime'])) {
                    $usersResolutionTime[$user]['totalTime'] = 0;
                    $usersResolutionTime[$user]['issueCount'] = 0;
                }

                $usersResolutionTime[$user]['totalTime'] += $resolutionTime;
                $usersResolutionTime[$user]['issueCount']++;
            }

            $userAverages = [];

            foreach ($usersResolutionTime as $user => $data) {
                $average = $data['issueCount'] > 0 ? $data['totalTime'] / $data['issueCount'] : 0;
                $record = [];
                $record['username'] = $user;
                $record['repo_name'] = $repoName;
                $record['average'] = $average;
                array_push($userAverages, $record);
            }

            return $userAverages;
        }

        return [];
    }

    public function getCodeChurn($username, $repoName)
    {
        // Logic to calculate code churn/change rate for the specified user
        // This code fetches contributor statistics from the GitHub API for a specific repository owned by the user. 
        // It then iterates through the contributor stats to calculate the churn for each contributor in the repository.
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('BEARER_TOKEN'),
        ])->get("https://api.github.com/repos/$username/$repoName/stats/contributors");

        if ($response->successful()) {
            $contributorsStats = $response->json();

            $contributorsChurn = [];

            foreach ($contributorsStats as $contributor) {
                $isUserAlreadyIn = false;
                foreach ($contributorsChurn as &$contribChrun) {
                    $contributorUsername = $contributor['author']['login'];
                    if ($contribChrun['username'] == $contributorUsername) {
                        $isUserAlreadyIn = true;
                        $additions = 0;
                        $deletions = 0;

                        foreach ($contributor['weeks'] as $week) {
                            $additions += $week['a'];
                            $deletions += $week['d'];
                        }

                        $churn = $additions + $deletions;
                        $contribChrun['churn'] = $churn;
                        break;
                    }
                }
                if (!$isUserAlreadyIn) {
                    $record = [];
                    $record['username'] = $contributor['author']['login'];
                    $record['repo_name'] = $repoName;

                    $additions = 0;
                    $deletions = 0;

                    foreach ($contributor['weeks'] as $week) {
                        $additions += $week['a'];
                        $deletions += $week['d'];
                    }

                    $churn = $additions + $deletions;

                    $record['churn'] = $churn;

                    array_push($contributorsChurn, $record);
                }
            }

            return $contributorsChurn;
        }

        return [];
    }
}
