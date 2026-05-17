<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Register New Customer') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('customers.store') }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" />
                                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
                                <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" />
                                <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" />
                                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" :value="__('Email (Optional)')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number')" required pattern="^(09|\+639)\d{9}$" placeholder="e.g. 09123456789 or +639123456789" title="Valid Philippine format: 09123456789 or +639123456789" />
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2 pt-4">Address</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="region_id" :value="__('Region')" />
                                <select id="region_id" name="region_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Select Region</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('region_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="province_id" :value="__('Province')" />
                                <select id="province_id" name="province_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required disabled>
                                    <option value="">Select Province</option>
                                </select>
                                <x-input-error :messages="$errors->get('province_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="city_id" :value="__('City / Municipality')" />
                                <select id="city_id" name="city_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required disabled>
                                    <option value="">Select City</option>
                                </select>
                                <x-input-error :messages="$errors->get('city_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="barangay_id" :value="__('Barangay')" />
                                <select id="barangay_id" name="barangay_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required disabled>
                                    <option value="">Select Barangay</option>
                                </select>
                                <x-input-error :messages="$errors->get('barangay_id')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="address_line" :value="__('Street / Building (Optional)')" />
                            <x-text-input id="address_line" class="block mt-1 w-full" type="text" name="address_line" :value="old('address_line')" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2 pt-4">ID Verification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="id_type" :value="__('ID Type')" />
                                <select id="id_type" name="id_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Select ID Type</option>
                                    <option value="national_id" @selected(old('id_type')==='national_id')>National ID</option>
                                    <option value="passport" @selected(old('id_type')==='passport')>Passport</option>
                                    <option value="driver_license" @selected(old('id_type')==='driver_license')>Driver's License</option>
                                    <option value="sss" @selected(old('id_type')==='sss')>SSS</option>
                                    <option value="philhealth" @selected(old('id_type')==='philhealth')>PhilHealth</option>
                                    <option value="voters_id" @selected(old('id_type')==='voters_id')>Voter's ID</option>
                                </select>
                                <x-input-error :messages="$errors->get('id_type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="id_number" :value="__('ID Number')" />
                                <x-text-input id="id_number" class="block mt-1 w-full" type="text" name="id_number" :value="old('id_number')" required />
                                <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="id_image" :value="__('Upload ID Image (Optional)')" />
                            <input id="id_image" type="file" name="id_image" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300" />
                            <x-input-error :messages="$errors->get('id_image')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        </div>
                        <div class="flex gap-4">
                            <x-primary-button>{{ __('Register Customer') }}</x-primary-button>
                            <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const r=document.getElementById('region_id'), p=document.getElementById('province_id'), c=document.getElementById('city_id'), b=document.getElementById('barangay_id');
        function reset(s,ph){s.innerHTML=`<option value="">${ph}</option>`;s.disabled=true;}
        function fill(s,data,ph){s.innerHTML=`<option value="">${ph}</option>`;data.forEach(i=>{const o=document.createElement('option');o.value=i.id;o.textContent=i.name;s.appendChild(o);});s.disabled=false;}
        r.addEventListener('change',function(){reset(p,'Select Province');reset(c,'Select City');reset(b,'Select Barangay');if(this.value)fetch(`/api/provinces/${this.value}`).then(r=>r.json()).then(d=>fill(p,d,'Select Province'));});
        p.addEventListener('change',function(){reset(c,'Select City');reset(b,'Select Barangay');if(this.value)fetch(`/api/cities/${this.value}`).then(r=>r.json()).then(d=>fill(c,d,'Select City'));});
        c.addEventListener('change',function(){reset(b,'Select Barangay');if(this.value)fetch(`/api/barangays/${this.value}`).then(r=>r.json()).then(d=>fill(b,d,'Select Barangay'));});
    });
    </script>
</x-app-layout>
