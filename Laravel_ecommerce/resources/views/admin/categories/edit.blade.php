{{-- resources/views/admin/categories/edit.blade.php --}}
@extends('layouts.admin')

@section('content')
<h1>Edit Category</h1>

<form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
    @csrf
    <label for="name">Category Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}">
    <button type="submit">Update</button>
</form>
@endsection
