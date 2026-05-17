<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Customer') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="first_name" :value="__('First Name')" />
                                <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name', $customer->first_name)" required />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="middle_name" :value="__('Middle Name (Optional)')" />
                                <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name', $customer->middle_name)" />
                            </div>
                            <div>
                                <x-input-label for="last_name" :value="__('Last Name')" />
                                <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name', $customer->last_name)" required />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" :value="__('Email (Optional)')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $customer->email)" />
                            </div>
                            <div>
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number', $customer->phone_number)" required pattern="^(09|\+639)\d{9}$" placeholder="e.g. 09123456789 or +639123456789" title="Valid Philippine format: 09123456789 or +639123456789" />
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
                                        <option value="{{ $region->id }}" @selected(old('region_id', $customer->region_id) == $region->id)>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="province_id" :value="__('Province')" />
                                <select id="province_id" name="province_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Select Province</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="city_id" :value="__('City / Municipality')" />
                                <select id="city_id" name="city_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="barangay_id" :value="__('Barangay')" />
                                <select id="barangay_id" name="barangay_id" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Select Barangay</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="address_line" :value="__('Street / Building (Optional)')" />
                            <x-text-input id="address_line" class="block mt-1 w-full" type="text" name="address_line" :value="old('address_line', $customer->address_line)" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2 pt-4">ID Verification</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="id_type" :value="__('ID Type')" />
                                <select id="id_type" name="id_type" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                                    <option value="">Select ID Type</option>
                                    @foreach(['national_id'=>'National ID','passport'=>'Passport','driver_license'=>"Driver's License",'sss'=>'SSS','philhealth'=>'PhilHealth','voters_id'=>"Voter's ID"] as $val => $lbl)
                                        <option value="{{ $val }}" @selected(old('id_type', $customer->id_type)===$val)>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="id_number" :value="__('ID Number')" />
                                <x-text-input id="id_number" class="block mt-1 w-full" type="text" name="id_number" :value="old('id_number', $customer->id_number)" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="id_image" :value="__('Upload New ID Image (Optional)')" />
                            @if($customer->id_image_path)
                                <div class="mb-2"><img src="{{ asset('storage/' . $customer->id_image_path) }}" alt="Current ID" class="h-24 rounded border dark:border-gray-600"></div>
                            @endif
                            <input id="id_image" type="file" name="id_image" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-gray-700 dark:file:text-gray-300" />
                        </div>
                        <div>
                            <x-input-label for="is_active" :value="__('Status')" />
                            <div class="mt-2 flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $customer->is_active)) class="rounded dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm">
                                <label for="is_active" class="ms-2 text-sm text-gray-600 dark:text-gray-400">Active</label>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="notes" :value="__('Notes (Optional)')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">{{ old('notes', $customer->notes) }}</textarea>
                        </div>
                        <div class="flex gap-4">
                            <x-primary-button>{{ __('Update Customer') }}</x-primary-button>
                            <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const r=document.getElementById('region_id'),p=document.getElementById('province_id'),c=document.getElementById('city_id'),b=document.getElementById('barangay_id');
        const oldProv='{{ old("province_id", $customer->province_id) }}', oldCity='{{ old("city_id", $customer->city_id) }}', oldBrgy='{{ old("barangay_id", $customer->barangay_id) }}';
        function fill(s,data,ph,selected){s.innerHTML=`<option value="">${ph}</option>`;data.forEach(i=>{const o=document.createElement('option');o.value=i.id;o.textContent=i.name;if(String(i.id)===String(selected))o.selected=true;s.appendChild(o);});s.disabled=false;}
        function loadChain(){
            if(r.value){
                fetch(`/api/provinces/${r.value}`).then(r=>r.json()).then(d=>{fill(p,d,'Select Province',oldProv);
                    if(p.value){fetch(`/api/cities/${p.value}`).then(r=>r.json()).then(d=>{fill(c,d,'Select City',oldCity);
                        if(c.value){fetch(`/api/barangays/${c.value}`).then(r=>r.json()).then(d=>fill(b,d,'Select Barangay',oldBrgy));}
                    });}
                });
            }
        }
        loadChain();
        r.addEventListener('change',function(){p.innerHTML='<option value="">Select Province</option>';p.disabled=true;c.innerHTML='<option value="">Select City</option>';c.disabled=true;b.innerHTML='<option value="">Select Barangay</option>';b.disabled=true;if(this.value)fetch(`/api/provinces/${this.value}`).then(r=>r.json()).then(d=>fill(p,d,'Select Province',''));});
        p.addEventListener('change',function(){c.innerHTML='<option value="">Select City</option>';c.disabled=true;b.innerHTML='<option value="">Select Barangay</option>';b.disabled=true;if(this.value)fetch(`/api/cities/${this.value}`).then(r=>r.json()).then(d=>fill(c,d,'Select City',''));});
        c.addEventListener('change',function(){b.innerHTML='<option value="">Select Barangay</option>';b.disabled=true;if(this.value)fetch(`/api/barangays/${this.value}`).then(r=>r.json()).then(d=>fill(b,d,'Select Barangay',''));});
    });
    </script>
</x-app-layout>
