@extends('layouts.app')

@section('title', 'Register - TOPCIT')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg" data-aos="fade-up" data-aos-duration="800">
        <!-- Brand -->
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🎓</div>
            <h1 class="text-3xl font-extrabold gradient-text">Create Account</h1>
            <p class="text-gray-400 text-sm mt-2">Register with face recognition support</p>
        </div>

        <!-- Register Card -->
        <div class="card">
            <form method="POST" action="{{ url('/portal/register/add') }}" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Student Number</label>
                            <input type="text" name="student_no" class="input-field" placeholder="e.g. 2023-00123" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                            <input type="email" name="email" class="input-field" placeholder="you@school.edu" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Contact Number</label>
                        <input type="text" name="contact" class="input-field" placeholder="09XXXXXXXXX" required>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                            <input type="password" name="password" class="input-field" placeholder="Min 8 characters" minlength="8" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm Password</label>
                            <input type="password" name="confirm_password" class="input-field" placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <!-- Face Photos Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Face Photos (for recognition)</label>
                        <div class="border-2 border-dashed border-white/10 rounded-lg p-6 text-center hover:border-primary/50 transition-colors cursor-pointer"
                             x-data @click="$refs.photoInput.click()">
                            <div class="text-3xl mb-2">📸</div>
                            <p class="text-sm text-gray-400">Click to upload face photos</p>
                            <p class="text-xs text-gray-500 mt-1">JPEG, PNG • Max 2MB each</p>
                            <input type="file" name="photos[]" multiple accept="image/jpeg,image/png" x-ref="photoInput"
                                   class="hidden" @change="const el = $el; const label = el.nextElementSibling;
                                   label.textContent = el.files.length + ' photo(s) selected'">
                            <span class="block text-xs text-primary mt-2">0 photos selected</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full ripple">Create Account</button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-400">
                    Already have an account?
                    <a href="{{ url('/portal') }}" class="text-primary hover:text-secondary transition-colors">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection