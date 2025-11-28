@extends('crud.form')

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasSelect = document.getElementById('kelas_id');
    const siswaSelect = document.getElementById('siswa_id');
    
    if (kelasSelect && siswaSelect) {
        // Store original student options
        const originalSiswaOptions = Array.from(siswaSelect.options);
        
        // Filter students based on selected class
        function filterSiswaByKelas() {
            const selectedKelasId = kelasSelect.value;
            
            // Clear current options
            siswaSelect.innerHTML = '';
            
            // Add placeholder option
            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = '-- Pilih Siswa --';
            siswaSelect.appendChild(placeholderOption);
            
            // Filter and add student options
            originalSiswaOptions.forEach(option => {
                if (option.value === '' || selectedKelasId === '') {
                    // Show all students if no class is selected
                    siswaSelect.appendChild(option.cloneNode(true));
                } else {
                    // Check if student belongs to selected class
                    const studentData = option.getAttribute('data-kelas-id');
                    if (studentData === selectedKelasId) {
                        siswaSelect.appendChild(option.cloneNode(true));
                    }
                }
            });
            
            // Reset selected student if it doesn't belong to the selected class
            if (siswaSelect.value && selectedKelasId) {
                const selectedOption = siswaSelect.querySelector(`option[value="${siswaSelect.value}"]`);
                if (!selectedOption || selectedOption.getAttribute('data-kelas-id') !== selectedKelasId) {
                    siswaSelect.value = '';
                }
            }
        }
        
        // Add event listener to class select
        kelasSelect.addEventListener('change', filterSiswaByKelas);
        
        // Initial filtering
        filterSiswaByKelas();
    }
});
</script>
@endsection

@section('form-fields')
@php
    // Get students with their class information for filtering
    $user = auth()->user();
    $studentsWithKelas = \App\Models\Siswa::with(['kelas'])
        ->whereHas('kelas', function($query) use ($user) {
            $query->where('guru', $user->id);
        })
        ->orderBy('nama_siswa')
        ->get(['id', 'nama_siswa', 'kelas_id']);
@endphp

<div class="row">
    @foreach($fields as $field)
        <div class="col-md-{{ $field['type'] === 'datetime-local' ? '6' : '12' }} mb-3">
            <label for="{{ $field['name'] }}" class="form-label">{{ $field['label'] }} @if(isset($field['rules']) && str_contains($field['rules'], 'required'))<span class="text-danger">*</span>@endif</label>
            
            @if($field['type'] === 'select')
                <select class="form-control @error($field['name']) is-invalid @enderror" 
                        id="{{ $field['name'] }}" 
                        name="{{ $field['name'] }}" 
                        @isset($field['rules']) required="{{ str_contains($field['rules'], 'required') ? 'required' : '' }}" @endisset>
                    <option value="">-- Pilih {{ $field['label'] }} --</option>
                    
                    @if($field['name'] === 'siswa_id')
                        @foreach($studentsWithKelas as $student)
                            <option value="{{ $student->id }}" 
                                    data-kelas-id="{{ $student->kelas_id }}"
                                    {{ old($field['name'], ($item->{$field['name']} ?? '')) == $student->id ? 'selected' : '' }}>
                                {{ $student->nama_siswa }}
                            </option>
                        @endforeach
                    @else
                        @foreach($field['options'] as $option)
                            <option value="{{ $option['value'] }}" 
                                    {{ old($field['name'], ($item->{$field['name']} ?? '')) == $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    @endif
                </select>
            @else
                <input type="{{ $field['type'] }}" 
                       class="form-control @error($field['name']) is-invalid @enderror" 
                       id="{{ $field['name'] }}" 
                       name="{{ $field['name'] }}" 
                       value="{{ old($field['name'], ($item->{$field['name']} ?? '')) }}"
                       @isset($field['rules']) required="{{ str_contains($field['rules'], 'required') ? 'required' : '' }}" @endisset>
            @endif
            
            @error($field['name'])
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>
@endsection
