@extends('layouts.app')

@section('title', 'Profile')

@section('style')
<style>
    /* Animasi untuk modal */
    .modal-enter {
        opacity: 0;
        transform: scale(0.9);
    }
    .modal-enter-active {
        opacity: 1;
        transform: scale(1);
        transition: opacity 300ms, transform 300ms;
    }
    .modal-exit {
        opacity: 1;
    }
    .modal-exit-active {
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 300ms, transform 300ms;
    }
    
    /* Animasi untuk backdrop */
    .backdrop-enter {
        opacity: 0;
    }
    .backdrop-enter-active {
        opacity: 1;
        transition: opacity 300ms;
    }
    .backdrop-exit {
        opacity: 1;
    }
    .backdrop-exit-active {
        opacity: 0;
        transition: opacity 300ms;
    }
    
    /* Animasi untuk notifikasi */
    .notification-enter {
        opacity: 0;
        transform: translateY(20px);
    }
    .notification-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 300ms, transform 300ms;
    }
    .notification-exit {
        opacity: 1;
    }
    .notification-exit-active {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 300ms, transform 300ms;
    }
    
    /* Fix for the modal overlay */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(2px);
    }
</style>
@endsection

@section('content')
<div class="bg-gray-100 py-4 font-medium ml-4">
    <!-- Breadcrumb (Home > Notifikasi > Pesanan) -->
    <div class="container mx-auto px-4">
        <nav class="flex text-sm text-gray-600">
            <a href="/" class="hover:text-[#003366]">Home</a>
            <span class="mx-2">></span>
            <span class="text-gray-900">Profile</span>
        </nav>
    </div>
</div>

<div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6 mt-4">
    <!-- Profile Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center">
            <img src="images/profile.png" alt="Profile Picture" class="w-20 h-20 rounded-full mr-5 object-cover">
            <div>
                <h1 id="profileName" class="text-xl font-bold">Loading...</h1>
                <p id="profileEmail" class="text-gray-600 text-sm">Loading...</p>
            </div>
        </div>
        <button id="saveProfileBtn" class="bg-blue-900 text-white font-bold py-2 px-4 rounded">
            Simpan Perubahan
        </button>
    </div>
    
    <!-- Main Content -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Account Information Section -->
        <div class="w-full md:w-1/2">
            <h2 class="text-lg font-medium mb-4">Account Information</h2>
            
            <div class="mb-4">
                <label class="block text-gray-600 text-sm mb-1">Display name</label>
                <input id="displayName" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-600 text-sm mb-1">Email</label>
                <input id="email" type="email" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
        </div>
        
        <!-- Password Reset Section -->
        <div class="w-full md:w-1/2">
            <h2 class="text-lg font-medium mb-4">Reset Password</h2>
            
            <div class="mb-4">
                <label class="block text-gray-600 text-sm mb-1">Old Password</label>
                <input id="oldPassword" type="password" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-600 text-sm mb-1">New Password</label>
                <input id="newPassword" type="password" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-600 text-sm mb-1">Confirm New Password</label>
                <input id="newPasswordConfirmation" type="password" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <button id="updatePasswordBtn" class="bg-blue-900 text-white font-bold py-2 px-4 rounded">
                Update Password
            </button>
        </div>
    </div>
    
    <!-- Address Section -->
    <div class="border border-gray-300 p-5 mt-6">
        <h2 class="text-lg font-medium mb-5">Alamat Anda</h2>
        
        <!-- Address container - will be populated by JavaScript -->
        <div id="addressContainer">
            <div class="text-center py-4">
                <p>Loading addresses...</p>
            </div>
        </div>
        
        <!-- Add Address Button -->
        <button id="addAddressBtn" class="w-full flex items-center justify-center border border-gray-300 rounded p-3 mt-5 text-gray-600">
            <span class="mr-2">+</span> Tambah Alamat
        </button>
    </div>
    
    <!-- Logout Button -->
    <div class="mt-5 text-right">
        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 text-white font-bold py-2 px-5 rounded">
                Logout
            </button>
        </form>
    </div>
</div>

<!-- Address Modal (Used for both Add and Edit) -->
<div id="addressModal" class="fixed inset-0 modal-overlay flex items-center justify-center hidden z-50 opacity-0 transition-opacity duration-300">
    <div id="modalContent" class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 transform scale-90 transition-transform duration-300">
        <h2 id="modalTitle" class="text-xl font-bold mb-6 pb-2 border-b border-gray-200">Alamat baru</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Full Name Field -->
            <div>
                <label class="block text-gray-600 text-sm mb-1">Full name</label>
                <input id="fullName" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- Phone Field -->
            <div>
                <label class="block text-gray-600 text-sm mb-1">Phone</label>
                <input id="phone" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- Address Field -->
            <div class="md:col-span-2">
                <label class="block text-gray-600 text-sm mb-1">Address</label>
                <input id="address" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- Subdistrict Field -->
            <div>
                <label class="block text-gray-600 text-sm mb-1">Kecamatan</label>
                <input id="subdistrict" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- City Field -->
            <div>
                <label class="block text-gray-600 text-sm mb-1">Kota</label>
                <input id="city" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- Province Field -->
            <div>
                <label class="block text-gray-600 text-sm mb-1">Provinsi</label>
                <input id="province" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- Postcode Field -->
            <div>
                <label class="block text-gray-600 text-sm mb-1">Kode Pos</label>
                <input id="postcode" type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-50">
            </div>
            
            <!-- Address Type -->
            <div class="md:col-span-2 flex gap-4 mt-2">
                <button data-label="Utama" class="address-type-btn border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-blue-300">Utama</button>
                <button data-label="Kantor" class="address-type-btn border border-gray-300 px-4 py-2 rounded focus:ring-2 focus:ring-blue-300">Kantor</button>
            </div>
        </div>
        
        <!-- Submit & Cancel Buttons -->
        <div class="flex justify-end mt-6">
            <button id="closeModalBtn" class="text-gray-600 mr-4">Batal</button>
            <button id="saveAddressBtn" class="bg-blue-900 text-white font-bold py-2 px-4 rounded">Simpan</button>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notificationContainer" class="fixed bottom-4 right-4 z-50"></div>
@endsection

@section('scripts')
<script>
// Global variables
let currentAddressId = null;
let selectedAddressLabel = 'Utama';
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const apiToken = '{{ session("api_token") }}';

// DOM elements
const modal = document.getElementById('addressModal');
const modalContent = document.getElementById('modalContent');
const modalTitle = document.getElementById('modalTitle');
const addAddressBtn = document.getElementById('addAddressBtn');
const closeModalBtn = document.getElementById('closeModalBtn');
const saveProfileBtn = document.getElementById('saveProfileBtn');
const updatePasswordBtn = document.getElementById('updatePasswordBtn');
const notificationContainer = document.getElementById('notificationContainer');
const addressTypeBtns = document.querySelectorAll('.address-type-btn');
const addressContainer = document.getElementById('addressContainer');

// Profile form elements
const profileName = document.getElementById('profileName');
const profileEmail = document.getElementById('profileEmail');
const displayName = document.getElementById('displayName');
const email = document.getElementById('email');
const oldPassword = document.getElementById('oldPassword');
const newPassword = document.getElementById('newPassword');
const newPasswordConfirmation = document.getElementById('newPasswordConfirmation');

// Address form elements
const fullName = document.getElementById('fullName');
const phone = document.getElementById('phone');
const address = document.getElementById('address');
const subdistrict = document.getElementById('subdistrict');
const city = document.getElementById('city');
const province = document.getElementById('province');
const postcode = document.getElementById('postcode');
const saveAddressBtn = document.getElementById('saveAddressBtn');

// ===========================
// API Functions
// ===========================

// Fetch user profile data
async function fetchUserProfile() {
    try {
        const response = await fetch('/api/profile', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Update profile display
            profileName.textContent = data.data.nama;
            profileEmail.textContent = data.data.email;
            
            // Update form fields
            displayName.value = data.data.nama;
            email.value = data.data.email;
        } else {
            showNotification('Failed to load profile data', 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Update user profile
async function updateUserProfile() {
    try {
        const response = await fetch('/api/profile/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                nama: displayName.value,
                email: email.value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            // Update profile display
            profileName.textContent = data.data.nama;
            profileEmail.textContent = data.data.email;
        } else {
            if (data.errors) {
                // Format validation errors
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showNotification('Error: ' + errorMessages, 'error');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Update user password
async function updatePassword() {
    try {
        const response = await fetch('/api/profile/update-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                old_password: oldPassword.value,
                new_password: newPassword.value,
                new_password_confirmation: newPasswordConfirmation.value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            // Clear password fields
            oldPassword.value = '';
            newPassword.value = '';
            newPasswordConfirmation.value = '';
        } else {
            if (data.errors) {
                // Format validation errors
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showNotification('Error: ' + errorMessages, 'error');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Fetch user addresses
async function fetchAddresses() {
    try {
        const response = await fetch('/api/alamat', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.success) {
            renderAddresses(data.data);
        } else {
            showNotification('Failed to load addresses', 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Create new address
async function createAddress() {
    try {
        const response = await fetch('/api/alamat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                label: selectedAddressLabel,
                alamat_lengkap: address.value,
                kecamatan: subdistrict.value,
                kota: city.value,
                provinsi: province.value,
                kode_pos: postcode.value,
                nomor_hp: phone.value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            closeModal();
            fetchAddresses(); // Refresh address list
        } else {
            if (data.errors) {
                // Format validation errors
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showNotification('Error: ' + errorMessages, 'error');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Update existing address
async function updateAddress(id) {
    try {
        const response = await fetch(`/api/alamat/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                label: selectedAddressLabel,
                alamat_lengkap: address.value,
                kecamatan: subdistrict.value,
                kota: city.value,
                provinsi: province.value,
                kode_pos: postcode.value,
                nomor_hp: phone.value
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            closeModal();
            fetchAddresses(); // Refresh address list
        } else {
            if (data.errors) {
                // Format validation errors
                const errorMessages = Object.values(data.errors).flat().join(', ');
                showNotification('Error: ' + errorMessages, 'error');
            } else {
                showNotification('Error: ' + data.message, 'error');
            }
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Delete address
async function deleteAddress(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus alamat ini?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/alamat/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message);
            fetchAddresses(); // Refresh address list
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// ===========================
// UI Functions
// ===========================

// Render addresses in the address container
function renderAddresses(addresses) {
    if (!addresses || addresses.length === 0) {
        addressContainer.innerHTML = `
            <div class="text-center py-4">
                <p>No addresses found. Add your first address.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    addresses.forEach((alamat, index) => {
        const isMainAddress = alamat.label === 'Utama';
        const borderClass = index < addresses.length - 1 ? 'border-b border-gray-200' : '';
        
        html += `
            <div class="py-4 ${borderClass}">
                ${isMainAddress ? '<span class="inline-block bg-gray-200 px-3 py-1 rounded text-sm mr-2">Utama</span>' : ''}
                
                <div class="flex justify-between mt-1">
                    <div>
                        <span class="font-bold mr-2">${alamat.label}</span>
                        <span class="text-gray-600">${alamat.nomor_hp}</span>
                    </div>
                    <div>
                        <button class="text-blue-900 font-bold edit-address-btn mr-2" data-id="${alamat.id}" data-label="${alamat.label}">Ubah</button>
                        <button class="text-red-500 font-bold delete-address-btn" data-id="${alamat.id}">Hapus</button>
                    </div>
                </div>
                
                <p class="text-gray-600 text-sm mt-1">${alamat.alamat_lengkap}, ${alamat.kecamatan}, ${alamat.kota}, ${alamat.provinsi}, ${alamat.kode_pos}</p>
            </div>
        `;
    });
    
    addressContainer.innerHTML = html;
    
    // Add event listeners to the edit buttons
    document.querySelectorAll('.edit-address-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const addressId = this.getAttribute('data-id');
            const addressLabel = this.getAttribute('data-label');
            editAddress(addressId, addressLabel);
        });
    });
    
    // Add event listeners to the delete buttons
    document.querySelectorAll('.delete-address-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const addressId = this.getAttribute('data-id');
            deleteAddress(addressId);
        });
    });
}

// Edit address - fetch address data and open modal
async function editAddress(id, label) {
    currentAddressId = id;
    
    try {
        const response = await fetch(`/api/alamat/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        
        if (data.success) {
            const alamat = data.data;
            
            // Fill form fields
            fullName.value = alamat.label; // This isn't directly mapped in your schema but using label for the name
            phone.value = alamat.nomor_hp;
            address.value = alamat.alamat_lengkap;
            subdistrict.value = alamat.kecamatan;
            city.value = alamat.kota;
            province.value = alamat.provinsi;
            postcode.value = alamat.kode_pos;
            
            // Set address type
            addressTypeBtns.forEach(btn => {
                if (btn.getAttribute('data-label') === alamat.label) {
                    btn.click();
                }
            });
            
            // Open modal
            openModal(true);
        } else {
            showNotification('Failed to load address data', 'error');
        }
    } catch (error) {
        showNotification('Error: ' + error.message, 'error');
    }
}

// Function to show notification
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-4 py-2 rounded shadow-lg transform transition-all duration-300 mb-2 opacity-0 translate-y-4`;
    notification.textContent = message;
    
    // Add to container
    notificationContainer.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.classList.remove('opacity-0', 'translate-y-4');
    }, 10);
    
    // Remove after delay
    setTimeout(() => {
        notification.classList.add('opacity-0', 'translate-y-4');
        setTimeout(() => {
            notificationContainer.removeChild(notification);
        }, 300);
    }, 3000);
}

// Function to open modal with animation
function openModal(isEdit = false) {
    // Update modal title based on mode
    modalTitle.textContent = isEdit ? 'Edit alamat' : 'Alamat baru';
    
    // If not editing, clear form fields
    if (!isEdit) {
        fullName.value = '';
        phone.value = '';
        address.value = '';
        subdistrict.value = '';
        city.value = '';
        province.value = '';
        postcode.value = '';
        
        // Reset address type
        addressTypeBtns[0].click(); // Select first button (Utama) by default
        currentAddressId = null;
    }
    
    // First make the modal visible but transparent
    modal.classList.remove('hidden');
    
    // Trigger reflow to make sure the transition works
    void modal.offsetWidth;
    
    // Fade in the backdrop
    modal.classList.add('opacity-100');
    modal.classList.remove('opacity-0');
    
    // Scale up the modal content
    modalContent.classList.add('scale-100');
    modalContent.classList.remove('scale-90');
}

// Function to close modal with animation
function closeModal() {
    // Fade out the backdrop
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    
    // Scale down the modal content
    modalContent.classList.remove('scale-100');
    modalContent.classList.add('scale-90');
    
    // Hide the modal after the animation completes
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// ===========================
// Event Listeners
// ===========================

// Handle address type button clicks
addressTypeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        addressTypeBtns.forEach(b => {
            b.classList.remove('bg-blue-100');
            b.classList.remove('border-blue-500');
        });
        
        // Add active class to clicked button
        btn.classList.add('bg-blue-100');
        btn.classList.add('border-blue-500');
        
        // Store selected label
        selectedAddressLabel = btn.getAttribute('data-label');
    });
});

// Add address button click handler
addAddressBtn.addEventListener('click', () => openModal(false));

// Close modal button click handler
closeModalBtn.addEventListener('click', closeModal);

// Close modal when clicking outside of it
modal.addEventListener('click', (e) => {
    if (e.target === modal) {
        closeModal();
    }
});

// Save profile button click handler
saveProfileBtn.addEventListener('click', updateUserProfile);

// Update password button click handler
updatePasswordBtn.addEventListener('click', updatePassword);

// Save address button click handler
saveAddressBtn.addEventListener('click', () => {
    if (currentAddressId) {
        updateAddress(currentAddressId);
    } else {
        createAddress();
    }
});

// Initialize page
document.addEventListener('DOMContentLoaded', () => {
    // Load user profile
    fetchUserProfile();
    
    // Load addresses
    fetchAddresses();
    
    // Set default address type
    addressTypeBtns[0].click(); // Select first button (Utama) by default
});
</script>
@endsection