<template>
	<nav class="fixed inset-x-0 bottom-0 bg-slate-900/90 backdrop-blur-md border border-white/10 rounded-t-2xl mx-auto md:max-w-[680px] grid grid-cols-4 gap-2 px-3 py-2 pb-[calc(8px+env(safe-area-inset-bottom))]">
		<button 
			@click="$emit('change','regular')"
			:class="['appearance-none border-0 flex flex-col items-center justify-center py-2.5 px-2 rounded-xl transition focus:outline-none', active==='regular' ? 'bg-amber-400/10 text-amber-400' : 'bg-transparent text-slate-400 hover:bg-white/5']">
			<span class="leading-none" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" :stroke-width="active==='regular' ? '2.2' : '1.8'" stroke-linecap="round" stroke-linejoin="round">
					<line x1="3" y1="6" x2="21" y2="6" />
					<line x1="3" y1="12" x2="21" y2="12" />
					<line x1="3" y1="18" x2="14" y2="18" />
				</svg>
			</span>
			<span class="text-[12px] mt-1.5">منوی معمولی</span>
		</button>

		<button 
			@click="$emit('change','simple')"
			:class="['appearance-none border-0 flex flex-col items-center justify-center py-2.5 px-2 rounded-xl transition focus:outline-none', active==='simple' ? 'bg-amber-400/10 text-amber-400' : 'bg-transparent text-slate-400 hover:bg-white/5']">
			<span class="leading-none" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" :stroke-width="active==='simple' ? '2.2' : '1.8'" stroke-linecap="round" stroke-linejoin="round">
					<rect x="4" y="5" width="16" height="14" rx="2" />
					<path d="M8 9h8M8 12h8M8 15h5" />
				</svg>
			</span>
			<span class="text-[12px] mt-1.5">منوی ساده</span>
		</button>

		<button 
			@click="$emit('change','orders')"
			:class="['appearance-none border-0 flex flex-col items-center justify-center py-2.5 px-2 rounded-xl transition focus:outline-none', active==='orders' ? 'bg-amber-400/10 text-amber-400' : 'bg-transparent text-slate-400 hover:bg-white/5']">
			<span class="leading-none" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" :stroke-width="active==='orders' ? '2.2' : '1.8'" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 7h12l-1 12a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 7z" />
					<path d="M9 7a3 3 0 0 1 6 0" />
				</svg>
			</span>
			<span class="text-[12px] mt-1.5">لیست سفارشات</span>
		</button>

		<button 
			@click="$emit('change','contact')"
			:class="['appearance-none border-0 flex flex-col items-center justify-center py-2.5 px-2 rounded-xl transition focus:outline-none', active==='contact' ? 'bg-amber-400/10 text-amber-400' : 'bg-transparent text-slate-400 hover:bg-white/5']">
			<span class="leading-none" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" :stroke-width="active==='contact' ? '2.2' : '1.8'" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
				</svg>
			</span>
			<span class="text-[12px] mt-1.5">ارتباط با ما</span>
		</button>
	</nav>
</template>

<script setup>
defineProps({ active: { type: String, required: true } });
</script>


