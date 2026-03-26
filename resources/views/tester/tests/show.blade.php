{{-- resources/views/tester/tests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Test Details - ' . $test->test_name)

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="fas fa-file-alt me-2"></i> Test Details: {{ $test->test_name }}
                            </h4>
                            <p class="mb-0 opacity-75">{{ $test->description ?? 'No description available' }}</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-line fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('tester.tests.partials.stats')

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-info-circle me-2 text-primary"></i> Test Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Test Code</th>
                                    <td><code>{{ $test->test_code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Test Name</th>
                                    <td>{{ $test->test_name }}</td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td>{{ $test->duration_minutes }} minutes</td>
                                </tr>
                                <tr>
                                    <th>Total Questions</th>
                                    <td>{{ number_format($test->total_questions) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Total Columns</th>
                                    <td>{{ $test->total_columns }}</td>
                                </tr>
                                <tr>
                                    <th>Rows per Column</th>
                                    <td>{{ $test->rows_per_column }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td><span class="badge bg-{{ $test->is_active ? 'success' : 'danger' }}">{{ $test->is_active ? 'Active' : 'Inactive' }}</span></td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $test->created_at->format('d F Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="{{ route('tester.tests.edit', $test) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Test
                        </a>
                        <a href="{{ route('tester.tests') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endsection