<header
  class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-end px-8 sticky top-0 z-10">
  <div class="flex items-center gap-6">
    <div class="hidden xl:flex flex-col items-end">
      <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Ngày làm việc</span>
      <span class="text-sm font-bold">24 Tháng 05, 2024</span>
    </div>
    <div class="flex gap-2">
      <button class="p-2.5 text-slate-500 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors relative">
        <span class="material-symbols-outlined">notifications</span>
        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
      </button>
      <div class="flex items-center gap-3 ml-2 border-l border-slate-200 dark:border-slate-800 pl-4">
        <div class="flex gap-4">
          <div class="flex flex-col">
            <span class="text-md font-bold uppercase ">Hà Tấn Phát</span>
            <span class="text-sm text-slate-500">Admin</span>
          </div>
          <div class="relative group">
            <div
              class="h-10 w-10 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 cursor-pointer">
              <span class="material-icons-round text-lg">person</span>
            </div>
            <!-- Dropdown Menu -->
            <div
              class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
              <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                  class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                  <span class="material-icons-round text-sm">logout</span>
                  Đăng xuất
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</header>