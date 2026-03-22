@php
    $inputName       = $inputName ?? 'country';
    $selectedValue   = $selectedValue ?? '';
    $hasError        = $errors->has($inputName);
    $inputId         = $inputId ?? null;
    $formId          = $formId ?? null;
    $placeholder     = $placeholder ?? 'Chọn quốc gia';
    $autoWidth       = $autoWidth ?? false;
    $pickerCountries = $pickerCountries ?? $viewModel->countries();

    // Convert flag emoji → ISO-2 code (works on Windows where emoji flags don't render)
    $resolveISO = function(array $c): string {
        if (isset($c['iso'])) return strtolower($c['iso']);
        if (!empty($c['flag'])) {
            $iso = '';
            foreach (mb_str_split($c['flag']) as $char) {
                $cp = unpack('N', mb_convert_encoding($char, 'UTF-32BE', 'UTF-8'))[1];
                if ($cp >= 0x1F1E6 && $cp <= 0x1F1FF) $iso .= chr($cp - 0x1F1E6 + 65);
            }
            return strtolower($iso);
        }
        return '';
    };
    $pickerCountries = array_map(function($c) use ($resolveISO) {
        $c['iso'] = $resolveISO($c);
        return $c;
    }, $pickerCountries);

    $selectedEntry = collect($pickerCountries)->firstWhere('value', $selectedValue);
    $selectedISO   = $selectedEntry['iso'] ?? '';
    $pickerId      = 'cp_' . str_replace('.', '_', uniqid('', true));
@endphp

@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.3/css/flag-icons.min.css">
@endonce

<div id="{{ $pickerId }}" class="relative {{ $autoWidth ? 'inline-block' : '' }}">
    {{-- Hidden input cho form submission --}}
    <input type="hidden" name="{{ $inputName }}" id="{{ $inputId }}" value="{{ $selectedValue }}" />

    {{-- Trigger button --}}
    <button type="button"
        class="cp-trigger {{ $autoWidth ? 'whitespace-nowrap' : 'w-full' }} flex items-center gap-3 rounded-xl border {{ $hasError ? 'border-red-400' : 'border-slate-200' }} bg-slate-50 px-4 py-2.5 text-sm text-left transition-all hover:border-primary/40 focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer">
        @if($selectedISO)
            <span class="cp-flag fi fi-{{ $selectedISO }}" style="width:1.5rem;height:1.125rem;display:inline-block;flex-shrink:0;border-radius:2px"></span>
        @else
            <span class="cp-flag material-symbols-outlined" style="font-size:1.125rem;flex-shrink:0;color:#94a3b8">language</span>
        @endif
        <span class="cp-name {{ $autoWidth ? '' : 'flex-1' }} {{ $selectedEntry ? 'text-slate-700' : 'text-slate-400' }}">
            {{ $selectedEntry['value'] ?? $placeholder }}
        </span>
        <span class="material-symbols-outlined !text-base text-slate-400 cp-chevron" style="transition:transform .2s">expand_more</span>
    </button>

    {{-- Dropdown panel — dùng fixed để thoát khỏi overflow-hidden của container cha --}}
    <div class="cp-panel hidden fixed z-[9999] bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden" style="max-height:328px; min-width:240px">
        {{-- Search --}}
        <div class="p-2 border-b border-slate-100 bg-white sticky top-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 !text-sm text-slate-400">search</span>
                <input type="text" placeholder="Tìm quốc gia..."
                    class="cp-search w-full pl-8 pr-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-primary/20" />
            </div>
        </div>

        {{-- Danh sách --}}
        <ul class="cp-list overflow-y-auto" style="max-height:264px">
            @if($formId)
                <li class="cp-item flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-primary/5 transition-colors text-sm"
                    data-value="" data-iso="" data-search="tất cả">
                    <span class="material-symbols-outlined" style="font-size:1.25rem;color:#94a3b8;flex-shrink:0">language</span>
                    <span class="flex-1 text-slate-500 italic">Tất cả quốc gia</span>
                    <span class="material-symbols-outlined !text-sm text-primary cp-check {{ $selectedValue === '' ? '' : 'invisible' }}">check</span>
                </li>
            @endif
            @foreach($pickerCountries as $country)
                <li class="cp-item flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-primary/5 transition-colors text-sm"
                    data-value="{{ $country['value'] }}"
                    data-iso="{{ $country['iso'] }}"
                    data-search="{{ mb_strtolower($country['value']) }}">
                    <span class="fi fi-{{ $country['iso'] }}" style="width:1.5rem;height:1.125rem;display:inline-block;flex-shrink:0;border-radius:2px"></span>
                    <span class="flex-1 text-slate-700">{{ $country['value'] }}</span>
                    <span class="material-symbols-outlined !text-sm text-primary cp-check {{ $selectedValue === $country['value'] ? '' : 'invisible' }}">check</span>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<script>
(function () {
    var root    = document.getElementById('{{ $pickerId }}');
    if (!root) return;
    var hidden  = root.querySelector('input[type="hidden"]');
    var trigger = root.querySelector('.cp-trigger');
    var panel   = root.querySelector('.cp-panel');
    var search  = root.querySelector('.cp-search');
    var chevron = root.querySelector('.cp-chevron');
    var flagEl  = trigger.querySelector('.cp-flag');
    var nameEl  = trigger.querySelector('.cp-name');

    // Di chuyển panel ra body để tránh nằm trong <form>
    // (form với >1 text input sẽ không submit khi nhấn Enter)
    document.body.appendChild(panel);

    var items = panel.querySelectorAll('.cp-item');

    function setFlag(iso) {
        if (iso) {
            flagEl.className = 'cp-flag fi fi-' + iso;
            flagEl.style.cssText = 'width:1.5rem;height:1.125rem;display:inline-block;flex-shrink:0;border-radius:2px;vertical-align:middle';
            flagEl.textContent = '';
        } else {
            flagEl.className = 'cp-flag material-symbols-outlined';
            flagEl.style.cssText = 'font-size:1.125rem;flex-shrink:0;color:#94a3b8;vertical-align:middle';
            flagEl.textContent = 'language';
        }
    }

    function reposition() {
        var rect = trigger.getBoundingClientRect();
        panel.style.top   = (rect.bottom + 4) + 'px';
        panel.style.left  = rect.left + 'px';
        panel.style.width = Math.max(rect.width, 240) + 'px';
    }

    function open() {
        reposition();
        panel.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
        search.value = '';
        items.forEach(function (i) { i.style.display = ''; });
        var checked = panel.querySelector('.cp-check:not(.invisible)');
        if (checked) {
            setTimeout(function () { checked.closest('.cp-item').scrollIntoView({ block: 'nearest' }); }, 10);
        }
        setTimeout(function () { search.focus(); }, 30);
    }

    function close() {
        panel.classList.add('hidden');
        chevron.style.transform = '';
    }

    window.addEventListener('scroll', function () { if (!panel.classList.contains('hidden')) reposition(); }, true);
    window.addEventListener('resize', function () { if (!panel.classList.contains('hidden')) reposition(); });

    trigger.addEventListener('click', function (e) {
        e.preventDefault();
        panel.classList.contains('hidden') ? open() : close();
    });

    document.addEventListener('click', function (e) {
        if (!root.contains(e.target) && !panel.contains(e.target)) close();
    });

    search.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        items.forEach(function (item) {
            item.style.display = item.dataset.search.includes(q) ? '' : 'none';
        });
    });

    items.forEach(function (item) {
        item.addEventListener('click', function () {
            var val = item.dataset.value;
            var iso = item.dataset.iso;
            if (hidden.value !== val) {
                hidden.value = val;
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
            }
            setFlag(iso);
            nameEl.textContent = val || '{{ $placeholder }}';
            nameEl.classList.toggle('text-slate-400', !val);
            nameEl.classList.toggle('text-slate-700', !!val);
            items.forEach(function (i) { i.querySelector('.cp-check').classList.add('invisible'); });
            item.querySelector('.cp-check').classList.remove('invisible');
            close();
            @if($formId)
            var form = document.getElementById('{{ $formId }}');
            if (form) form.submit();
            @endif
        });
    });
}());
</script>
