<?php

namespace App\Http\Controllers\Panel\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\AdminResource;
use App\Support\Admin\AdminResources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request, string $resource): View
    {
        $definition = $this->resolve($resource);
        $query = $definition->model()::query()->with($definition->with());

        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function ($where) use ($definition, $search): void {
                foreach ($definition->searchable() as $column) {
                    $where->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        $activeFilters = [];

        foreach ($definition->filters() as $name => $filter) {
            $value = $request->query($name);

            if (is_scalar($value) && array_key_exists((string) $value, $filter['options'])) {
                $query->where($name, (string) $value);
                $activeFilters[$name] = (string) $value;
            }
        }

        $sortable = collect($definition->columns())->where('sortable', true)->pluck('name')->all();
        $sort = $request->query('sort');
        $direction = $request->query('dir') === 'asc' ? 'asc' : 'desc';

        if (is_string($sort) && in_array($sort, $sortable, true)) {
            $query->orderBy($sort, $direction)->orderByDesc('id');
        } else {
            $sort = null;
            $query->orderByDesc('id');
        }

        return view('panel.admin.resources.index', [
            'definition' => $definition,
            'records' => $query->paginate(20)->withQueryString(),
            'search' => $search,
            'activeFilters' => $activeFilters,
            'sort' => $sort,
            'direction' => $direction,
            'total' => $definition->model()::query()->count(),
        ]);
    }

    public function create(string $resource): View
    {
        $definition = $this->resolve($resource);

        return view('panel.admin.resources.form', [
            'definition' => $definition,
            'record' => null,
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = $this->resolve($resource);

        $validated = $request->validate($definition->rules(null));
        $record = $definition->model()::query()->create($definition->prepare($validated, null));

        return redirect()
            ->route('panel.admin.resources.index', $definition->key())
            ->with('status', $definition->singular().' «'.$definition->recordTitle($record).'» ایجاد شد.');
    }

    public function edit(string $resource, int $id): View
    {
        $definition = $this->resolve($resource);

        return view('panel.admin.resources.form', [
            'definition' => $definition,
            'record' => $this->findRecord($definition, $id),
        ]);
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $definition = $this->resolve($resource);
        $record = $this->findRecord($definition, $id);

        $validated = $request->validate($definition->rules($record));
        $record->update($definition->prepare($validated, $record));

        return redirect()
            ->route('panel.admin.resources.index', $definition->key())
            ->with('status', 'تغییرات «'.$definition->recordTitle($record).'» ذخیره شد.');
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $definition = $this->resolve($resource);
        $record = $this->findRecord($definition, $id);
        $title = $definition->recordTitle($record);

        if (method_exists($record, 'reviews')) {
            $record->reviews()->delete();
        }

        $record->delete();

        return redirect()
            ->route('panel.admin.resources.index', $definition->key())
            ->with('status', $definition->singular().' «'.$title.'» حذف شد.');
    }

    public function toggle(string $resource, int $id, string $field): RedirectResponse
    {
        $definition = $this->resolve($resource);
        $record = $this->findRecord($definition, $id);

        $allowed = collect($definition->columns())
            ->where('type', 'toggle')
            ->pluck('name')
            ->all();

        abort_unless(in_array($field, $allowed, true), 404);

        $record->update([$field => ! $record->getAttribute($field)]);

        return back()->with('status', 'وضعیت «'.$definition->recordTitle($record).'» به‌روزرسانی شد.');
    }

    private function resolve(string $resource): AdminResource
    {
        $definition = AdminResources::find($resource);

        abort_if($definition === null, 404);

        return $definition;
    }

    private function findRecord(AdminResource $definition, int $id): Model
    {
        return $definition->model()::query()->findOrFail($id);
    }
}
