@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-2 border-navy/20 focus:border-navy focus:ring-0 rounded-none shadow-[2px_2px_0_0_#0B132B] focus:shadow-[4px_4px_0_0_#0B132B] transition-all bg-white py-3 px-4 font-mono text-sm']) }}>
