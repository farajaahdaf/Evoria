{{--
    Custom modal dialog — pengganti browser confirm() / alert()
    Gunakan via JavaScript:

        // Confirm (returns Promise<boolean>)
        const ok = await evModal.confirm({ title: 'Judul', message: 'Pesan', danger: true });
        if (!ok) return;

        // Alert (returns Promise<void>)
        await evModal.alert({ title: 'Info', message: 'Pesan' });
--}}
<div id="ev-modal-backdrop"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
     style="display:none!important; background:rgba(15,23,42,.55); backdrop-filter:blur(4px);">

    <div id="ev-modal-card"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden
                transform transition-all duration-200 scale-95 opacity-0">

        {{-- Icon area --}}
        <div id="ev-modal-icon-wrap" class="flex justify-center pt-7 pb-1"></div>

        {{-- Text --}}
        <div class="px-7 py-4 text-center">
            <h3 id="ev-modal-title"
                class="text-lg font-bold text-slate-900 leading-snug"></h3>
            <p id="ev-modal-message"
               class="mt-2 text-sm text-slate-500 leading-relaxed whitespace-pre-line"></p>
        </div>

        {{-- Buttons --}}
        <div id="ev-modal-buttons"
             class="flex gap-3 px-7 pb-7 pt-2"></div>
    </div>
</div>

<script>
window.evModal = (() => {
    const backdrop  = document.getElementById('ev-modal-backdrop');
    const card      = document.getElementById('ev-modal-card');
    const iconWrap  = document.getElementById('ev-modal-icon-wrap');
    const titleEl   = document.getElementById('ev-modal-title');
    const msgEl     = document.getElementById('ev-modal-message');
    const buttonsEl = document.getElementById('ev-modal-buttons');

    const ICONS = {
        danger: `<div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center">
                   <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                   </svg>
                 </div>`,
        info:   `<div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
                   <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                   </svg>
                 </div>`,
        success:`<div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
                   <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="M5 13l4 4L19 7"/>
                   </svg>
                 </div>`,
    };

    function show({ title, message, icon = 'info' }) {
        titleEl.textContent = title  || '';
        msgEl.textContent   = message || '';
        iconWrap.innerHTML  = ICONS[icon] || ICONS.info;
        backdrop.style.removeProperty('display');
        // Animate in
        requestAnimationFrame(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        });
    }

    function hide() {
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { backdrop.style.setProperty('display', 'none', 'important'); }, 180);
    }

    function makeBtn(label, style) {
        const btn = document.createElement('button');
        btn.textContent = label;
        btn.className   = style;
        return btn;
    }

    /**
     * confirm({ title, message, confirmText, cancelText, danger })
     * returns Promise<boolean>
     */
    function confirm({ title, message, confirmText, cancelText, danger = false } = {}) {
        return new Promise(resolve => {
            buttonsEl.innerHTML = '';

            const cancelBtn = makeBtn(
                cancelText || 'Batal',
                'flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold ' +
                'text-slate-600 hover:bg-slate-50 transition-colors'
            );

            const confirmCls = danger
                ? 'flex-1 py-2.5 rounded-xl bg-red-600 text-sm font-semibold text-white hover:bg-red-700 transition-colors'
                : 'flex-1 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white hover:opacity-90 transition-opacity';

            const confirmBtn = makeBtn(confirmText || 'Ya, Lanjutkan', confirmCls);

            cancelBtn.onclick  = () => { hide(); resolve(false); };
            confirmBtn.onclick = () => { hide(); resolve(true);  };

            buttonsEl.appendChild(cancelBtn);
            buttonsEl.appendChild(confirmBtn);

            show({ title, message, icon: danger ? 'danger' : 'info' });
        });
    }

    /**
     * alert({ title, message, buttonText })
     * returns Promise<void>
     */
    function alert({ title, message, buttonText, icon } = {}) {
        return new Promise(resolve => {
            buttonsEl.innerHTML = '';

            const okBtn = makeBtn(
                buttonText || 'Oke',
                'flex-1 py-2.5 rounded-xl bg-primary text-sm font-semibold text-white hover:opacity-90 transition-opacity'
            );
            okBtn.onclick = () => { hide(); resolve(); };

            buttonsEl.appendChild(okBtn);
            show({ title, message, icon: icon || 'info' });
        });
    }

    // Close on backdrop click
    backdrop.addEventListener('click', e => {
        if (e.target === backdrop) hide();
    });

    return { confirm, alert };
})();
</script>
