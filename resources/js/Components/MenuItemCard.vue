<template>
    <div
        class="bg-slate-700/50 rounded-lg p-3 flex items-center gap-3"
        @click="route('item-details', { item: item })"
    >
        <button
            @click.stop="onOpen(item)"
            class="shrink-0 overflow-hidden rounded-lg w-16 h-16"
        >
            <img
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-cover"
            />
        </button>
        <div class="flex-1" @click="onOpen(item)">
            <div class="font-medium">{{ item.name }}</div>
            <div class="text-xs text-slate-300 mt-0.5 line-clamp-2">
                {{ item.desc }}
            </div>
            <div class="text-sm text-amber-400 mt-2">
                {{ item.price.toLocaleString() }} تومان
            </div>
        </div>
        <div class="flex items-center gap-2 self-end">
            <div v-if="qty > 0" class="flex items-center gap-2">
                <button
                    @click.stop="onDec(item.id)"
                    class="bg-red-500 hover:bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold transition-colors"
                >
                    -
                </button>
                <span
                    class="text-lg font-bold text-amber-400 min-w-[1.5rem] text-center"
                    >{{ qty }}</span
                >
                <button
                    @click.stop="onInc(item.id)"
                    class="bg-green-500 hover:bg-green-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-sm font-bold transition-colors"
                >
                    +
                </button>
            </div>
            <button
                v-else
                @click.stop="onAdd(item)"
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
</template>

<script setup>
const props = defineProps({
    item: { type: Object, required: true },
    qty: { type: Number, required: true },
    onAdd: { type: Function, required: true },
    onInc: { type: Function, required: true },
    onDec: { type: Function, required: true },
    onOpen: { type: Function, required: true },
});
</script>
