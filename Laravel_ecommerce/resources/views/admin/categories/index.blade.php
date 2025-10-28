@extends('layouts.app')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">


@section('content')
<div class="container mt-5">
    <h2 class="text-3xl font-bold text-center mb-4 bg-gradient-to-r from-gray-800 to-black text-white py-3 rounded">
        🛍️ Manage Categories
    </h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success text-center fw-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Add Category Form --}}
    <div class="card shadow p-4 mb-5 border-0">
        <h5 class="mb-3 fw-bold text-dark">Add New Category</h5>
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="row align-items-end g-3">
                <div class="col-md-4">
                    <label for="name" class="form-label fw-semibold">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control border-dark shadow-sm" placeholder="Enter category name" required>
                </div>

                <div class="col-md-4">
                    <label for="parent_id" class="form-label fw-semibold">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="form-select border-dark shadow-sm">
                        <option value="">None (Main Category)</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-dark btn-lg shadow">+ Add Category</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Category Table --}}
    <div class="card shadow border-0">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">📂 All Categories</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Category Name</th>
                        <th>Parent Category</th>
                        <th class="text-center" style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        function renderCategories($categories, $level = 0) {
                            foreach ($categories as $category) {
                                echo '<tr>';
                                echo '<td>' . $category->id . '</td>';
                                echo '<td>';
                                echo str_repeat('&nbsp;&nbsp;&nbsp;— ', $level);
                                echo '<span class="fw-semibold">' . e($category->name) . '</span>';
                                echo '</td>';
                                echo '<td>' . ($category->parent ? e($category->parent->name) : '<span class="text-muted">Main Category</span>') . '</td>';
                                echo '<td class="text-center">';
                                echo '<a href="' . route('admin.categories.edit', $category->id) . '" class="btn btn-sm btn-success me-2 shadow-sm"><i class="bi bi-pencil-square"></i> Edit</a>';
                                echo '<form action="' . route('admin.categories.destroy', $category->id) . '" method="POST" class="d-inline">';
                                echo csrf_field() . method_field('DELETE');
                                echo '<button class="btn btn-sm btn-danger shadow-sm" onclick="return confirm(\'Delete this category?\')"><i class="bi bi-trash"></i> Delete</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';

                                if ($category->children->count()) {
                                    renderCategories($category->children, $level + 1);
                                }
                            }
                        }
                    @endphp

                    @if($categories->count())
                        {!! renderCategories($categories) !!}
                    @else
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No categories added yet.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
