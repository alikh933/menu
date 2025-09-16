<template>
	<section class="bg-slate-800/60 rounded-xl p-4 shadow-lg mb-10">
		<h1 class="text-2xl font-bold mb-4">لیست سفارشات</h1>

		<div v-if="orders.length > 0" class="space-y-4 mb-6">
			<div v-for="order in orders" :key="`${order.id}-${order.selectedSize?.id || 'default'}-${JSON.stringify(order.selectedSideDishes || {})}`" class="bg-slate-700/50 rounded-lg p-4">
				<div class="flex justify-between items-start mb-3">
					<div class="flex-1">
						<span class="font-medium text-lg">{{ order.name }}</span>
						
						<!-- نمایش اندازه انتخاب شده -->
						<div v-if="order.selectedSize" class="text-sm text-blue-400 mt-1">
							اندازه: {{ order.selectedSize.name }}
						</div>
						
						<!-- نمایش فقط مخلفات انتخاب نشده به صورت "بدون ..." -->
						<div v-if="order.sideDishes && getUnselectedSideDishNames(order).length > 0" class="text-sm text-green-400 mt-1">
							مخلفات: بدون {{ getUnselectedSideDishNames(order).join(', ') }}
						</div>
						
						<div class="text-sm text-slate-300 mt-1">
							{{ getOrderPrice(order).toLocaleString() }} تومان
						</div>
					</div>
					<div class="flex items-center gap-3">
						<button @click="onDec(order.id)" class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-lg font-bold transition-colors">-</button>
						<span class="text-xl font-bold text-amber-400 min-w-[2rem] text-center">{{ order.quantity }}</span>
						<button @click="onInc(order.id)" class="bg-green-500 hover:bg-green-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-lg font-bold transition-colors">+</button>
					</div>
				</div>
				<div class="text-right">
					<span class="text-amber-400 font-medium">مجموع: {{ (getOrderPrice(order) * order.quantity).toLocaleString() }} تومان</span>
				</div>
			</div>
		</div>

		<div v-else class="text-center py-8 text-slate-400">
			<div class="text-6xl mb-4">🛒</div>
			<p class="text-lg">هیچ آیتمی در سفارش شما وجود ندارد</p>
			<p class="text-sm mt-2">برای اضافه کردن آیتم، به بخش منو بروید</p>
		</div>

		<div v-if="orders.length > 0" class="border-t border-slate-600/50 pt-4">
			<div class="flex justify-between items-center mb-4">
				<span class="text-xl font-bold">مجموع کل:</span>
				<span class="text-2xl font-bold text-amber-400">{{ total.toLocaleString() }} تومان</span>
			</div>
			<button @click="onFinalize" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-xl text-lg font-bold transition-colors">نهایی سازی سفارش</button>
		</div>
	</section>
</template>

<script setup>
const props = defineProps({
	orders: { type: Array, required: true },
	total: { type: Number, required: true },
	onInc: { type: Function, required: true },
	onDec: { type: Function, required: true },
	onFinalize: { type: Function, required: true },
});

// تابع دریافت نام مخلفات بر اساس ID
const getSideDishName = (order, sideDishId) => {
	if (order.sideDishes) {
		const sideDish = order.sideDishes.find(sd => sd.id === sideDishId);
		return sideDish ? sideDish.name : sideDishId;
	}
	return sideDishId;
};

// محاسبه نام مخلفات انتخاب نشده
const getUnselectedSideDishNames = (order) => {
	if (!order.sideDishes || order.sideDishes.length === 0) return [];
	const selected = order.selectedSideDishes || {};
	return order.sideDishes
		.filter(sd => !selected[sd.id])
		.map(sd => sd.name);
};

// تابع محاسبه قیمت آیتم شامل تنظیمات انتخاب شده
const getOrderPrice = (order) => {
	// استفاده از قیمت کل سفارشی اگر موجود باشد، در غیر این صورت از قیمت اصلی
	return order.totalPrice || order.price;
};
</script>


