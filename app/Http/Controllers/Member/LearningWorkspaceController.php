<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\CourseProgress;
use App\Models\LearningCourse;
use App\Models\LearningLesson;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningWorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('entitlements');
        $progress = CourseProgress::where('user_id', $user->id)->get()->keyBy('course_key');
        $courses = LearningCourse::query()->where('status', 'published')->withCount(['modules'])->orderBy('sort_order')->get()
            ->map(function ($course) use ($user, $progress) {
                $course->member_progress = $progress->get($course->key);
                $course->member_has_access = $course->is_free || $user->hasCourseAccess($course->key);
                return $course;
            });

        return view('member.learn.index', compact('courses'));
    }

    public function course(Request $request, LearningCourse $course)
    {
        $this->ensurePublished($course);
        $course->load(['modules.lessons' => fn ($query) => $query->where('status', 'published')]);
        $this->ensureAccess($request, $course);
        $completed = LessonProgress::where('user_id', $request->user()->id)->whereNotNull('completed_at')
            ->whereIn('lesson_id', $course->modules->flatMap(fn ($module) => $module->lessons)->pluck('id'))->pluck('lesson_id');

        return view('member.learn.course', compact('course', 'completed'));
    }

    public function lesson(Request $request, LearningCourse $course, LearningLesson $lesson)
    {
        $this->ensurePublished($course);
        abort_unless($lesson->module?->course_id === $course->id && $lesson->status === 'published', 404);
        $this->ensureAccess($request, $course, (bool) $lesson->is_preview);
        $course->load(['modules.lessons' => fn ($query) => $query->where('status', 'published')]);
        $progress = LessonProgress::updateOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            ['last_viewed_at' => now()]
        );

        return view('member.learn.lesson', compact('course', 'lesson', 'progress'));
    }

    public function progress(Request $request, LearningLesson $lesson)
    {
        $course = $lesson->module?->course;
        abort_unless($course && $course->status === 'published' && $lesson->status === 'published', 404);
        $this->ensureAccess($request, $course, (bool) $lesson->is_preview);
        $data = $request->validate(['notes' => ['nullable', 'string', 'max:10000'], 'bookmarked' => ['nullable', 'boolean'], 'complete' => ['nullable', 'boolean']]);

        DB::transaction(function () use ($request, $lesson, $course, $data) {
            $record = LessonProgress::firstOrNew(['user_id' => $request->user()->id, 'lesson_id' => $lesson->id]);
            if (array_key_exists('notes', $data)) $record->notes = $data['notes'];
            if (array_key_exists('bookmarked', $data)) $record->bookmarked = (bool) $data['bookmarked'];
            if ($request->boolean('complete')) $record->completed_at = now();
            $record->last_viewed_at = now();
            $record->save();

            $lessonIds = $course->modules()->with('lessons:id,module_id')->get()->flatMap(fn ($module) => $module->lessons)->pluck('id');
            $completed = LessonProgress::where('user_id', $request->user()->id)->whereIn('lesson_id', $lessonIds)->whereNotNull('completed_at')->count();
            CourseProgress::updateOrCreate(
                ['user_id' => $request->user()->id, 'course_key' => $course->key],
                ['current_item_title' => $lesson->title, 'completed_items' => $completed, 'total_items' => $lessonIds->count(), 'resume_url' => route('learn.lesson', [$course, $lesson]), 'last_activity_at' => now()]
            );
        });

        return back()->with('status', 'Learning progress saved.');
    }

    private function ensurePublished(LearningCourse $course): void { abort_unless($course->status === 'published', 404); }
    private function ensureAccess(Request $request, LearningCourse $course, bool $preview = false): void
    {
        abort_unless($preview || $course->is_free || $request->user()->loadMissing('entitlements')->hasCourseAccess($course->key), 403, 'This lesson is not included in your current Path.');
    }
}
