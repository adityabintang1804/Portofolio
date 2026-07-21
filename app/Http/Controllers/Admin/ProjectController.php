<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = Project::query()->with('category:id,name')
            ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderBy('display_order')->latest('updated_at')->paginate(12)->withQueryString();

        return Inertia::render('Admin/Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Projects/Form', $this->formOptions());
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $this->persist(new Project, $request);

        return to_route('admin.projects.edit', $project)->with('success', 'Proyek berhasil dibuat.');
    }

    public function edit(Project $project): Response
    {
        $project->load(['technologies:id', 'images']);

        return Inertia::render('Admin/Projects/Form', [
            ...$this->formOptions(),
            'project' => [...$project->toArray(), 'technology_ids' => $project->technologies->pluck('id')],
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->persist($project, $request);

        return back()->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return to_route('admin.projects.index')->with('success', 'Proyek dipindahkan ke arsip.');
    }

    public function destroyImage(Project $project, ProjectImage $projectImage): RedirectResponse
    {
        abort_unless($projectImage->project_id === $project->id, 404);
        $this->deleteManagedFile($projectImage->image);
        $projectImage->delete();

        return back()->with('success', 'Gambar galeri berhasil dihapus.');
    }

    private function persist(Project $project, StoreProjectRequest $request): Project
    {
        $validated = $request->validated();
        $data = Arr::except($validated, ['technology_ids', 'thumbnail', 'og_image', 'gallery_images']);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if ($data['status'] !== 'published') {
            $data['published_at'] = null;
        }

        foreach (['thumbnail', 'og_image'] as $field) {
            if ($request->hasFile($field)) {
                $this->deleteManagedFile($project->{$field});
                $data[$field] = $request->file($field)->store('projects', 'public');
            }
        }

        $project->fill($data)->save();
        $project->technologies()->sync($validated['technology_ids'] ?? []);
        $nextOrder = (int) $project->images()->max('display_order') + 1;
        foreach ($request->file('gallery_images', []) as $image) {
            $project->images()->create([
                'image' => $image->store('projects/gallery', 'public'),
                'alt_text' => 'Galeri proyek '.$project->title,
                'display_order' => $nextOrder++,
            ]);
        }

        return $project;
    }

    private function formOptions(): array
    {
        return [
            'project' => null,
            'categories' => ProjectCategory::query()->where('is_active', true)->orderBy('display_order')->get(['id', 'name']),
            'technologies' => Technology::query()->where('is_active', true)->orderBy('display_order')->get(['id', 'name']),
        ];
    }

    private function deleteManagedFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'placeholders/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
