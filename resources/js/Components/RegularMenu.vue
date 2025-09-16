<template>
	<div class="space-y-4">
		<div class="bg-slate-700/50 rounded-lg p-4">
			<h3 class="text-lg font-semibold text-amber-400">غذاهای اصلی</h3>
			<div class="mt-3 space-y-3">
				<MenuItemCard
					v-for="item in items.filter(i => i.category === 'غذاهای اصلی')"
					:key="item.id"
					:item="item"
					:qty="getQty(item.id)"
					:onAdd="onAdd"
					:onInc="onInc"
					:onDec="onDec"
					:onOpen="onOpen"
				/>
			</div>
		</div>
		<div class="bg-slate-700/50 rounded-lg p-4">
			<h3 class="text-lg font-semibold text-amber-400">نوشیدنی‌ها</h3>
			<div class="mt-3 space-y-3">
				<MenuItemCard
					v-for="item in items.filter(i => i.category === 'نوشیدنی‌ها')"
					:key="item.id"
					:item="item"
					:qty="getQty(item.id)"
					:onAdd="onAdd"
					:onInc="onInc"
					:onDec="onDec"
					:onOpen="onOpen"
				/>
			</div>
		</div>
	</div>
</template>

<script setup>
import MenuItemCard from './MenuItemCard.vue';

const props = defineProps({
	items: { type: Array, required: true },
	getQty: { type: Function, required: true },
	onAdd: { type: Function, required: true },
	onInc: { type: Function, required: true },
	onDec: { type: Function, required: true },
	onOpen: { type: Function, required: true },
});
</script>


