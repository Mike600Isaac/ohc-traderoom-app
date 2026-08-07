<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('entitlements');

        $catalog = [
            ['key' => 'basics', 'category' => 'Foundations', 'title' => 'Financial Markets Basics', 'modules' => 5, 'price' => 'Free', 'unlock_offer' => 'basics', 'included_in' => 'Foundation, Trader, Investor, Ultimate'],
            ['key' => 'equity', 'category' => 'Asset Analysis', 'title' => 'Equity Analysis & Investments', 'modules' => 6, 'price' => '$250', 'unlock_offer' => 'equity', 'included_in' => 'Foundation, Trader, Investor, Ultimate'],
            ['key' => 'fixed_income', 'category' => 'Asset Analysis', 'title' => 'Fixed Income Analysis & Investments', 'modules' => 6, 'price' => '$250', 'unlock_offer' => 'fixed_income', 'included_in' => 'Foundation, Investor, Ultimate'],
            ['key' => 'lmrss', 'category' => 'Trading System', 'title' => 'LMRSS Day Trading System', 'modules' => 8, 'price' => '$1,000', 'unlock_offer' => 'lmrss', 'included_in' => 'Trader, Ultimate'],
            ['key' => 'live_room', 'category' => 'Live', 'title' => 'OHC Trade Room Sessions', 'modules' => null, 'price' => 'Trader Path', 'unlock_offer' => 'trader', 'included_in' => 'Trader, Ultimate'],
            ['key' => 'derivatives', 'category' => 'Advanced Strategy', 'title' => 'Derivatives 101', 'modules' => 8, 'price' => '$1,000', 'unlock_offer' => 'derivatives', 'included_in' => 'Investor, Ultimate'],
            ['key' => 'advanced_derivatives', 'category' => 'Advanced Strategy', 'title' => 'Advanced Derivatives', 'modules' => 'Ultimate Path', 'price' => 'Ultimate Path', 'unlock_offer' => 'ultimate', 'included_in' => 'Ultimate'],
            ['key' => 'mentorship', 'category' => 'Coaching', 'title' => 'Group Mentorship', 'modules' => 'Ultimate Path', 'price' => 'Ultimate Path', 'unlock_offer' => 'ultimate', 'included_in' => 'Ultimate'],
        ];

        $recordedProgress = CourseProgress::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('course_key');

        $courses = collect($catalog)->map(function ($course) use ($user, $recordedProgress) {
            $progress = $recordedProgress->get($course['key']);
            $course['is_owned'] = $user->hasCourseAccess($course['key']);
            $course['progress'] = $progress?->percentage;
            $course['unlock_url'] = route('subscribe.show', $course['unlock_offer']);
            return $course;
        })->sortByDesc('is_owned')->values();

        return view('courses', compact('courses'));
    }
}