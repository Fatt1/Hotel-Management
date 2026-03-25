import { cleanRoomApi } from '../../api';

let currentMenuRoom = null;

function showRoomMenu(event, element) {
  event.stopPropagation();
  event.preventDefault();
  
  const menu = document.getElementById('room-menu');
  const roomId = element.dataset.roomId;
  const roomName = element.dataset.roomName;
  const roomStatus = element.dataset.roomStatus;
  const bookingId = element.dataset.bookingId;
  const customerName = element.dataset.customerName;
  
  // Hide menu first to reset height calculation
  menu.style.display = 'none';
  
  // Update header
  const badge = document.getElementById('menu-room-badge');
  const nameEl = document.getElementById('menu-room-name');
  const customerEl = document.getElementById('menu-customer-name');
  
  badge.textContent = roomName;
  nameEl.textContent = getStatusLabel(roomStatus);
  customerEl.textContent = customerName || '';
  customerEl.style.display = customerName ? 'block' : 'none';
  
  // Set badge color based on status
  const statusColors = {
    'available': 'bg-green-500 text-white',
    'reserved': 'bg-blue-500 text-white',
    'arriving': 'bg-purple-500 text-white',
    'occupied': 'bg-red-500 text-white',
    'late_checkout': 'bg-orange-500 text-white',
    'dirty': 'bg-gray-500 text-white'
  };
  badge.className = 'px-2 py-1 rounded-lg text-xs font-black ' + (statusColors[roomStatus] || '');
  
  // Hide all menu items first
  document.getElementById('menu-checkin').style.display = 'none';
  document.getElementById('menu-checkout').style.display = 'none';
  document.getElementById('menu-book').style.display = 'none';
  document.getElementById('menu-clean').style.display = 'none';
  document.getElementById('menu-details').style.display = 'none';
  
  // Show menu items based on status
  if (roomStatus === 'reserved' || roomStatus === 'arriving') {
    // Đã đặt / Sắp đến: Check-in và Chi tiết
    document.getElementById('menu-checkin').style.display = 'flex';
    document.getElementById('menu-details').style.display = 'flex';
    document.getElementById('menu-checkin').href = `/admin/bookings/${bookingId}/checkin`;
    document.getElementById('menu-details').href = `/admin/bookings/${bookingId}/edit`;
  } else if (roomStatus === 'occupied' || roomStatus === 'late_checkout') {
    // Đang ở / Chưa đi: Checkout và Chi tiết
    document.getElementById('menu-checkout').style.display = 'flex';
    document.getElementById('menu-details').style.display = 'flex';
    document.getElementById('menu-checkout').href = `/admin/bookings/${bookingId}/checkout`;
    document.getElementById('menu-details').href = `/admin/bookings/${bookingId}/edit`;
  } else if (roomStatus === 'available') {
    // Trống: Đặt phòng
    document.getElementById('menu-book').style.display = 'flex';
    document.getElementById('menu-book').href = `/admin/bookings/create?room=${roomId}`;
  } else if (roomStatus === 'dirty') {
    // Bẩn: Làm sạch
    document.getElementById('menu-clean').style.display = 'flex';
    document.getElementById('menu-clean').dataset.roomId = roomId;
    document.getElementById('menu-clean').href = '#';
  }
  
  // Reset position to top-left before showing to avoid old position affecting calculation
  menu.style.top = '0px';
  menu.style.left = '0px';
  
  // Show menu first to calculate correct dimensions
  menu.style.display = 'block';
  menu.style.visibility = 'hidden'; // Hide while positioning
  
  // Force reflow to get correct dimensions after content change
  menu.offsetHeight;
  
  // Position menu - use fixed positioning (no scroll offset needed)
  const rect = element.getBoundingClientRect();
  const menuRect = menu.getBoundingClientRect();
  
  // Default position: to the right of the room card
  let top = rect.top;
  let left = rect.right + 10;
  
  // Check if menu would go off-screen on the right
  if (left + menuRect.width > window.innerWidth) {
    // Position to the left of the room card instead
    left = rect.left - menuRect.width - 10;
  }
  
  // Ensure menu doesn't go off left edge
  if (left < 10) {
    left = 10;
  }
  
  // Check if menu would go off-screen at the bottom
  if (top + menuRect.height > window.innerHeight) {
    // Align bottom of menu with bottom of room card
    top = rect.bottom - menuRect.height;
  }
  
  // Ensure menu doesn't go off top edge
  if (top < 10) {
    top = 10;
  }
  
  // Apply position
  menu.style.top = top + 'px';
  menu.style.left = left + 'px';
  menu.style.visibility = 'visible'; // Show after positioning
  
  currentMenuRoom = element;
}

function hideRoomMenu() {
  const menu = document.getElementById('room-menu');
  if (menu) {
    menu.style.display = 'none';
    menu.style.visibility = 'visible'; // Reset visibility
  }
  currentMenuRoom = null;
}

function getStatusLabel(status) {
  const labels = {
    'available': 'Phòng trống',
    'reserved': 'Đã đặt',
    'arriving': 'Sắp đến',
    'occupied': 'Có khách',
    'late_checkout': 'Chưa đi',
    'dirty': 'Phòng bẩn'
  };
  return labels[status] || status;
}

function applyDateFilter() {
  const date = document.getElementById('date-filter').value;
  const urlParams = new URLSearchParams(window.location.search);
  const currentStatus = urlParams.get('status') || '';
  const currentGroupBy = urlParams.get('group_by') || 'type';
  
  let url = window.location.pathname + '?date=' + date;
  if (currentStatus) url += '&status=' + currentStatus;
  if (currentGroupBy !== 'type') url += '&group_by=' + currentGroupBy;
  
  window.location.href = url;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Close menu when clicking outside
  document.addEventListener('click', function(event) {
    const menu = document.getElementById('room-menu');
    if (menu && !menu.contains(event.target) && !event.target.closest('.room-card')) {
      hideRoomMenu();
    }
  });

  // Prevent menu from closing when clicking inside
  const menu = document.getElementById('room-menu');
  if (menu) {
    menu.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  }

  const cleanBtn = document.getElementById('menu-clean');
  if (cleanBtn) {
    cleanBtn.addEventListener('click', async function(event) {
      event.preventDefault();
      const roomId = Number(cleanBtn.dataset.roomId);
      if (!Number.isInteger(roomId) || roomId <= 0) {
        return;
      }

      const confirmed = window.confirm('Đánh dấu phòng này đã dọn xong?');
      if (!confirmed) {
        return;
      }

      try {
        await cleanRoomApi(roomId);
        hideRoomMenu();
        window.location.reload();
      } catch (error) {
        const msg = error.response?.data?.message ?? 'Không thể cập nhật trạng thái phòng.';
        alert(msg);
      }
    });
  }
  
  // Auto-refresh every 60 seconds
  setInterval(() => {
    window.location.reload();
  }, 60000);
});

// Make functions globally available for inline onclick
window.showRoomMenu = showRoomMenu;
window.applyDateFilter = applyDateFilter;
