@extends('layouts.admin')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold text-white bg-dark p-3 rounded shadow-sm">
        <i class="bi bi-people-fill me-2"></i> User Management
    </h2>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="text-center">
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->is_blocked)
                                    <span class="badge bg-danger">Blocked</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $user->is_blocked ? 'btn-success' : 'btn-danger' }}">
                                        <i class="bi {{ $user->is_blocked ? 'bi-unlock-fill' : 'bi-lock-fill' }} me-1"></i>
                                        {{ $user->is_blocked ? 'Unblock' : 'Block' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="5" class="text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
