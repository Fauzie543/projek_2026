
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
                <a href="{{ route('dashboard') }}"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 
                         dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                  role="menuitem">Dashboard</a>
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
  class="fixed top-0 left-0 z-40 w-60 h-screen pt-20 transition-transform -translate-x-full 
         bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
  aria-label="Sidebar">
  <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
    <ul class="space-y-2 font-medium">
      <!-- Dashboard -->
      <li>
        <a href="{{ route('dashboard') }}"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white 
                 hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 
                      group-hover:text-gray-900 dark:group-hover:text-white"
            fill="currentColor" viewBox="0 0 22 21">
            <path d="M16.975 11H10V4.025a1 1 
                     0 0 0-1.066-.998 8.5 8.5 
                     0 1 0 9.039 9.039.999.999 
                     0 0 0-1-1.066h.002Z"/>
            <path d="M12.5 0c-.157 0-.311.01-.565.027A1 
                     1 0 0 0 11 1.02V10h8.975a1 
                     1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 
                     8.51 0 0 0 12.5 0Z"/>
          </svg>
          <span class="ms-3">Dashboard</span>
        </a>
      </li>

      {{-- === Admin Menu === --}}
      @role('admin')
      <li>
        <a href="{{ route('users.index') }}"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white 
                 hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 
                      group-hover:text-gray-900 dark:group-hover:text-white" 
               fill="currentColor" viewBox="0 0 20 20">
            <path d="M14 2a3.963 3.963 0 0 0-1.4.267 
                     6.439 6.439 0 0 1-1.331 
                     6.638A4 4 0 1 0 14 2Z"/>
          </svg>
          <span class="flex-1 ms-3 whitespace-nowrap">Users</span>
        </a>
      </li>
      @endrole

      {{-- === Employees Menu (Admin + Manager) === --}}
      @role('admin')
      <li>
        <a href="{{ route('employees.index') }}"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white 
                 hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 
                      group-hover:text-gray-900 dark:group-hover:text-white"
               fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 0a10 10 0 1 0 10 10A10 
                     10 0 0 0 10 0Z"/>
          </svg>
          <span class="flex-1 ms-3 whitespace-nowrap">Employees</span>
        </a>
      </li>
      @elserole('manager')
      <li>
        <a href="{{ route('employees.index') }}"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white 
                 hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 
                      group-hover:text-gray-900 dark:group-hover:text-white"
               fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 0a10 10 0 1 0 10 10A10 
                     10 0 0 0 10 0Z"/>
          </svg>
          <span class="flex-1 ms-3 whitespace-nowrap">Employees</span>
        </a>
      </li>
      @endrole

      {{-- === Attendance Menu (Admin, Manager, Staff) === --}}
      @can('view attendance')
      <li>
        <a href="{{ route('attendance.index') }}"
          class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white 
                 hover:bg-gray-100 dark:hover:bg-gray-700 group">
          <svg class="shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 
                      group-hover:text-gray-900 dark:group-hover:text-white"
               fill="currentColor" viewBox="0 0 20 20">
            <path d="M6 2a1 1 0 0 0-1 
                     1v14a1 1 0 0 0 1.447.894l12-7a1 
                     1 0 0 0 0-1.788l-12-7A1 1 
                     0 0 0 6 2Z"/>
          </svg>
          <span class="flex-1 ms-3 whitespace-nowrap">Attendance</span>
        </a>
      </li>
      @endcan

    </ul>
  </div>
</aside>

