@extends('layouts.admin')
@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex">
            <h6 class="m-0 font-weight-bold text-primary">supervisors ({{ $user->name }})</h6>
            <div class="ml-auto">
                <a href="{{ route('admin.supervisors.index') }}" class="btn btn-primary">
                    <span class="icon text-white-50">
                        <i class="fas fa-user"></i>
                    </span>
                    <span class="text">Supervisors</span>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <tbody>
                <tr>
                    <td colspan="4">
                        @if ($user->user_image != '')
                            <img src="{{ url('/') }}/public/assets/users/{{$user->user_image}}" width="60" height="60" class=" rounded-circle" style="box-shadow: 1px 1px 10px 1px #000000;">
                        @else
                            <img src="{{ url('/') }}/public/assets/users/default.png" width="60" height="60" class=" rounded-circle" style="box-shadow: 1px 1px 10px 1px #000000;">
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $user->name }} ({{ $user->username }})</td>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td>{{ $user->mobile }}</td>
                    <th>Status</th>
                    <td>{{ $user->status() }}</td>
                </tr>
                <tr>
                    <th>Created date</th>
                    <td>{{ $user->created_at->format('d-m-Y h:i a') }}</td>
                    <th>Posts Count</th>
                    <td>{{ $user->posts_count }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
