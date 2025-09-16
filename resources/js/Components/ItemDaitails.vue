<template v-if="selectedItem">
    <div
        class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm"
        @click.self="closeItem"
    >
        <div
            class="absolute inset-x-0 bottom-0 top-0 md:top-10 md:mx-auto md:max-w-[680px] bg-slate-900 rounded-t-2xl md:rounded-2xl overflow-auto"
        >
            <div class="relative">
                <img
                    :src="selectedItem.image"
                    :alt="selectedItem.name"
                    class="w-full h-56 md:h-72 object-cover rounded-t-2xl"
                />
                <button
                    @click="closeItem"
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
            <div class="p-4 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold">
                            {{ selectedItem.name }}
                        </h2>
                        <p class="text-sm text-slate-300 mt-1">
                            {{ selectedItem.desc }}
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-amber-400">
                            {{ selectedItem.price.toLocaleString() }} تومان
                        </div>
                        <div class="mt-2 flex items-center gap-2 justify-end">
                            <div
                                v-if="getOrderQuantity(selectedItem.id) > 0"
                                class="flex items-center gap-2"
                            >
                                <button
                                    @click="decreaseQuantity(selectedItem.id)"
                                    class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center"
                                >
                                    -
                                </button>
                                <span
                                    class="text-xl font-bold text-amber-400 min-w-[2rem] text-center"
                                    >{{
                                        getOrderQuantity(selectedItem.id)
                                    }}</span
                                >
                                <button
                                    @click="increaseQuantity(selectedItem.id)"
                                    class="bg-green-500 hover:bg-green-600 text-white w-8 h-8 rounded-full flex items-center justify-center"
                                >
                                    +
                                </button>
                            </div>
                            <button
                                v-else
                                @click="addToOrder(selectedItem)"
                                class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm"
                            >
                                افزودن
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-700 pt-4">
                    <h3 class="font-semibold mb-2">اطلاعات تغذیه‌ای (کالری)</h3>
                    <div
                        class="divide-y divide-slate-700 rounded-xl overflow-hidden bg-slate-800/40"
                    >
                        <div
                            v-for="row in selectedItem.calories"
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
            </div>
        </div>
    </div>
</template>
<script setup>
    import { ref } from 'vue';
    const selectedItem = ref(null);
</script>