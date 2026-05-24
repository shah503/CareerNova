@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-cogs"></i> System Settings</h2>
        </div>
    </div>

    <div class="row">
        <!-- Tabs Navigation -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                    <i class="fas fa-sliders-h"></i> General Settings
                </a>
                <a href="#payment" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-credit-card"></i> Payment Settings
                </a>
                <a href="#features" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-star"></i> Features
                </a>
                <a href="#system" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-heartbeat"></i> System Status
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="col-md-9">
            <!-- General Settings -->
            <div class="card shadow-sm mb-4" id="general">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-sliders-h"></i> General Settings</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/settings/general" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                   value="{{ $settings['site_name']['value'] ?? 'CareerNova' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="support_email" class="form-label">Support Email</label>
                            <input type="email" class="form-control" id="support_email" name="support_email"
                                   value="{{ $settings['support_email']['value'] ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="whatsapp_support" class="form-label">WhatsApp Support Number</label>
                            <input type="text" class="form-control" id="whatsapp_support" name="whatsapp_support"
                                   value="{{ $settings['whatsapp_support']['value'] ?? '' }}" placeholder="+92...">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Payment Settings -->
            <div class="card shadow-sm mb-4" id="payment">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Payment Settings</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/settings/payment" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="jazzcash_number" class="form-label">JazzCash Number</label>
                            <input type="text" class="form-control" id="jazzcash_number" name="jazzcash_number"
                                   value="{{ $paymentSettings['jazzcash_number'] ?? '' }}" placeholder="03XX-XXXXXXX">
                        </div>

                        <div class="mb-3">
                            <label for="easypaisa_number" class="form-label">EasyPaisa Number</label>
                            <input type="text" class="form-control" id="easypaisa_number" name="easypaisa_number"
                                   value="{{ $paymentSettings['easypaisa_number'] ?? '' }}" placeholder="03XX-XXXXXXX">
                        </div>

                        <div class="mb-3">
                            <label for="bank_iban" class="form-label">Bank IBAN</label>
                            <input type="text" class="form-control" id="bank_iban" name="bank_iban"
                                   value="{{ $paymentSettings['bank_iban'] ?? '' }}" placeholder="PK...">
                        </div>

                        <div class="mb-3">
                            <label for="bank_title" class="form-label">Bank Title</label>
                            <input type="text" class="form-control" id="bank_title" name="bank_title"
                                   value="{{ $paymentSettings['bank_title'] ?? '' }}" placeholder="Bank Name">
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Features -->
            <div class="card shadow-sm mb-4" id="features">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-star"></i> Features</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach (['ai_mcq' => 'AI MCQ Generation', 'csv_import' => 'CSV Import', 'payments' => 'Payment Gateway', 'analytics' => 'Analytics'] as $key => $label)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ $label }}</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input feature-toggle" type="checkbox" 
                                           id="feature_{{ $key }}" data-feature="feature_{{ $key }}"
                                           {{ isset($features[$key]) && $features[$key] ? 'checked' : '' }}>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card shadow-sm mb-4" id="system">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-heartbeat"></i> System Status</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        When disabled, students cannot access the platform. Teachers and admins can still access.
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                        <div>
                            <h6>System Status</h6>
                            <p class="text-muted small mb-0">Enable or disable the entire platform</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input system-toggle" type="checkbox" id="systemToggle"
                                   {{ $systemActive ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.feature-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const feature = this.dataset.feature;
            fetch('/admin/settings/feature/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    feature: feature,
                    status: this.checked ? 'enable' : 'disable'
                })
            }).then(response => response.json())
              .then(data => {
                  alert(data.message);
              });
        });
    });

    document.querySelector('.system-toggle').addEventListener('change', function() {
        fetch('/admin/settings/system/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
              location.reload();
          });
    });
</script>
@endpush
@endsection