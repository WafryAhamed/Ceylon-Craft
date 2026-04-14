<template>
  <div class="bg-[#EEF0F7] min-h-screen py-12">
    <!-- PAGE HEADER -->
    <section class="bg-white py-12 md:py-16 border-b-2 border-[#DFE2E9]">
      <div class="max-w-7xl mx-auto px-6">
        <h1 class="text-5xl font-bold text-[#363851] mb-2">
          My Profile
        </h1>
        <p class="text-xl text-[#657691]">
          Manage your account settings and preferences
        </p>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-6 py-12">
      <div class="grid lg:grid-cols-3 gap-8">
        <!-- SIDEBAR NAVIGATION -->
        <aside class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-24">
            <nav class="space-y-1 p-4">
              <button
                v-for="section in sections"
                :key="section.id"
                @click="activeSection = section.id"
                :class="[
                  'w-full text-left px-4 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-3',
                  activeSection === section.id
                    ? 'bg-[#FB2B4A] text-white'
                    : 'text-[#657691] hover:bg-[#EEF0F7]'
                ]"
              >
                <component :is="section.icon" class="w-5 h-5" />
                {{ section.label }}
              </button>
            </nav>
          </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="lg:col-span-2 space-y-8">
          <!-- PERSONAL INFO SECTION -->
          <section v-if="activeSection === 'personal'" class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-3xl font-bold text-[#363851] mb-8">Personal Information</h2>
            <form class="space-y-6">
              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">First Name</label>
                  <input
                    v-model="user.firstName"
                    type="text"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Last Name</label>
                  <input
                    v-model="user.lastName"
                    type="text"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Email Address</label>
                <input
                  v-model="user.email"
                  type="email"
                  disabled
                  class="w-full px-4 py-3 bg-[#DFE2E9] border-2 border-[#A0ACC0] text-[#657691] rounded-lg opacity-60 cursor-not-allowed"
                />
                <p class="text-xs text-[#657691] mt-2">Email cannot be changed</p>
              </div>

              <div class="grid md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Phone Number</label>
                  <input
                    v-model="user.phone"
                    type="tel"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Date of Birth</label>
                  <input
                    v-model="user.dob"
                    type="date"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
              </div>

              <button
                @click="saveChanges('personal')"
                class="px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
              >
                Save Changes
              </button>
            </form>
          </section>

          <!-- ADDRESS SECTION -->
          <section v-if="activeSection === 'address'" class="bg-white rounded-xl shadow-md p-8">
            <h2 class="text-3xl font-bold text-[#363851] mb-8">Shipping Address</h2>
            <form class="space-y-6">
              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Street Address</label>
                <input
                  v-model="address.street"
                  type="text"
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                />
              </div>

              <div class="grid md:grid-cols-3 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">City</label>
                  <input
                    v-model="address.city"
                    type="text"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">State</label>
                  <input
                    v-model="address.state"
                    type="text"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">ZIP Code</label>
                  <input
                    v-model="address.zip"
                    type="text"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-[#363851] mb-2">Country</label>
                <select
                  v-model="address.country"
                  class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                >
                  <option>Select Country</option>
                  <option>United States</option>
                  <option>United Kingdom</option>
                  <option>Canada</option>
                  <option>Australia</option>
                </select>
              </div>

              <button
                @click="saveChanges('address')"
                class="px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
              >
                Save Address
              </button>
            </form>
          </section>

          <!-- SETTINGS SECTION -->
          <section v-if="activeSection === 'settings'" class="space-y-6">
            <!-- PASSWORD -->
            <div class="bg-white rounded-xl shadow-md p-8">
              <h3 class="text-2xl font-bold text-[#363851] mb-6">Change Password</h3>
              <form class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Current Password</label>
                  <input
                    type="password"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">New Password</label>
                  <input
                    type="password"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-[#363851] mb-2">Confirm New Password</label>
                  <input
                    type="password"
                    class="w-full px-4 py-3 bg-[#EEF0F7] border-2 border-[#DFE2E9] text-[#363851] rounded-lg focus:outline-none focus:border-[#FB2B4A] transition"
                  />
                </div>
                <button
                  @click="saveChanges('password')"
                  class="px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
                >
                  Update Password
                </button>
              </form>
            </div>

            <!-- NOTIFICATIONS -->
            <div class="bg-white rounded-xl shadow-md p-8">
              <h3 class="text-2xl font-bold text-[#363851] mb-6">Notification Preferences</h3>
              <div class="space-y-4">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input
                    v-model="preferences.emailNotifications"
                    type="checkbox"
                    class="w-5 h-5 rounded accent-[#FB2B4A]"
                  />
                  <span class="text-[#657691] font-semibold">Receive email updates about orders</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input
                    v-model="preferences.newsletter"
                    type="checkbox"
                    class="w-5 h-5 rounded accent-[#FB2B4A]"
                  />
                  <span class="text-[#657691] font-semibold">Subscribe to our newsletter</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                  <input
                    v-model="preferences.promotions"
                    type="checkbox"
                    class="w-5 h-5 rounded accent-[#FB2B4A]"
                  />
                  <span class="text-[#657691] font-semibold">Receive promotional offers</span>
                </label>
              </div>
              <button
                @click="saveChanges('preferences')"
                class="mt-6 px-8 py-3 bg-[#FB2B4A] hover:bg-[#E91B3D] text-white font-bold rounded-lg transition-all duration-300 shadow-md hover:shadow-lg"
              >
                Save Preferences
              </button>
            </div>
          </section>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

const { success } = useToast();
const activeSection = ref('personal');

const sections = [
  { id: 'personal', label: 'Personal Info', icon: 'svg' },
  { id: 'address', label: 'Address', icon: 'svg' },
  { id: 'settings', label: 'Settings', icon: 'svg' }
];

const user = ref({
  firstName: 'Wafry',
  lastName: 'Ahamed',
  email: 'wafry@ceyloncraft.lk',
  phone: '+1 (555) 000-0000',
  dob: '1995-05-15'
});

const address = ref({
  street: '123 Main Street',
  city: 'New York',
  state: 'NY',
  zip: '10001',
  country: 'United States'
});

const preferences = ref({
  emailNotifications: true,
  newsletter: true,
  promotions: false
});

const saveChanges = (section) => {
  success(`${section} settings saved successfully!`);
};
</script>
