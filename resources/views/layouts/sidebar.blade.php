<aside class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
      <ul class="space-y-2 font-medium">
         <li>
            <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-orange-50 text-orange-600' : '' }}">
               <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                  <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                  <path d="M12.5 0c-.157 0-.311.01-.462.03a1 1 0 0 0-.815.815c-.019.151-.029.305-.029.462V11h8.5a1 1 0 0 0 .997-1.066 8.5 8.5 0 0 0-8.191-8.191Z"/>
               </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         
         <li class="pt-4 pb-2">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Academic</span>
         </li>

         <li>
            <a href="{{ route('students.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('students.*') ? 'bg-orange-50 text-orange-600 font-bold' : '' }}">
               <i class="fas fa-user-graduate w-5 {{ request()->routeIs('students.*') ? 'text-orange-600' : 'text-gray-500' }}"></i>
               <span class="flex-1 ms-3 white-space-nowrap">Students</span>
            </a>
         </li>

         <li>
            <a href="#" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
               <i class="fas fa-book w-5 text-gray-500"></i>
               <span class="flex-1 ms-3 white-space-nowrap">Courses</span>
            </a>
         </li>

         <li class="pt-4 pb-2">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Administration</span>
         </li>

         <li>
            <a href="{{ route('staff.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('staff.*') ? 'bg-blue-50 text-blue-600 font-bold' : '' }}">
               <i class="fas fa-users w-5 {{ request()->routeIs('staff.*') ? 'text-blue-600' : 'text-gray-500' }}"></i>
               <span class="flex-1 ms-3 white-space-nowrap">Staff</span>
            </a>
         </li>

         <li>
            <a href="{{ route('attendance.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('attendance.*') ? 'bg-purple-50 text-purple-600 font-bold' : '' }}">
               <i class="fas fa-calendar-check w-5 {{ request()->routeIs('attendance.*') ? 'text-purple-600' : 'text-gray-500' }}"></i>
               <span class="flex-1 ms-3 white-space-nowrap">Attendance</span>
            </a>
         </li>

         <li class="pt-4 pb-2">
            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</span>
         </li>

         <li>
            <a href="{{ route('fee-receipts.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('fee-receipts.*') ? 'bg-green-50 text-green-600 font-bold' : '' }}">
               <i class="fas fa-receipt w-5 {{ request()->routeIs('fee-receipts.*') ? 'text-green-600' : 'text-gray-500' }}"></i>
               <span class="flex-1 ms-3 white-space-nowrap">Fees Receipts</span>
            </a>
         </li>

         <li>
            <a href="{{ route('client-invoices.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('client-invoices.*') ? 'bg-teal-50 text-teal-600 font-bold' : '' }}">
               <i class="fas fa-file-invoice-dollar w-5 {{ request()->routeIs('client-invoices.*') ? 'text-teal-600' : 'text-gray-500' }}"></i>
               <span class="flex-1 ms-3 white-space-nowrap">Client Invoices</span>
            </a>
         </li>
      </ul>
   </div>
</aside>
