<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-[#e91e63] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#d81b60] focus:bg-[#d81b60] active:bg-[#c2185b] focus:outline-none focus:ring-2 focus:ring-[#e91e63] focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150 shadow-lg']) }}>
    {{ $slot }}
</button>
