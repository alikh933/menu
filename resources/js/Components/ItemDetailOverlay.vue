<template>
    <div
        v-if="item"
        class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm"
        @click.self="onClose"
    >
        <div
            class="absolute inset-x-0 bottom-0 top-0 md:top-10 md:mx-auto md:max-w-[680px] bg-slate-900 rounded-t-2xl md:rounded-2xl overflow-auto"
        >
            <div class="relative">
                <img
                    :src="item.image"
                    :alt="item.name"
                    class="w-full h-56 md:h-72 object-cover rounded-t-2xl"
                />
                <button
                    @click="onClose"
                    class="absolute top-3 right-3 w-10 h-10 rounded-full bg-slate-900/70 text-white flex items-center justify-center backdrop-blur hover:bg-slate-900/90"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-4 mb-20">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ item.name }}</h2>
                        <p class="text-sm text-slate-300 mt-1">
                            {{ item.desc }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-amber-400">
                            {{ getTotalPrice().toLocaleString() }} تومان
                        </div>
                        <div class="mt-2 flex items-center gap-2 justify-end">
                            <div v-if="qty > 0" class="flex items-center gap-2">
                                <button
                                    @click="onDec(item.id)"
                                    class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center"
                                >
                                    -
                                </button>
                                <span
                                    class="text-xl font-bold text-amber-400 min-w-[2rem] text-center"
                                    >{{ qty }}</span
                                >
                                <button
                                    @click="onInc(item.id)"
                                    class="bg-green-500 hover:bg-green-600 text-white w-8 h-8 rounded-full flex items-center justify-center"
                                >
                                    +
                                </button>
                            </div>
                            <button
                                v-else
                                @click="onAdd(item)"
                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm"
                            >
                                افزودن
                            </button>
                        </div>
                    </div>
                </div>

                <!-- بخش انتخاب اندازه - فقط در صورت وجود اندازه‌های مختلف -->
                <div v-if="item.sizes && item.sizes.length > 0" class="border-t border-slate-700 pt-4">
                    <h3 class="font-semibold mb-3 text-white">انتخاب اندازه</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            v-for="size in item.sizes"
                            :key="size.id"
                            @click="selectedSize = size"
                            :class="[
                                'p-3 rounded-lg border-2 transition-colors',
                                selectedSize?.id === size.id
                                    ? 'border-amber-400 bg-amber-400/10 text-amber-400'
                                    : 'border-slate-600 hover:border-slate-500 text-slate-300'
                            ]"
                        >
                            <div class="text-center">
                                <div class="font-medium text-sm">{{ size.name }}</div>
                                <div class="text-xs mt-1 text-amber-400">
                                    {{ sizePriceLabel(size) }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- بخش انتخاب مخلفات - فقط در صورت وجود مخلفات -->
                <div v-if="item.sideDishes && item.sideDishes.length > 0" class="border-t border-slate-700 pt-4">
                    <h3 class="font-semibold mb-3 text-white">انتخاب مخلفات</h3>
                    <p class="text-sm text-slate-400 mb-3">
                        مخلفات مورد نظر خود را انتخاب کنید:
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <div
                            v-for="sideDish in item.sideDishes"
                            :key="sideDish.id"
                            @click="toggleSideDish(sideDish.id)"
                            :class="[
                                'p-3 rounded-lg border-2 cursor-pointer transition-all hover:scale-105',
                                selectedSideDishes[sideDish.id]
                                    ? 'border-amber-400 bg-amber-400/10'
                                    : 'border-slate-600 hover:border-slate-500'
                            ]"
                        >
                            <div class="text-center">
                                <div 
                                    :class="[
                                        'text-2xl mb-2 transition-colors',
                                        selectedSideDishes[sideDish.id] ? 'opacity-100' : 'opacity-50'
                                    ]"
                                >
                                    {{ sideDish.icon }}
                                </div>
                                <div 
                                    :class="[
                                        'text-xs font-medium transition-colors',
                                        selectedSideDishes[sideDish.id] ? 'text-amber-400' : 'text-slate-400'
                                    ]"
                                >
                                    {{ sideDish.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-700 pt-4">
                    <h3 class="font-semibold mb-2 text-white">اطلاعات تغذیه‌ای (کالری)</h3>
                    <div
                        class="divide-y divide-slate-700 rounded-xl overflow-hidden bg-slate-800/40"
                    >
                        <div
                            v-for="row in item.calories"
                            :key="row.part"
                            class="flex justify-between items-center px-4 py-3"
                        >
                            <span class="text-slate-300">{{ row.part }}</span>
                            <span class="text-slate-100"
                                >{{ row.kcal }} کیلوکالری</span
                            >
                        </div>
                    </div>
                </div>

                <!-- بخش پیشنهادات سفارش - استفاده از پیشنهادات خود آیتم -->
                <div v-if="item.suggestions && item.suggestions.length > 0" class="border-t border-slate-700 pt-4">
                    <h3 class="font-semibold mb-3 text-white">پیشنهادات سفارش</h3>
                    <p class="text-sm text-slate-400 mb-3">
                        معمولاً همراه با این سفارش، این موارد را هم سفارش
                        می‌دهند:
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <div
                            v-for="suggestion in item.suggestions"
                            :key="suggestion.id"
                            class="bg-slate-800/40 rounded-lg p-3"
                        >
                            <div class="flex items-center gap-3">
                                <button
                                    @click="onOpen(suggestion)"
                                    class="shrink-0 overflow-hidden rounded-lg w-12 h-12 hover:opacity-80 transition-opacity"
                                >
                                    <img
                                        :src="suggestion.image"
                                        :alt="suggestion.name"
                                        class="w-full h-full object-cover"
                                    />
                                </button>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm truncate text-white">
                                        {{ suggestion.name }}
                                    </h4>
                                    <div class="text-xs text-amber-400 mt-1">
                                        {{
                                            suggestion.price.toLocaleString()
                                        }}
                                        تومان
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        v-if="getQty(suggestion.id) > 0"
                                        class="text-xs text-amber-400 font-bold"
                                    >
                                        {{ getQty(suggestion.id) }}
                                    </div>
                                    <button
                                        @click="onAdd(suggestion)"
                                        class="bg-amber-500 hover:bg-amber-600 text-white w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- دکمه ثابت افزودن به سفارشات -->
        <div
            class="fixed bottom-0 left-0 right-0 md:left-auto md:right-auto md:max-w-[680px] md:mx-auto bg-slate-900 border-t border-slate-700 p-4 z-50"
        >
            <button
                @click="addToOrderWithCustomizations"
                class="w-full bg-amber-500 hover:bg-amber-600 text-white py-3 px-6 rounded-xl text-lg font-bold transition-colors"
            >
                افزودن به لیست سفارشات
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    item: { type: Object, default: null },
    qty: { type: Number, default: 0 },
    onAdd: { type: Function, required: true },
    onInc: { type: Function, required: true },
    onDec: { type: Function, required: true },
    onClose: { type: Function, required: true },
    onOpen: { type: Function, required: true },
    getQty: { type: Function, required: true },
});

// متغیرهای reactive برای نگهداری انتخاب‌های کاربر
const selectedSize = ref(null);
const selectedSideDishes = ref({});

// تابع تنظیم مقادیر پیش‌فرض
const initializeDefaults = () => {
    if (props.item) {
        // تنظیم اندازه پیش‌فرض (متوسط)
        if (props.item.sizes && props.item.sizes.length > 0) {
            selectedSize.value = props.item.sizes.find(size => size.multiplier === 1.0) || props.item.sizes[1] || props.item.sizes[0];
        }
        
        // تنظیم مخلفات پیش‌فرض (همه روشن)
        selectedSideDishes.value = {};
        if (props.item.sideDishes) {
            props.item.sideDishes.forEach(sideDish => {
                if (sideDish.enabled) {
                    selectedSideDishes.value[sideDish.id] = true;
                }
            });
        }
    }
};

// اجرای تابع تنظیم مقادیر پیش‌فرض در زمان mount
initializeDefaults();

// نظارت بر تغییرات item و تنظیم مجدد مقادیر پیش‌فرض
watch(() => props.item, () => {
    initializeDefaults();
}, { immediate: true });

// تابع تغییر وضعیت مخلفات (روشن/خاموش)
const toggleSideDish = (sideDishId) => {
    if (selectedSideDishes.value[sideDishId]) {
        delete selectedSideDishes.value[sideDishId];
    } else {
        selectedSideDishes.value[sideDishId] = true;
    }
};

// تابع محاسبه قیمت کل شامل اندازه (مخلفات تاثیری در قیمت ندارند)
const getTotalPrice = () => {
    if (!props.item) return 0;

    const base = props.item.price || 0;
    const multiplier = selectedSize.value && typeof selectedSize.value.multiplier === 'number' ? selectedSize.value.multiplier : 1;
    const extra = selectedSize.value && typeof selectedSize.value.price === 'number' ? selectedSize.value.price : 0;

    // قیمت نهایی = قیمت پایه × ضریب اندازه + مبلغ اضافه اندازه
    return Math.round(base * multiplier + extra);
};

// برچسب قیمت برای هر اندازه (نمایش مجموع با تخفیف اندازه کوچک)
const sizePriceLabel = (size) => {
    const base = props.item?.price || 0;
    const multiplier = typeof size.multiplier === 'number' ? size.multiplier : 1;
    const extra = typeof size.price === 'number' ? size.price : 0;
    const price = Math.round(base * multiplier + extra);
    return price.toLocaleString() + ' تومان';
};

// تابع افزودن به سفارش با تنظیمات انتخاب شده
const addToOrderWithCustomizations = () => {
    // ایجاد آیتم سفارش با تنظیمات انتخاب شده
    const orderItem = {
        ...props.item,
        selectedSize: selectedSize.value,
        selectedSideDishes: selectedSideDishes.value,
        totalPrice: getTotalPrice()
    };
    
    props.onAdd(orderItem);
    
    // بازگرداندن مقادیر به حالت پیش‌فرض
    initializeDefaults();
};
</script>
