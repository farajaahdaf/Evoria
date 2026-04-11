<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-[#10367d] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#0c2a61] focus:bg-[#0c2a61] active:bg-[#091b42] focus:outline-none focus:ring-2 focus:ring-[#10367d] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
