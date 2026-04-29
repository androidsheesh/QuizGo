<x-layout>
    <div class="flex min-h-screen bg-[#F9FAFB]">
        <x-sidebar/>
        <x-dropdown-profile/>

        <main class="flex-1 flex justify-center py-12 overflow-y-auto">
            <div class="w-full max-w-3xl px-6"
                 x-data="{
                    firstname: '{{ addslashes($user->firstname) }}',
                    lastname: '{{ addslashes($user->lastname) }}',
                    email: '{{ addslashes($user->email) }}',
                    bio: `{{ addslashes($user->bio ?? '') }}`,
                    avatarPreview: '{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : '' }}',
                    showDeleteModal: false,
                    confirmText: '',
                    saved: {{ session('success') ? 'true' : 'false' }},
                 }">

                {{-- Success toast --}}
                <div x-show="saved" x-init="if(saved) setTimeout(() => saved = false, 3000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4"
                     class="fixed top-6 left-1/2 -translate-x-1/2 z-[60] bg-emerald-500 text-white px-6 py-3 rounded-2xl shadow-lg font-semibold text-sm flex items-center gap-2">
                    <span>✓</span> Profile updated successfully
                </div>

                {{-- Header Section: Avatar + Name/Email --}}
                <div class="flex flex-col items-center md:flex-row md:space-x-8 mb-12 text-center md:text-left">
                    <div class="relative mb-4 md:mb-0">
                        <div class="w-32 h-32 bg-emerald-100 rounded-full flex items-center justify-center overflow-hidden border-4 border-white shadow-md">
                            <template x-if="avatarPreview">
                                <img :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!avatarPreview">
                                <span class="text-6xl">👤</span>
                            </template>
                        </div>
                        <button type="button" @click="$refs.avatarInput.click()"
                                class="absolute bottom-0 right-0 bg-white border border-gray-200 p-2 rounded-full shadow-sm hover:bg-gray-50 hover:scale-110 transition-all">
                            📷
                        </button>
                    </div>
                    <div>
                        <h2 class="text-4xl font-bold text-slate-800" x-text="firstname + ' ' + lastname"></h2>
                        <p class="text-slate-400 text-lg mt-1" x-text="email"></p>
                    </div>
                </div>

                <hr class="border-gray-100 mb-10">

                <div class="space-y-10">
                    {{-- Account Details Card --}}
                    <section>
                        <h3 class="text-xl font-bold text-slate-700 mb-6 flex items-center gap-2">
                            <span>⚙️</span> Account Details
                        </h3>

                        <form method="POST" action="{{ route('myprofile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- Hidden file input for avatar --}}
                            <input type="file" name="profile_picture" accept="image/*" class="hidden" x-ref="avatarInput"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           const reader = new FileReader();
                                           reader.onload = (e) => avatarPreview = e.target.result;
                                           reader.readAsDataURL(file);
                                       }
                                   ">

                            <div class="bg-white border border-gray-200 rounded-[2.5rem] p-8 md:p-10 shadow-sm">
                                <div class="space-y-8">
                                    {{-- First Name --}}
                                    <div>
                                        <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">First Name</label>
                                        <div class="relative group">
                                            <input type="text" name="firstname" x-model="firstname"
                                                   class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                                        </div>
                                        @error('firstname')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Last Name --}}
                                    <div>
                                        <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Last Name</label>
                                        <div class="relative group">
                                            <input type="text" name="lastname" x-model="lastname"
                                                   class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                                        </div>
                                        @error('lastname')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div>
                                        <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Email Address</label>
                                        <div class="relative group">
                                            <input type="email" name="email" x-model="email"
                                                   class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all">
                                        </div>
                                        @error('email')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Bio --}}
                                    <div>
                                        <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Bio</label>
                                        <div class="relative group">
                                            <textarea rows="3" name="bio" x-model="bio" placeholder="Tell us about yourself..."
                                                      class="w-full p-4 bg-slate-50 border border-slate-100 rounded-2xl text-slate-600 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-400/30 focus:border-emerald-300 transition-all resize-none"></textarea>
                                        </div>
                                        @error('bio')
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Save Button --}}
                                <div class="mt-8 flex justify-end">
                                    <button type="submit"
                                            class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-2xl shadow-md hover:bg-emerald-600 hover:shadow-lg active:scale-95 transition-all">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </section>

                    {{-- Danger Zone --}}
                    <button type="button" @click="showDeleteModal = true; confirmText = ''"
                            class="w-full flex justify-between items-center bg-white border border-red-100 p-6 rounded-[2rem] shadow-sm hover:bg-red-50 transition-all group">
                        <div class="flex items-center space-x-4">
                            <div class="bg-red-50 border border-red-100 p-3 rounded-xl">
                                <span class="text-red-500">🗑️</span>
                            </div>
                            <div class="text-left">
                                <span class="block text-lg font-bold text-red-600">Delete Account</span>
                                <span class="text-sm text-red-400">This action is permanent and cannot be undone.</span>
                            </div>
                        </div>
                        <span class="text-2xl text-red-300 group-hover:translate-x-1 group-hover:text-red-500 transition-all">»</span>
                    </button>
                </div>

                {{-- ===== DELETE CONFIRMATION MODAL ===== --}}
                <div x-show="showDeleteModal" x-cloak
                     class="fixed inset-0 z-[70] flex items-center justify-center"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">

                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showDeleteModal = false"></div>

                    {{-- Modal Card --}}
                    <div class="relative bg-white rounded-[2rem] shadow-2xl w-full max-w-md mx-4 p-8 md:p-10"
                         x-transition:enter="transition ease-out duration-200 delay-75"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">

                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-red-100">
                                <span class="text-3xl">⚠️</span>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800">Delete Your Account?</h3>
                            <p class="text-slate-400 mt-2 text-sm leading-relaxed">
                                This will <strong class="text-red-500">permanently delete</strong> your account, all your decks, flashcards, and data. This action cannot be undone.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('myprofile.destroy') }}">
                            @csrf
                            @method('DELETE')

                            <div class="mb-6">
                                <label class="block text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">
                                    Type <span class="text-red-500 font-black">CONFIRM</span> to delete
                                </label>
                                <input type="text" name="confirmation" x-model="confirmText" autocomplete="off" placeholder="CONFIRM"
                                       class="w-full p-4 bg-red-50/50 border border-red-200 rounded-2xl text-slate-700 font-mono font-bold text-center text-lg tracking-widest focus:outline-none focus:ring-2 focus:ring-red-300 focus:border-red-400 transition-all">
                            </div>

                            @error('confirmation')
                                <p class="text-red-500 text-sm mb-4 text-center">{{ $message }}</p>
                            @enderror

                            <div class="flex gap-3">
                                <button type="button" @click="showDeleteModal = false"
                                        class="flex-1 py-3 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all">
                                    Cancel
                                </button>
                                <button type="submit"
                                        :disabled="confirmText !== 'CONFIRM'"
                                        :class="confirmText === 'CONFIRM'
                                            ? 'bg-red-500 text-white hover:bg-red-600 shadow-md hover:shadow-lg'
                                            : 'bg-red-200 text-red-300 cursor-not-allowed'"
                                        class="flex-1 py-3 font-bold rounded-2xl transition-all">
                                    Delete Forever
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </main>
    </div>
</x-layout>
