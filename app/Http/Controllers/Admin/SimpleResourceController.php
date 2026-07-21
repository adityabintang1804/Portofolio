<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateCategory;
use App\Models\ChatbotFaq;
use App\Models\Education;
use App\Models\Experience;
use App\Models\ProjectCategory;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class SimpleResourceController extends Controller
{
    public function index(Request $request): Response
    {
        $config = $this->config($request);
        $query = $config['model']::query();
        if ($config['with']) {
            $query->with($config['with']);
        }
        if ($request->string('search')->isNotEmpty()) {
            $query->where($config['search'], 'like', '%'.$request->string('search').'%');
        }

        return Inertia::render('Admin/Resources/Index', [
            'resource' => $request->route('resource'),
            'title' => $config['title'],
            'singular' => $config['singular'],
            'columns' => $config['columns'],
            'records' => $query->orderBy($config['order'])->paginate(15)->withQueryString(),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(Request $request): Response
    {
        return $this->formResponse($request);
    }

    public function store(Request $request): RedirectResponse
    {
        $config = $this->config($request);
        $model = new $config['model'];
        $this->persist($request, $model, $config);

        return to_route("admin.{$request->route('resource')}.index")->with('success', "{$config['singular']} berhasil ditambahkan.");
    }

    public function edit(Request $request, int $record): Response
    {
        return $this->formResponse($request, $record);
    }

    public function update(Request $request, int $record): RedirectResponse
    {
        $config = $this->config($request);
        $model = $config['model']::query()->findOrFail($record);
        $this->persist($request, $model, $config);

        return back()->with('success', "{$config['singular']} berhasil diperbarui.");
    }

    public function destroy(Request $request, int $record): RedirectResponse
    {
        $config = $this->config($request);
        $model = $config['model']::query()->findOrFail($record);

        try {
            foreach ($config['files'] as $field => $folder) {
                $this->deleteManagedFile($model->{$field});
            }
            $model->delete();
        } catch (Throwable) {
            return back()->with('error', "{$config['singular']} masih digunakan dan tidak dapat dihapus.");
        }

        return back()->with('success', "{$config['singular']} berhasil dihapus.");
    }

    private function formResponse(Request $request, ?int $recordId = null): Response
    {
        $config = $this->config($request);
        $record = $recordId ? $config['model']::query()->findOrFail($recordId) : null;
        $values = $record?->toArray();
        foreach ($config['arrays'] as $field) {
            if ($record) {
                $values[$field] = implode("\n", $record->{$field} ?? []);
            }
        }

        return Inertia::render('Admin/Resources/Form', [
            'resource' => $request->route('resource'),
            'title' => $config['title'],
            'singular' => $config['singular'],
            'fields' => $this->fieldsWithOptions($config['fields']),
            'record' => $values,
        ]);
    }

    private function persist(Request $request, Model $model, array $config): void
    {
        $rules = $this->rules($request->route('resource'), $model->exists ? $model->getKey() : null);
        $data = $request->validate($rules);

        foreach ($config['arrays'] as $field) {
            $data[$field] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data[$field] ?? ''))));
        }
        foreach ($config['booleans'] as $field) {
            $data[$field] = $request->boolean($field);
        }
        foreach ($config['files'] as $field => $folder) {
            if ($request->hasFile($field)) {
                $this->deleteManagedFile($model->{$field});
                $data[$field] = $request->file($field)->store($folder, 'public');
            } else {
                unset($data[$field]);
            }
        }

        $model->fill($data)->save();
    }

    private function fieldsWithOptions(array $fields): array
    {
        return array_map(function (array $field): array {
            if (isset($field['optionsFrom'])) {
                [$model, $label] = $field['optionsFrom'];
                $field['options'] = $model::query()->orderBy($label)->get(['id', $label])->map(fn ($item) => [
                    'value' => (string) $item->id,
                    'label' => $item->{$label},
                ]);
                unset($field['optionsFrom']);
            }

            return $field;
        }, $fields);
    }

    private function rules(string $resource, ?int $id): array
    {
        $unique = fn (string $table, string $column = 'slug') => Rule::unique($table, $column)->ignore($id);
        $commonOrder = ['display_order' => ['required', 'integer', 'min:0'], 'is_active' => ['boolean']];

        return match ($resource) {
            'project-categories' => [...$commonOrder, 'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', $unique('project_categories')], 'description' => ['nullable', 'string', 'max:1000']],
            'technologies' => [...$commonOrder, 'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', $unique('technologies')], 'icon_key' => ['nullable', 'string', 'max:80'], 'brand_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/']],
            'skill-categories' => [...$commonOrder, 'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', $unique('skill_categories')], 'description' => ['nullable', 'string', 'max:1000']],
            'skills' => [...$commonOrder, 'skill_category_id' => ['required', 'exists:skill_categories,id'], 'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', $unique('skills')], 'icon_key' => ['nullable', 'string', 'max:80'], 'description' => ['nullable', 'string', 'max:1000']],
            'experiences' => [...$commonOrder, 'position' => ['required', 'string', 'max:180'], 'organization' => ['required', 'string', 'max:180'], 'location' => ['nullable', 'string', 'max:120'], 'start_date' => ['required', 'date'], 'end_date' => ['nullable', 'date', 'after_or_equal:start_date'], 'is_current' => ['boolean'], 'description' => ['required', 'string', 'max:10000'], 'responsibilities' => ['nullable', 'string', 'max:10000'], 'technologies' => ['nullable', 'string', 'max:5000'], 'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']],
            'educations' => [...$commonOrder, 'institution' => ['required', 'string', 'max:180'], 'degree' => ['required', 'string', 'max:120'], 'study_program' => ['required', 'string', 'max:120'], 'location' => ['nullable', 'string', 'max:120'], 'start_year' => ['required', 'integer', 'min:1950', 'max:2100'], 'end_year' => ['nullable', 'integer', 'gte:start_year', 'max:2100'], 'description' => ['nullable', 'string', 'max:10000'], 'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']],
            'certificate-categories' => [...$commonOrder, 'name' => ['required', 'string', 'max:120'], 'slug' => ['required', 'alpha_dash', $unique('certificate_categories')]],
            'certificates' => [...$commonOrder, 'certificate_category_id' => ['required', 'exists:certificate_categories,id'], 'title' => ['required', 'string', 'max:180'], 'issuer' => ['required', 'string', 'max:180'], 'issued_at' => ['required', 'date'], 'credential_id' => ['nullable', 'string', 'max:180'], 'credential_url' => ['nullable', 'url', 'max:255'], 'is_featured' => ['boolean'], 'certificate_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072']],
            'chatbot-faqs' => ['question' => ['required', 'string', 'max:500'], 'answer' => ['required', 'string', 'max:10000'], 'keywords' => ['nullable', 'string', 'max:2000'], 'action_label' => ['nullable', 'string', 'max:100'], 'action_url' => ['nullable', 'string', 'max:255'], 'priority' => ['required', 'integer', 'min:0'], 'is_active' => ['boolean']],
            default => abort(404),
        };
    }

    private function config(Request $request): array
    {
        $resource = $request->route('resource');
        $base = match ($resource) {
            'project-categories' => ['model' => ProjectCategory::class, 'title' => 'Kategori Proyek', 'singular' => 'Kategori proyek', 'search' => 'name', 'order' => 'display_order', 'columns' => [['key' => 'name', 'label' => 'Nama'], ['key' => 'slug', 'label' => 'Slug'], ['key' => 'is_active', 'label' => 'Aktif']], 'fields' => $this->categoryFields()],
            'technologies' => ['model' => Technology::class, 'title' => 'Teknologi', 'singular' => 'Teknologi', 'search' => 'name', 'order' => 'display_order', 'columns' => [['key' => 'name', 'label' => 'Nama'], ['key' => 'icon_key', 'label' => 'Icon key'], ['key' => 'is_active', 'label' => 'Aktif']], 'fields' => [['name' => 'name', 'label' => 'Nama', 'type' => 'text'], ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ['name' => 'icon_key', 'label' => 'Icon key', 'type' => 'text'], ['name' => 'brand_color', 'label' => 'Warna brand (#RRGGBB)', 'type' => 'text'], ...$this->orderFields()]],
            'skill-categories' => ['model' => SkillCategory::class, 'title' => 'Kategori Skill', 'singular' => 'Kategori skill', 'search' => 'name', 'order' => 'display_order', 'columns' => [['key' => 'name', 'label' => 'Nama'], ['key' => 'slug', 'label' => 'Slug'], ['key' => 'is_active', 'label' => 'Aktif']], 'fields' => $this->categoryFields()],
            'skills' => ['model' => Skill::class, 'title' => 'Skills', 'singular' => 'Skill', 'search' => 'name', 'order' => 'display_order', 'with' => ['category:id,name'], 'columns' => [['key' => 'name', 'label' => 'Nama'], ['key' => 'category.name', 'label' => 'Kategori'], ['key' => 'is_active', 'label' => 'Aktif']], 'fields' => [['name' => 'skill_category_id', 'label' => 'Kategori', 'type' => 'select', 'optionsFrom' => [SkillCategory::class, 'name']], ['name' => 'name', 'label' => 'Nama', 'type' => 'text'], ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ['name' => 'icon_key', 'label' => 'Icon key', 'type' => 'text'], ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'], ...$this->orderFields()]],
            'experiences' => ['model' => Experience::class, 'title' => 'Pengalaman', 'singular' => 'Pengalaman', 'search' => 'position', 'order' => 'display_order', 'columns' => [['key' => 'position', 'label' => 'Posisi'], ['key' => 'organization', 'label' => 'Organisasi'], ['key' => 'is_current', 'label' => 'Saat ini']], 'fields' => $this->experienceFields(), 'arrays' => ['responsibilities', 'technologies'], 'booleans' => ['is_current', 'is_active'], 'files' => ['logo' => 'organizations']],
            'educations' => ['model' => Education::class, 'title' => 'Pendidikan', 'singular' => 'Pendidikan', 'search' => 'institution', 'order' => 'display_order', 'columns' => [['key' => 'institution', 'label' => 'Institusi'], ['key' => 'study_program', 'label' => 'Program'], ['key' => 'start_year', 'label' => 'Mulai']], 'fields' => $this->educationFields(), 'files' => ['logo' => 'organizations']],
            'certificate-categories' => ['model' => CertificateCategory::class, 'title' => 'Kategori Sertifikat', 'singular' => 'Kategori sertifikat', 'search' => 'name', 'order' => 'display_order', 'columns' => [['key' => 'name', 'label' => 'Nama'], ['key' => 'slug', 'label' => 'Slug'], ['key' => 'is_active', 'label' => 'Aktif']], 'fields' => [['name' => 'name', 'label' => 'Nama', 'type' => 'text'], ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ...$this->orderFields()]],
            'certificates' => ['model' => Certificate::class, 'title' => 'Sertifikat', 'singular' => 'Sertifikat', 'search' => 'title', 'order' => 'display_order', 'with' => ['category:id,name'], 'columns' => [['key' => 'title', 'label' => 'Judul'], ['key' => 'issuer', 'label' => 'Penerbit'], ['key' => 'category.name', 'label' => 'Kategori']], 'fields' => $this->certificateFields(), 'booleans' => ['is_featured', 'is_active'], 'files' => ['certificate_image' => 'certificates']],
            'chatbot-faqs' => ['model' => ChatbotFaq::class, 'title' => 'FAQ Chatbot', 'singular' => 'FAQ', 'search' => 'question', 'order' => 'priority', 'columns' => [['key' => 'question', 'label' => 'Pertanyaan'], ['key' => 'priority', 'label' => 'Prioritas'], ['key' => 'is_active', 'label' => 'Aktif']], 'fields' => $this->faqFields(), 'arrays' => ['keywords']],
            default => abort(404),
        };

        return array_merge(['with' => [], 'arrays' => [], 'booleans' => ['is_active'], 'files' => []], $base);
    }

    private function categoryFields(): array
    {
        return [['name' => 'name', 'label' => 'Nama', 'type' => 'text'], ['name' => 'slug', 'label' => 'Slug', 'type' => 'text'], ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'], ...$this->orderFields()];
    }

    private function orderFields(): array
    {
        return [['name' => 'display_order', 'label' => 'Urutan', 'type' => 'number', 'default' => 0], ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true]];
    }

    private function experienceFields(): array
    {
        return [['name' => 'position', 'label' => 'Posisi', 'type' => 'text'], ['name' => 'organization', 'label' => 'Organisasi', 'type' => 'text'], ['name' => 'location', 'label' => 'Lokasi', 'type' => 'text'], ['name' => 'logo', 'label' => 'Logo', 'type' => 'file'], ['name' => 'start_date', 'label' => 'Tanggal mulai', 'type' => 'date'], ['name' => 'end_date', 'label' => 'Tanggal selesai', 'type' => 'date'], ['name' => 'is_current', 'label' => 'Masih berlangsung', 'type' => 'checkbox'], ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'], ['name' => 'responsibilities', 'label' => 'Kontribusi (satu per baris)', 'type' => 'textarea'], ['name' => 'technologies', 'label' => 'Teknologi (satu per baris)', 'type' => 'textarea'], ...$this->orderFields()];
    }

    private function educationFields(): array
    {
        return [['name' => 'institution', 'label' => 'Institusi', 'type' => 'text'], ['name' => 'degree', 'label' => 'Jenjang', 'type' => 'text'], ['name' => 'study_program', 'label' => 'Program studi', 'type' => 'text'], ['name' => 'location', 'label' => 'Lokasi', 'type' => 'text'], ['name' => 'logo', 'label' => 'Logo', 'type' => 'file'], ['name' => 'start_year', 'label' => 'Tahun mulai', 'type' => 'number'], ['name' => 'end_year', 'label' => 'Tahun selesai', 'type' => 'number'], ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea'], ...$this->orderFields()];
    }

    private function certificateFields(): array
    {
        return [['name' => 'certificate_category_id', 'label' => 'Kategori', 'type' => 'select', 'optionsFrom' => [CertificateCategory::class, 'name']], ['name' => 'title', 'label' => 'Judul', 'type' => 'text'], ['name' => 'issuer', 'label' => 'Penerbit', 'type' => 'text'], ['name' => 'issued_at', 'label' => 'Tanggal terbit', 'type' => 'date'], ['name' => 'credential_id', 'label' => 'Credential ID', 'type' => 'text'], ['name' => 'credential_url', 'label' => 'Credential URL', 'type' => 'url'], ['name' => 'certificate_image', 'label' => 'Gambar sertifikat', 'type' => 'file'], ['name' => 'is_featured', 'label' => 'Unggulan', 'type' => 'checkbox'], ...$this->orderFields()];
    }

    private function faqFields(): array
    {
        return [['name' => 'question', 'label' => 'Pertanyaan', 'type' => 'textarea'], ['name' => 'answer', 'label' => 'Jawaban', 'type' => 'textarea'], ['name' => 'keywords', 'label' => 'Keyword (satu per baris)', 'type' => 'textarea'], ['name' => 'action_label', 'label' => 'Label aksi', 'type' => 'text'], ['name' => 'action_url', 'label' => 'URL aksi', 'type' => 'text'], ['name' => 'priority', 'label' => 'Prioritas', 'type' => 'number', 'default' => 0], ['name' => 'is_active', 'label' => 'Aktif', 'type' => 'checkbox', 'default' => true]];
    }

    private function deleteManagedFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'placeholders/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
