<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-3 bg-navy border-2 border-navy rounded-none font-display font-bold text-sm text-eggshell uppercase tracking-widest hover:bg-eggshell hover:text-navy shadow-[4px_4px_0_0_#991B1B] hover:shadow-[6px_6px_0_0_#991B1B] hover:-translate-y-0.5 active:translate-y-1 active:shadow-[0_0_0_0_#991B1B] focus:outline-none focus:ring-0 transition-all duration-150']) }}>
    {{ $slot }}
</button>
