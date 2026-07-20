<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Full Catalog based on your Product Setup
        $catalog = [
            ['id' => 1, 'category' => 'Foundations', 'title' => 'Financial Markets Basics', 'modules' => 5, 'price' => 'Free', 'path' => 'Free'],
            ['id' => 2, 'category' => 'Asset Analysis', 'title' => 'Equity Analysis & Investments', 'modules' => 6, 'price' => '$250', 'path' => 'Foundation'],
            ['id' => 3, 'category' => 'Trading System', 'title' => 'LMRSS Day Trading System', 'modules' => 8, 'price' => '$1,000', 'path' => 'Trader'],
            ['id' => 4, 'category' => 'Live', 'title' => 'OHC Trade Room Sessions', 'modules' => null, 'price' => 'Ultimate', 'path' => 'Trader'],
            ['id' => 5, 'category' => 'Asset Analysis', 'title' => 'Fixed Income Analysis', 'modules' => 6, 'price' => '$250', 'path' => 'Foundation'],
            ['id' => 6, 'category' => 'Advanced Strategy', 'title' => 'Derivatives 101', 'modules' => 8, 'price' => '$1,000', 'path' => 'Investor'],
            ['id' => 7, 'category' => 'Advanced Strategy', 'title' => 'Advanced Derivatives', 'modules' => 'Ultimate Path', 'price' => 'Ultimate', 'path' => 'Ultimate'],
            ['id' => 8, 'category' => 'Coaching', 'title' => 'Group Mentorship', 'modules' => 'Ultimate Path', 'price' => 'Ultimate', 'path' => 'Ultimate'],
        ];

        // Logic: Determine if owned and sort (Owned first)
        $courses = collect($catalog)->map(function($course) use ($user) {
            $course['is_owned'] = $user->hasAccessTo($course['path']) || $user->hasAccessTo($course['title']);
            $course['progress'] = $course['is_owned'] ? rand(10, 100) : 0; // Mock progress
            return $course;
        })->sortByDesc('is_owned')->values();

        return view('courses', compact('courses'));
    }
}
