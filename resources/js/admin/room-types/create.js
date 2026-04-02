let amenityModal;
let equipmentModal;
let amenitiesList;
let amenityGrid;
let amenitySearch;
let equipmentGrid;
let equipmentSearch;
let equipmentTableBody;
let imageGallery;
let imageCount;
let codeInput;

function cacheElements() {
  amenityModal = document.getElementById('amenityModal');
  equipmentModal = document.getElementById('equipmentModal');
  amenitiesList = document.getElementById('amenitiesList');
  amenityGrid = document.getElementById('amenityGrid');
  amenitySearch = document.getElementById('amenitySearch');
  equipmentGrid = document.getElementById('equipmentGrid');
  equipmentSearch = document.getElementById('equipmentSearch');
  equipmentTableBody = document.querySelector('#equipmentTable tbody');
  imageGallery = document.getElementById('imageGallery');
  imageCount = document.getElementById('imageCount');
  codeInput = document.getElementById('codeInput');
}

function generateCode() {
  if (!codeInput) return;
  const randomNum = Math.floor(Math.random() * 90) + 10;
  codeInput.value = 'RT-' + randomNum.toString().padStart(2, '0');
}

function truncateFilename(filename, maxLength = 25) {
  if (filename.length <= maxLength) return filename;
  const ext = filename.split('.').pop();
  const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
  const truncatedName = nameWithoutExt.substring(0, maxLength - ext.length - 4) + '...';
  return truncatedName + '.' + ext;
}

function incrementValue(btn) {
  const input = btn.parentElement.querySelector('input');
  if (!input) return;
  input.value = parseInt(input.value, 10) + 1;
}

function decrementValue(btn) {
  const input = btn.parentElement.querySelector('input');
  if (!input) return;
  const min = parseInt(input.getAttribute('min') ?? '0', 10);
  if (parseInt(input.value, 10) > min) {
    input.value = parseInt(input.value, 10) - 1;
  }
}

function handleImageUpload(event) {
  const files = event.target.files;
  if (!imageGallery || !imageCount) return;

  const placeholder = imageGallery.querySelector('[data-placeholder]');
  if (placeholder) {
    placeholder.remove();
  }

  if (files.length === 0 && imageGallery.querySelectorAll('[data-image-item]').length === 0) {
    imageGallery.innerHTML = `
      <div class="w-full py-16 bg-slate-50 rounded-lg flex items-center justify-center border border-dashed border-slate-300" data-placeholder="true">
        <div class="text-center">
          <span class="material-symbols-outlined text-slate-400 text-4xl block mb-2">image_not_supported</span>
          <p class="text-xs text-slate-500">Không có ảnh nào được tải lên</p>
        </div>
      </div>
    `;
    return;
  }

  Array.from(files).forEach((file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const row = document.createElement('div');
      row.className = 'flex items-center gap-4 p-3 border border-slate-200 rounded-lg bg-white hover:bg-slate-50 transition-all group';
      row.setAttribute('data-image-item', 'true');
      row.innerHTML = `
        <img src="${e.target.result}" alt="${file.name}" class="w-16 h-16 object-cover rounded">
        <div class="flex-1">
          <p class="text-sm font-medium text-slate-900 truncate" title="${file.name}">${truncateFilename(file.name)}</p>
          <p class="text-xs text-slate-500">${(file.size / 1024).toFixed(2)} KB</p>
        </div>
        <button type="button" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition-all opacity-0 group-hover:opacity-100" onclick="this.closest('div').remove(); updateImageCount();">
          <span class="material-symbols-outlined text-lg">delete</span>
        </button>
      `;
      imageGallery.appendChild(row);
      updateImageCount();
    };
    reader.readAsDataURL(file);
  });
}

function updateImageCount() {
  if (!imageGallery || !imageCount) return;
  const items = imageGallery.querySelectorAll('[data-image-item]');

  if (items.length === 0) {
    imageGallery.innerHTML = `
      <div class="w-full py-16 bg-slate-50 rounded-lg flex items-center justify-center border border-dashed border-slate-300" data-placeholder="true">
        <div class="text-center">
          <span class="material-symbols-outlined text-slate-400 text-4xl block mb-2">image_not_supported</span>
          <p class="text-xs text-slate-500">Không có ảnh nào được tải lên</p>
        </div>
      </div>
    `;
    imageCount.textContent = '0';
  } else {
    imageCount.textContent = String(items.length);
  }
}

function removeAmenity(element) {
  element.remove();
}

function addAmenityModal() {
  if (!amenityModal || !amenitySearch || !amenityGrid || !amenitiesList) return;
  amenityModal.classList.remove('hidden');
  amenitySearch.value = '';
  filterAmenities('');

  const selectedIds = Array.from(amenitiesList.querySelectorAll('input[name="amenities[]"]')).map((input) => input.value);
  amenityGrid.querySelectorAll('.amenity-item').forEach((item) => {
    const id = item.getAttribute('data-id');
    if (selectedIds.includes(id)) {
      item.classList.add('ring-2', 'ring-green-500', 'bg-green-50', 'opacity-50', 'pointer-events-none');
      item.querySelector('input[type="checkbox"]').checked = false;
      if (!item.querySelector('.selected-label')) {
        const label = document.createElement('span');
        label.className = 'selected-label text-xs text-green-600 font-bold';
        label.textContent = 'Đã chọn';
        item.appendChild(label);
      }
    } else {
      item.classList.remove('ring-2', 'ring-green-500', 'bg-green-50', 'ring-blue-900', 'opacity-50', 'pointer-events-none');
      const label = item.querySelector('.selected-label');
      if (label) label.remove();
    }
  });
}

function closeAmenityModal() {
  if (!amenityModal) return;
  amenityModal.classList.add('hidden');
  uncheckAllAmenities();
}

function filterAmenities(query) {
  if (!amenityGrid) return;
  amenityGrid.querySelectorAll('.amenity-item').forEach((item) => {
    const text = item.getAttribute('data-text').toLowerCase();
    item.classList.toggle('hidden', !text.includes(query.toLowerCase()));
  });
}

function toggleAmenity(element) {
  element.classList.toggle('ring-2');
  element.classList.toggle('ring-blue-900');
  element.classList.toggle('bg-blue-50');
  const checkbox = element.querySelector('input[type="checkbox"]');
  checkbox.checked = !checkbox.checked;
}

function uncheckAllAmenities() {
  if (!amenityGrid) return;
  amenityGrid.querySelectorAll('.amenity-item').forEach((item) => {
    item.classList.remove('ring-2', 'ring-blue-900', 'bg-blue-50');
    item.querySelector('input[type="checkbox"]').checked = false;
  });
}

function confirmAmenities() {
  if (!amenityGrid || !amenitiesList) return;

  const selected = Array.from(amenityGrid.querySelectorAll('.amenity-item input[type="checkbox"]:checked')).map((input) => ({
    id: input.closest('.amenity-item').getAttribute('data-id'),
    name: input.getAttribute('data-name'),
    icon: input.getAttribute('data-icon'),
  }));

  if (selected.length === 0) {
    alert('Vui lòng chọn ít nhất một tiện ích!');
    return;
  }

  selected.forEach((amenity) => {
    const exists = amenitiesList.querySelector(`input[name="amenities[]"][value="${amenity.id}"]`);
    if (!exists) {
      const span = document.createElement('span');
      span.className = 'inline-flex items-center gap-2 px-3 py-2 bg-blue-100 text-blue-900 rounded-full text-sm font-medium cursor-pointer hover:bg-blue-200 transition-all';
      span.setAttribute('data-id', amenity.id);
      span.onclick = function () {
        removeAmenity(this);
      };
      span.innerHTML = `
        <span class="material-symbols-outlined text-sm">${amenity.icon}</span>
        ${amenity.name}
        <input type="hidden" name="amenities[]" value="${amenity.id}">
        <span class="material-symbols-outlined text-sm cursor-pointer">close</span>
      `;
      amenitiesList.insertBefore(span, amenitiesList.lastElementChild);
    }
  });

  closeAmenityModal();
}

function addEquipmentRow(id, name, category, quantity) {
  if (!equipmentTableBody) return;

  if (equipmentTableBody.querySelector(`input[name="equipments[]"][value="${id}"]`)) {
    return;
  }

  const emptyRow = equipmentTableBody.querySelector('tr td[colspan]');
  if (emptyRow) {
    emptyRow.closest('tr').remove();
  }

  const row = document.createElement('tr');
  row.className = 'border-b border-slate-100 hover:bg-slate-50';
  row.innerHTML = `
    <td class="px-6 py-4">
      <div>
        <p class="font-medium text-slate-900">${name}</p>
        <p class="text-xs text-slate-500">${category || 'Chưa phân loại'}</p>
      </div>
      <input type="hidden" name="equipments[]" value="${id}">
    </td>
    <td class="px-6 py-4 text-right">
      <div class="flex items-center justify-end">
        <button type="button" onclick="decrementValue(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 border border-slate-300 rounded-l-lg">
          <span class="material-symbols-outlined text-sm">remove</span>
        </button>
        <input type="number" name="equipment_quantities[${id}]" value="${quantity}" class="w-12 h-8 text-center border-y border-slate-300 text-sm" min="1">
        <button type="button" onclick="incrementValue(this)" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 border border-slate-300 rounded-r-lg">
          <span class="material-symbols-outlined text-sm">add</span>
        </button>
      </div>
    </td>
    <td class="px-6 py-4 text-center">
      <button type="button" onclick="removeEquipmentRow(this)" class="text-red-500 hover:text-red-700 transition-all">
        <span class="material-symbols-outlined">delete</span>
      </button>
    </td>
  `;
  equipmentTableBody.appendChild(row);
}

function removeEquipmentRow(btn) {
  const row = btn.closest('tr');
  row.remove();

  if (!equipmentTableBody) return;
  if (equipmentTableBody.querySelectorAll('tr:not(.empty-row)').length === 0) {
    equipmentTableBody.innerHTML = '<tr class="hover:bg-slate-50 empty-row"><td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">Chưa có thiết bị nào</td></tr>';
  }
}

function openEquipmentModal() {
  if (!equipmentModal || !equipmentSearch || !equipmentGrid || !equipmentTableBody) return;
  equipmentModal.classList.remove('hidden');
  equipmentSearch.value = '';
  filterEquipment('');

  const selectedIds = Array.from(equipmentTableBody.querySelectorAll('input[name="equipments[]"]')).map((input) => input.value);
  equipmentGrid.querySelectorAll('.equipment-item').forEach((item) => {
    const id = item.getAttribute('data-id');
    const iconDiv = item.querySelector('div:last-of-type');
    if (selectedIds.includes(id)) {
      item.classList.add('ring-2', 'ring-green-500', 'bg-green-50', 'opacity-50', 'pointer-events-none');
      item.classList.remove('ring-blue-900');
      item.querySelector('input[type="checkbox"]').checked = false;
      iconDiv.innerHTML = '<span class="material-symbols-outlined text-green-500">check_circle</span><span class="text-xs text-green-600 font-bold ml-1">Đã chọn</span>';
    } else {
      item.classList.remove('ring-2', 'ring-green-500', 'bg-green-50', 'ring-blue-900', 'opacity-50', 'pointer-events-none');
      iconDiv.innerHTML = '<span class="material-symbols-outlined text-slate-300">radio_button_unchecked</span>';
    }
  });
}

function closeEquipmentModal() {
  if (!equipmentModal) return;
  equipmentModal.classList.add('hidden');
  uncheckAllEquipment();
}

function filterEquipment(query) {
  if (!equipmentGrid) return;
  equipmentGrid.querySelectorAll('.equipment-item').forEach((item) => {
    const text = item.getAttribute('data-text').toLowerCase();
    item.classList.toggle('hidden', !text.includes(query.toLowerCase()));
  });
}

function toggleEquipment(element) {
  if (element.classList.contains('pointer-events-none')) {
    return;
  }

  element.classList.toggle('ring-2');
  element.classList.toggle('ring-blue-900');
  element.classList.toggle('bg-blue-50');
  const checkbox = element.querySelector('input[type="checkbox"]');
  checkbox.checked = !checkbox.checked;

  const iconDiv = element.querySelector('div:last-of-type');
  if (checkbox.checked) {
    iconDiv.innerHTML = '<span class="material-symbols-outlined text-blue-900">check_circle</span>';
  } else {
    iconDiv.innerHTML = '<span class="material-symbols-outlined text-slate-300">radio_button_unchecked</span>';
  }
}

function uncheckAllEquipment() {
  if (!equipmentGrid) return;
  equipmentGrid.querySelectorAll('.equipment-item').forEach((item) => {
    if (!item.classList.contains('pointer-events-none')) {
      item.classList.remove('ring-2', 'ring-blue-900', 'bg-blue-50');
      item.querySelector('input[type="checkbox"]').checked = false;
      const iconDiv = item.querySelector('div:last-of-type');
      iconDiv.innerHTML = '<span class="material-symbols-outlined text-slate-300">radio_button_unchecked</span>';
    }
  });
}

function confirmEquipment() {
  if (!equipmentGrid) return;

  const selected = Array.from(equipmentGrid.querySelectorAll('.equipment-item input[type="checkbox"]:checked')).map((input) => ({
    id: input.closest('.equipment-item').getAttribute('data-id'),
    name: input.getAttribute('data-name'),
    category: input.getAttribute('data-category'),
  }));

  if (selected.length === 0) {
    alert('Vui lòng chọn ít nhất một thiết bị!');
    return;
  }

  selected.forEach((equipment) => {
    addEquipmentRow(equipment.id, equipment.name, equipment.category, '1');
  });

  closeEquipmentModal();
}

function validateBeforeSubmit(event) {
  const singleBedInput = document.querySelector('input[name="single_bed_quantity"]');
  const doubleBedInput = document.querySelector('input[name="double_bed_quantity"]');
  const singleBed = parseInt(singleBedInput?.value ?? '0', 10);
  const doubleBed = parseInt(doubleBedInput?.value ?? '0', 10);

  if (singleBed + doubleBed < 1) {
    event.preventDefault();
    // Highlight the bed section visually
    const bedSection = singleBedInput?.closest('.grid');
    if (bedSection) {
      bedSection.querySelectorAll('.bg-slate-50.rounded-lg').forEach(el => {
        if (el.querySelector('input[name="single_bed_quantity"], input[name="double_bed_quantity"]')) {
          el.classList.add('ring-2', 'ring-red-400');
          setTimeout(() => el.classList.remove('ring-2', 'ring-red-400'), 3000);
        }
      });
    }

    // Inject a temporary error notice at the top of the form
    let notice = document.getElementById('bed-error-notice');
    if (!notice) {
      notice = document.createElement('div');
      notice.id = 'bed-error-notice';
      notice.className = 'mb-6 p-4 bg-red-50 border border-red-200 rounded-lg';
      notice.innerHTML = `
        <div class="flex items-center gap-2 text-red-700 font-bold mb-2">
          <span class="material-symbols-outlined">error</span>
          Vui lòng kiểm tra lại thông tin:
        </div>
        <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
          <li>Loại phòng phải có ít nhất 1 giường (giường đơn hoặc giường đôi).</li>
        </ul>
      `;
      const form = document.getElementById('roomTypeForm');
      form?.parentElement?.insertBefore(notice, form);
    }
    notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return false;
  }

  // Remove notice if valid
  document.getElementById('bed-error-notice')?.remove();
  return true;
}

document.addEventListener('DOMContentLoaded', () => {
  cacheElements();

  const form = document.getElementById('roomTypeForm');
  if (form) {
    form.addEventListener('submit', validateBeforeSubmit);
  }

  Object.assign(window, {
    generateCode,
    incrementValue,
    decrementValue,
    handleImageUpload,
    updateImageCount,
    removeAmenity,
    addAmenityModal,
    closeAmenityModal,
    filterAmenities,
    toggleAmenity,
    uncheckAllAmenities,
    confirmAmenities,
    addEquipmentRow,
    removeEquipmentRow,
    openEquipmentModal,
    closeEquipmentModal,
    filterEquipment,
    toggleEquipment,
    uncheckAllEquipment,
    confirmEquipment,
  });
});
