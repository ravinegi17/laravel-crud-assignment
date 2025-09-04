@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit User</h4>
            </div>
            <div class="card-body">
                <form id="editUserForm" action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="first_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="last_name_error"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="email_error"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Leave blank to keep current password">
                        <small class="form-text text-muted">Leave blank to keep current password</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="password_error"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <button type="submit" class="btn btn-primary" id="updateBtn">
                            <i class="fas fa-save"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#editUserForm').submit(function(e) {
        e.preventDefault();
        
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#updateBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        if (!validateForm()) {
            $('#updateBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Update User');
            return;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                window.location.href = '{{ route("users.index") }}';
            },
            error: function(xhr) {
                $('#updateBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Update User');
                
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '_error').text(errors[key][0]).show();
                    });
                }
            }
        });
    });

    function validateForm() {
        let isValid = true;
        
        const firstName = $('#first_name').val().trim();
        const lastName = $('#last_name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();

        if (!firstName) {
            $('#first_name').addClass('is-invalid');
            $('#first_name_error').text('First name is required').show();
            isValid = false;
        }

        if (!lastName) {
            $('#last_name').addClass('is-invalid');
            $('#last_name_error').text('Last name is required').show();
            isValid = false;
        }

        if (!email) {
            $('#email').addClass('is-invalid');
            $('#email_error').text('Email is required').show();
            isValid = false;
        } else if (!isValidEmail(email)) {
            $('#email').addClass('is-invalid');
            $('#email_error').text('Please enter a valid email address').show();
            isValid = false;
        }

        if (password && password.length < 6) {
            $('#password').addClass('is-invalid');
            $('#password_error').text('Password must be at least 6 characters').show();
            isValid = false;
        }

        return isValid;
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});
</script>
@endsection
