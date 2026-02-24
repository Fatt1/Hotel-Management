<!-- Global Modal - Reusable -->
<div id="global-modal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
  aria-modal="true">
  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
    <!-- Backdrop -->
    <div class="fixed inset-0 transition-opacity bg-black opacity-60 backdrop-blur-md" aria-hidden="true">
    </div>
    <!-- Modal Content -->
    <div
      class="relative inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
      <div id="global-modal-content" class="p-5">
        {{ $slot }}
      </div>
    </div>
  </div>
</div>