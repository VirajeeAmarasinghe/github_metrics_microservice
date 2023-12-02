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
        $response = Http::get("https://api.github.com/repos/$username/$repoName/commits");

        $returnArray = [];

        // Check if the request was successful
        if ($response->successful()) {
            // Parse the response to get commit count
            $commits = $response->json();

            foreach($commits as $commit){
                $isUserNameAlreadyExists = false;
                foreach($returnArray as &$userCount){
                    if($commit['commit']['committer']['name'] === $userCount['username']){
                        $isUserNameAlreadyExists = true;
                        $userCount['commit_frequency'] = $userCount['commit_frequency'] + 1;
                        break;
                    }
                }
                if(!$isUserNameAlreadyExists){
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

    public function getCodeReviewInvolvement($username)
    {
        // Logic to calculate code review involvement for the specified user
        // Example: Query the CodeReview model for participation in reviews
    }

    public function getIssueResolutionTime($username)
    {
        // Logic to calculate issue resolution time for the specified user
        // Example: Query the Issue model for average resolution time
    }

    public function getCodeChurn($username)
    {
        // Logic to calculate code churn/change rate for the specified user
        // Example: Query the CodeChange model for changes made by the user
    }
}
