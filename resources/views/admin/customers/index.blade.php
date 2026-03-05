@extends("layouts.admin")
@section("content")
<div class="p-8 space-y-6">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
<div>
<h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Quản lý khách hàng</h1>
<p class="text-slate-500 dark:text-slate-400 font-medium">Hệ thống quản trị khách sạn Urban Luxe - Quản lý danh sách khách hàng chuyên nghiệp.</p>
</div>
<button class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl text-sm font-bold shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all hover:-translate-y-0.5">
<span class="material-symbols-outlined">person_add</span>
                    Thêm khách hàng mới
                </button>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
<div class="p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col xl:flex-row gap-4 justify-between items-center">
<div class="relative w-full xl:w-96">
<span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
<span class="material-symbols-outlined !text-lg">search</span>
</span>
<input class="block w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tìm theo Tên/Email/SĐT..." type="text"/>
</div>
<div class="flex flex-wrap items-center gap-3 w-full xl:w-auto">
<select class="block w-full sm:w-48 px-4 py-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
<option value="">Quốc gia (Tất cả)</option>
<option value="VN">Việt Nam</option>
<option value="US">Hoa Kỳ</option>
<option value="UK">Anh Quốc</option>
<option value="JP">Nhật Bản</option>
<option value="KR">Hàn Quốc</option>
</select>
<button class="p-2 text-slate-400 hover:text-primary transition-colors">
<span class="material-symbols-outlined">filter_list</span>
</button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead>
<tr class="bg-slate-50/50 dark:bg-slate-800/30">
<th class="table-header">ID</th>
<th class="table-header">Họ</th>
<th class="table-header">Tên</th>
<th class="table-header">Số điện thoại</th>
<th class="table-header">ID tài khoản</th>
<th class="table-header">Quốc gia</th>
<th class="table-header">Email</th>
<th class="table-header text-right">Thao tác</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group">
<td class="table-cell">
<span class="font-bold text-primary">CUS-1001</span>
</td>
<td class="table-cell">Nguyễn</td>
<td class="table-cell font-semibold text-slate-900 dark:text-white">Minh Anh</td>
<td class="table-cell">0901234567</td>
<td class="table-cell">ACC-9901</td>
<td class="table-cell">Việt Nam</td>
<td class="table-cell">minhanh.n@gmail.com</td>
<td class="table-cell text-right">
<div class="flex items-center justify-end gap-1">
<button class="action-btn text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Xem chi tiết">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="action-btn text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20" title="Chỉnh sửa">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="action-btn text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20" onclick="toggleModal('delete-modal')" title="Xóa">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group">
<td class="table-cell">
<span class="font-bold text-primary">CUS-1002</span>
</td>
<td class="table-cell">Smith</td>
<td class="table-cell font-semibold text-slate-900 dark:text-white">John</td>
<td class="table-cell">+1 202-555-0143</td>
<td class="table-cell">ACC-9902</td>
<td class="table-cell">Hoa Kỳ</td>
<td class="table-cell">john.smith@example.com</td>
<td class="table-cell text-right">
<div class="flex items-center justify-end gap-1">
<button class="action-btn text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Xem chi tiết">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="action-btn text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20" title="Chỉnh sửa">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="action-btn text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20" onclick="toggleModal('delete-modal')" title="Xóa">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group">
<td class="table-cell">
<span class="font-bold text-primary">CUS-1003</span>
</td>
<td class="table-cell">Lê</td>
<td class="table-cell font-semibold text-slate-900 dark:text-white">Quang Hải</td>
<td class="table-cell">0912888999</td>
<td class="table-cell">ACC-9903</td>
<td class="table-cell">Việt Nam</td>
<td class="table-cell">hai.lq@vnn.vn</td>
<td class="table-cell text-right">
<div class="flex items-center justify-end gap-1">
<button class="action-btn text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Xem chi tiết">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="action-btn text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20" title="Chỉnh sửa">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="action-btn text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20" onclick="toggleModal('delete-modal')" title="Xóa">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-colors group">
<td class="table-cell">
<span class="font-bold text-primary">CUS-1004</span>
</td>
<td class="table-cell">Sato</td>
<td class="table-cell font-semibold text-slate-900 dark:text-white">Yuki</td>
<td class="table-cell">+81 90-1234-5678</td>
<td class="table-cell">ACC-9904</td>
<td class="table-cell">Nhật Bản</td>
<td class="table-cell">yuki.sato@jp.co</td>
<td class="table-cell text-right">
<div class="flex items-center justify-end gap-1">
<button class="action-btn text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20" title="Xem chi tiết">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="action-btn text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20" title="Chỉnh sửa">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="action-btn text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20" onclick="toggleModal('delete-modal')" title="Xóa">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
<span class="text-xs font-medium text-slate-500">Hiển thị 4 trên 150 khách hàng</span>
<div class="flex items-center gap-1">
<button class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed" disabled="">
<span class="material-symbols-outlined !text-lg">chevron_left</span>
</button>
<button class="w-8 h-8 rounded-lg bg-primary text-white text-xs font-bold">1</button>
<button class="w-8 h-8 rounded-lg text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800">2</button>
<button class="w-8 h-8 rounded-lg text-xs font-bold hover:bg-slate-100 dark:hover:bg-slate-800">3</button>
<button class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800">
<span class="material-symbols-outlined !text-lg">chevron_right</span>
</button>
</div>
</div>
</div>
</div> 
@endsection
