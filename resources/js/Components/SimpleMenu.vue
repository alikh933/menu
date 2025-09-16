<template>
	<div>
		<!-- فست فودی -->
		<div class="mb-6">
			<div class="sticky top-0 bg-slate-800/60 backdrop-blur-sm py-3 mb-3 z-10">
				<h3 class="text-lg font-semibold text-amber-400 text-center">فست فودی</h3>
			</div>
			<div class="space-y-3">
				<MenuItemCard
					v-for="item in items.filter(i => i.category === 'فست فودی')"
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

		<div class="border-t border-slate-600/50 my-6"></div>

		<!-- برنجی -->
		<div class="mb-6">
			<div class="sticky top-0 bg-slate-800/60 backdrop-blur-sm py-3 mb-3 z-10">
				<h3 class="text-lg font-semibold text-amber-400 text-center">برنجی</h3>
			</div>
			<div class="space-y-3">
				<MenuItemCard
					v-for="item in items.filter(i => i.category === 'برنجی')"
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

		<div class="border-t border-slate-600/50 my-6"></div>

		<!-- نوشیدنی -->
		<div class="mb-6">
			<div class="sticky top-0 bg-slate-800/60 backdrop-blur-sm py-3 mb-3 z-10">
				<h3 class="text-lg font-semibold text-amber-400 text-center">نوشیدنی</h3>
			</div>
			<div class="space-y-3">
				<MenuItemCard
					v-for="item in items.filter(i => i.category === 'نوشیدنی')"
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


