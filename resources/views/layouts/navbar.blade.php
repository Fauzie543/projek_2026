{{-- resources/views/layouts/navbar.blade.php --}}
<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
  <div class="px-3 py-3 lg:px-5 lg:pl-3">
    <div class="flex items-center justify-between">
      <div class="flex items-center justify-start rtl:justify-end">
        <!-- Toggle Sidebar Button (Mobile) -->
        <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar"
          aria-controls="logo-sidebar" type="button"
          class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 
                 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 
                 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
              <path clip-rule="evenodd" fill-rule="evenodd"
                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 
                   0 010 1.5H2.75A.75.75 0 012 4.75zm0 
                   10.5a.75.75 0 01.75-.75h7.5a.75.75 
                   0 010 1.5h-7.5a.75.75 0 
                   01-.75-.75zM2 10a.75.75 0 
                   01.75-.75h14.5a.75.75 0 
                   010 1.5H2.75A.75.75 0 
                   012 10z"></path>
            </svg>
        </button>
        <!-- Logo / App Name -->
        <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24">
          <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap dark:text-white">
            PROJEK 2026
          </span>
        </a>
      </div>

      <!-- User Profile Dropdown -->
      <div class="flex items-center">
        <div class="flex items-center ms-3">
          <div>
            <button type="button"
              class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 
                     dark:focus:ring-gray-600" aria-expanded="false" data-dropdown-toggle="dropdown-user">
              <span class="sr-only">Open user menu</span>
              <img class="w-8 h-8 rounded-full" 
                   src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}" 
                   alt="user photo">
            </button>
          </div>
          <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 
                       rounded-sm shadow-sm dark:bg-gray-700 dark:divide-gray-600" id="dropdown-user">
            <div class="px-4 py-3" role="none">
              <p class="text-sm text-gray-900 dark:text-white" role="none">{{ Auth::user()->name }}</p>
              <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                {{ Auth::user()->email }}
              </p>
            </div>
            <ul class="py-1" role="none">
              <li>
                  <a href="{{ route('profile.edit') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 
                           dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                    role="menuitem">Profile</a>
              </li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit"
                    class="w-full text-left block px-4 py-2 text-sm text-gray-700 
                           hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                    role="menuitem">Logout</button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>

<!-- Sidebar -->
<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-65 h-screen pt-20 transition-transform -translate-x-full 
           bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/></svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            {{-- Dropdown Manajemen User --}}
            @canany(['manage employees', 'view attendance'])
            <li>
                <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-manajemen" data-collapse-toggle="dropdown-manajemen">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M8 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H6Zm7.25-2.095c.478-.86.75-1.85.75-2.905a5.973 5.973 0 0 0-.75-2.906 4 4 0 1 1 0 5.811ZM15.466 20c.34-.588.535-1.271.535-2v-1a5.978 5.978 0 0 0-1.528-4H18a4 4 0 0 1 4 4v1a2 2 0 0 1-2 2h-4.535Z" clip-rule="evenodd"/></svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Manajemen Pengguna</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                </button>
                <ul id="dropdown-manajemen" class="hidden py-2 space-y-2">
                    @role('admin')
                    <li><a href="{{ route('users.index') }}" class="menu-item">Pengguna</a></li>
                    @endrole
                    @can('manage employees')
                    <li><a href="{{ route('employees.index') }}" class="menu-item">Pegawai</a></li>
                    @endcan
                    @can('view attendance')
                    <li><a href="{{ route('attendance.index') }}" class="menu-item">Absensi</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany

            {{-- =================================================================== --}}
            {{-- PEMBARUAN DIMULAI DARI SINI --}}
            {{-- =================================================================== --}}
            @canany(['manage inventory', 'manage procurement'])
            <li>
                <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-inventory" data-collapse-toggle="dropdown-inventory">
                    <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M4 4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H4Zm10 2a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V6ZM8 6a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0V6Zm6 8a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Zm-4 0a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Z" clip-rule="evenodd"/></svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Inventaris & Pengadaan</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" /></svg>
                </button>

                <ul id="dropdown-inventory" class="hidden py-2 space-y-2">
                    {{-- Sub-menu Data Master --}}
                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-7 group hover:bg-gray-100" aria-controls="dropdown-master" data-collapse-toggle="dropdown-master">
                            <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Data Master</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                        </button>
                        <ul id="dropdown-master" class="hidden py-2 space-y-2">
                            <li><a href="{{ route('inventory.suppliers.index') }}" class="menu-item-sub">Pemasok</a></li>
                            <li><a href="{{ route('inventory.items.index') }}" class="menu-item-sub">Barang Inventaris</a></li>
                        </ul>
                    </li>
                    {{-- Sub-menu Pengadaan --}}
                    @can('manage procurement')
                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-7 group hover:bg-gray-100" aria-controls="dropdown-procurement" data-collapse-toggle="dropdown-procurement">
                            <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Pengadaan</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                        </button>
                        <ul id="dropdown-procurement" class="hidden py-2 space-y-2">
                            <li><a href="{{ route('procurement.purchase-requests.index') }}" class="menu-item-sub">Permintaan Pembelian</a></li>
                            <li><a href="{{ route('procurement.purchase-orders.index') }}" class="menu-item-sub">Pesanan Pembelian</a></li>
                            <li><a href="{{ route('procurement.receivings.index') }}" class="menu-item-sub">Penerimaan Barang</a></li>
                        </ul>
                    </li>
                    @endcan
                    {{-- Sub-menu Manajemen Stok --}}
                    @can('manage inventory')
                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-7 group hover:bg-gray-100" aria-controls="dropdown-stock" data-collapse-toggle="dropdown-stock">
                            <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Manajemen Stok</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                        </button>
                        <ul id="dropdown-stock" class="hidden py-2 space-y-2">
                            <li><a href="{{ route('inventory.stocks.index') }}" class="menu-item-sub">Ringkasan Stok</a></li>
                            <li><a href="{{ route('inventory.stock-batches.index') }}" class="menu-item-sub">Batch Stok</a></li>
                            <li><a href="{{ route('inventory.stock-transactions.index') }}" class="menu-item-sub">Transaksi Stok</a></li>
                        </ul>
                    </li>
                    {{-- Sub-menu Produksi Pakan --}}
                    <li>
                        <button type="button" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-7 group hover:bg-gray-100" aria-controls="dropdown-feed" data-collapse-toggle="dropdown-feed">
                            <span class="flex-1 text-left rtl:text-right whitespace-nowrap">Produksi Pakan</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>
                        </button>
                        <ul id="dropdown-feed" class="hidden py-2 space-y-2">
                             <li><a href="{{ route('inventory.feed-recipes.index') }}" class="menu-item-sub">Resep Pakan</a></li>
                             <li><a href="{{ route('inventory.feed-batches.index') }}" class="menu-item-sub">Batch Produksi</a></li>
                        </ul>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany
        </ul>
    </div>
</aside>

{{-- Style Tambahan untuk Sub-menu --}}
<style>


  
    .menu-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.5rem 2.75rem; /* pl-11 */
        color: #1f2937; /* text-gray-900 */
        transition-duration: 75ms;
        border-radius: 0.5rem; /* rounded-lg */
    }
    .menu-item:hover {
        background-color: #f3f4f6; /* hover:bg-gray-100 */
    }
    .dark .menu-item {
        color: #ffffff; /* dark:text-white */
    }
    .dark .menu-item:hover {
        background-color: #374151; /* dark:hover:bg-gray-700 */
    }
    .menu-item-sub {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 0.5rem 3.75rem; /* pl-15 */
        color: #1f2937;
        transition-duration: 75ms;
        border-radius: 0.5rem;
    }
     .menu-item-sub:hover {
        background-color: #f3f4f6;
    }
    .dark .menu-item-sub {
        color: #ffffff;
    }
    .dark .menu-item-sub:hover {
        background-color: #374151;
    }
</style>

