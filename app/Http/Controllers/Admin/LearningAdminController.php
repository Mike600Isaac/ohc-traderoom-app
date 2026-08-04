<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningAssessment;
use App\Models\LearningCourse;
use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Services\PublishingService;
use App\Support\AdminAccess;
use App\Support\AdminAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LearningAdminController extends Controller
{
    public function index(Request $request)
    {
        AdminAccess::require($request->user(), 'content.view');
        $courses = LearningCourse::withCount('modules')->orderBy('sort_order')->paginate(20);
        return view('admin.learning.index', compact('courses'));
    }

    public function create(Request $request)
    {
        AdminAccess::require($request->user(), 'content.manage');
        return view('admin.learning.course-form', ['course' => new LearningCourse]);
    }

    public function storeCourse(Request $request, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'content.manage');
        $data = $this->courseData($request);
        $data['is_free'] = $request->boolean('is_free');
        $data['key'] = Str::slug($data['key'] ?: $data['title'], '_');
        if (LearningCourse::where('key', $data['key'])->exists()) {
            return back()->withErrors(['key' => 'That course key is already in use.'])->withInput();
        }
        $course = new LearningCourse(collect($data)->except(['status', 'scheduled_for'])->all());
        $publisher->apply($course, $data['status'], $data['scheduled_for'] ?? null);
        $course->save();
        AdminAudit::record($request, 'course.created', $course, "Created course: {$course->title}", $course->toArray());
        return redirect()->route('admin.courses.edit', $course)->with('status', 'Course created. Add its modules and lessons below.');
    }

    public function editCourse(Request $request, LearningCourse $course)
    {
        AdminAccess::require($request->user(), 'content.view');
        $course->load('modules.lessons.assessments');
        return view('admin.learning.course-form', compact('course'));
    }

    public function updateCourse(Request $request, LearningCourse $course, PublishingService $publisher)
    {
        AdminAccess::require($request->user(), 'content.manage');
        $data = $this->courseData($request, $course);
        $data['is_free'] = $request->boolean('is_free');
        $data['key'] = Str::slug($data['key'] ?: $data['title'], '_');
        if (LearningCourse::where('key', $data['key'])->where('id', '!=', $course->id)->exists()) {
            return back()->withErrors(['key' => 'That course key is already in use.'])->withInput();
        }
        $course->fill(collect($data)->except(['status', 'scheduled_for'])->all());
        $publisher->apply($course, $data['status'], $data['scheduled_for'] ?? null);
        $course->save();
        AdminAudit::record($request, 'course.updated', $course, "Updated course: {$course->title}", AdminAudit::changes($course));
        return back()->with('status', 'Course updated.');
    }

    public function storeModule(Request $request, LearningCourse $course)
    {
        AdminAccess::require($request->user(), 'content.manage');
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
        $module = $course->modules()->create($data);
        AdminAudit::record($request, 'module.created', $module, "Added module to {$course->title}: {$module->title}", $data);
        return back()->with('status', 'Module added.');
    }

    public function updateModule(Request $request, LearningCourse $course, LearningModule $module)
    {
        AdminAccess::require($request->user(), 'content.manage');
        abort_unless($module->course_id === $course->id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
        $module->update($data);
        AdminAudit::record($request, 'module.updated', $module, "Updated module: {$module->title}", AdminAudit::changes($module));
        return back()->with('status', 'Module updated.');
    }

    public function createLesson(Request $request, LearningModule $module)
    {
        AdminAccess::require($request->user(), 'content.manage');
        return view('admin.learning.lesson-form', ['lesson' => new LearningLesson, 'module' => $module]);
    }

    public function storeLesson(Request $request, LearningModule $module)
    {
        AdminAccess::require($request->user(), 'content.manage');
        $data = $this->lessonData($request);
        $data['is_preview'] = $request->boolean('is_preview');
        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('course-documents', 'public');
        }
        unset($data['document']);
        $lesson = $module->lessons()->create($data);
        AdminAudit::record($request, 'lesson.created', $lesson, "Created lesson: {$lesson->title}", collect($data)->except('body')->all());
        return redirect()->route('admin.lessons.edit', $lesson)->with('status', 'Lesson created.');
    }

    public function editLesson(Request $request, LearningLesson $lesson)
    {
        AdminAccess::require($request->user(), 'content.view');
        $lesson->load(['module.course', 'assessments']);
        return view('admin.learning.lesson-form', ['lesson' => $lesson, 'module' => $lesson->module]);
    }

    public function updateLesson(Request $request, LearningLesson $lesson)
    {
        AdminAccess::require($request->user(), 'content.manage');
        $data = $this->lessonData($request);
        $data['is_preview'] = $request->boolean('is_preview');
        if ($request->hasFile('document')) {
            $data['document_path'] = $request->file('document')->store('course-documents', 'public');
        }
        unset($data['document']);
        $lesson->update($data);
        AdminAudit::record($request, 'lesson.updated', $lesson, "Updated lesson: {$lesson->title}", AdminAudit::changes($lesson));
        return back()->with('status', 'Lesson updated.');
    }

    public function storeAssessment(Request $request, LearningLesson $lesson)
    {
        AdminAccess::require($request->user(), 'content.manage');
        $data = $request->validate([
            'question' => ['required', 'string'],
            'options_text' => ['required', 'string'],
            'correct_option' => ['required', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
        $options = array_values(array_filter(array_map('trim', preg_split('/\R/', $data['options_text']) ?: [])));
        if (count($options) < 2 || $data['correct_option'] > count($options)) {
            return back()->withErrors(['options_text' => 'Provide at least two options and a valid correct option number.'])->withInput();
        }
        unset($data['options_text']);
        $data['options'] = $options;
        $assessment = $lesson->assessments()->create($data);
        AdminAudit::record($request, 'assessment.created', $assessment, "Added assessment to {$lesson->title}", collect($data)->except('options')->all());
        return back()->with('status', 'Assessment added.');
    }

    private function courseData(Request $request, ?LearningCourse $course = null): array
    {
        return $request->validate([
            'key' => ['nullable', 'string', 'max:100', Rule::unique('learning_courses', 'key')->ignore($course?->id)],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'scheduled', 'published'])],
            'scheduled_for' => ['nullable', 'required_if:status,scheduled', 'date'],
            'is_free' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }

    private function lessonData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:2048'],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_preview' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}
