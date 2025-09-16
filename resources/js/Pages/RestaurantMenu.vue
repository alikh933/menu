
<template>
    <!-- عنوان صفحه که در head مرورگر نمایش داده می‌شود -->
    <Head title="منوی رستوران" />

    <!-- کانتینر اصلی صفحه با استایل‌های responsive و dark theme -->
    <div
        class="min-h-screen bg-slate-950 text-slate-100 p-4 pt-4 pb-[calc(72px+env(safe-area-inset-bottom))] grid gap-4 md:max-w-[680px] md:mx-auto font-vazir"
    >
        <!-- بخش منوی معمولی - نمایش آیتم‌های منو با جزئیات کامل -->
        <section v-show="activeTab === 'regular'" class="bg-slate-800/60 rounded-xl p-4 shadow-lg">
            <h1 class="text-2xl font-bold mb-4">منوی معمولی</h1>
            <!-- کامپوننت منوی معمولی که آیتم‌ها، توابع مدیریت سفارش و نمایش جزئیات را دریافت می‌کند -->
            <RegularMenu
                :items="menuItems.regular"
                :getQty="getOrderQuantity"
                :onAdd="addToOrder"
                :onInc="increaseQuantity"
                :onDec="decreaseQuantity"
                :onOpen="openItem"
            />
        </section>

        <!-- بخش منوی ساده - نمایش آیتم‌های منو به صورت خلاصه -->
        <section v-show="activeTab === 'simple'" class="bg-slate-800/60 rounded-xl p-4 shadow-lg">
            <h1 class="text-2xl font-bold mb-4">منوی ساده</h1>
            <!-- کامپوننت منوی ساده که آیتم‌ها، توابع مدیریت سفارش و نمایش جزئیات را دریافت می‌کند -->
            <SimpleMenu
                :items="menuItems.simple"
                :getQty="getOrderQuantity"
                :onAdd="addToOrder"
                :onInc="increaseQuantity"
                :onDec="decreaseQuantity"
                :onOpen="openItem"
            />
        </section>

        <!-- بخش لیست سفارشات - نمایش آیتم‌های انتخاب شده و مجموع قیمت -->
        <OrdersList v-show="activeTab === 'orders'"
            :orders="orders"
            :total="totalPrice"
            :onInc="increaseQuantity"
            :onDec="decreaseQuantity"
            :onFinalize="finalizeOrder"
        />

        <!-- بخش اطلاعات تماس - نمایش اطلاعات رستوران و راه‌های ارتباطی -->
        <ContactSection v-show="activeTab === 'contact'" />
    </div>

    <!-- لایه نمایش جزئیات آیتم - نمایش اطلاعات کامل آیتم انتخاب شده -->
    <ItemDetailOverlay
        :item="selectedItem"
        :qty="getOrderQuantity(selectedItem?.id || 0)"
        :onAdd="addToOrder"
        :onInc="increaseQuantity"
        :onDec="decreaseQuantity"
        :onClose="closeItem"
        :onOpen="openItem"
        :getQty="getOrderQuantity"
    />

    <!-- نوار ناوبری پایین صفحه - تغییر بین تب‌های مختلف -->
    <BottomNav :active="activeTab" @change="setActiveTab" />
</template>

<script setup>
// Import کامپوننت‌های مورد نیاز
import { Head } from "@inertiajs/vue3"; // کامپوننت مدیریت head صفحه
import { ref, computed } from "vue"; // توابع reactive و computed از Vue 3
import RegularMenu from '../Components/RegularMenu.vue'; // کامپوننت منوی معمولی
import SimpleMenu from '../Components/SimpleMenu.vue'; // کامپوننت منوی ساده
import OrdersList from '../Components/OrdersList.vue'; // کامپوننت لیست سفارشات
import ContactSection from '../Components/ContactSection.vue'; // کامپوننت بخش تماس
import BottomNav from '../Components/BottomNav.vue'; // کامپوننت ناوبری پایین
import ItemDetailOverlay from '../Components/ItemDetailOverlay.vue'; // کامپوننت نمایش جزئیات آیتم

// دریافت props از Inertia
const props = defineProps({
    menuItems: { type: Object, default: () => ({ regular: [], simple: [] }) },
});

// منبع داده نهایی: فقط از props
const menuItems = computed(() => props.menuItems);

// متغیر reactive برای نگهداری تب فعال (پیش‌فرض: منوی معمولی)
const activeTab = ref("regular");

// متغیر reactive برای نگهداری لیست سفارشات کاربر
const orders = ref([]);

// تابع اضافه کردن آیتم به سفارش
// اگر آیتم قبلاً در سفارش وجود داشته باشد، کمیت آن را افزایش می‌دهد
// در غیر این صورت، آیتم جدید با کمیت 1 به سفارش اضافه می‌کند
// حالا از اندازه و مخلفات انتخاب شده نیز پشتیبانی می‌کند
const addToOrder = (item) => {
    // بررسی اینکه آیا آیتم با همین تنظیمات قبلاً در سفارش وجود دارد یا نه
    const existingOrder = orders.value.find((order) => {
        // بررسی ID اصلی آیتم
        if (order.id !== item.id) return false;
        
        // بررسی اندازه انتخاب شده
        const orderSize = order.selectedSize?.id || 'default';
        const itemSize = item.selectedSize?.id || 'default';
        if (orderSize !== itemSize) return false;
        
        // بررسی مخلفات انتخاب شده
        const orderSideDishes = JSON.stringify(order.selectedSideDishes || {});
        const itemSideDishes = JSON.stringify(item.selectedSideDishes || {});
        if (orderSideDishes !== itemSideDishes) return false;
        
        return true;
    });
    
    if (existingOrder) {
        existingOrder.quantity += 1;
    } else {
        orders.value.push({
            ...item,
            quantity: 1,
            // ذخیره تنظیمات انتخاب شده
            selectedSize: item.selectedSize || null,
            selectedSideDishes: item.selectedSideDishes || {},
            totalPrice: item.totalPrice || item.price
        });
    }
};

// تابع کاهش کمیت آیتم در سفارش
// اگر کمیت بیشتر از 1 باشد، آن را کاهش می‌دهد
// اگر کمیت 1 باشد، آیتم را از سفارش حذف می‌کند
const decreaseQuantity = (itemId) => {
    const order = orders.value.find((order) => order.id === itemId);
    if (order && order.quantity > 1) {
        order.quantity -= 1;
    } else if (order && order.quantity === 1) {
        removeFromOrder(itemId);
    }
};

// تابع افزایش کمیت آیتم در سفارش
// کمیت آیتم موجود را یک واحد افزایش می‌دهد
const increaseQuantity = (itemId) => {
    const order = orders.value.find((order) => order.id === itemId);
    if (order) {
        order.quantity += 1;
    }
};

// تابع حذف کامل آیتم از سفارش
// آیتم را بر اساس ID از آرایه سفارشات حذف می‌کند
const removeFromOrder = (itemId) => {
    const index = orders.value.findIndex((order) => order.id === itemId);
    if (index > -1) {
        orders.value.splice(index, 1);
    }
};

// تابع دریافت کمیت آیتم در سفارش
// اگر آیتم در سفارش وجود داشته باشد، کمیت آن را برمی‌گرداند
// در غیر این صورت، 0 برمی‌گرداند
const getOrderQuantity = (itemId) => {
    const order = orders.value.find((order) => order.id === itemId);
    return order ? order.quantity : 0;
};

// computed property برای محاسبه مجموع قیمت سفارشات
// قیمت هر آیتم را در کمیت آن ضرب کرده و مجموع کل را محاسبه می‌کند
// حالا از قیمت‌های سفارشی (اندازه و مخلفات) نیز پشتیبانی می‌کند
const totalPrice = computed(() => {
    return orders.value.reduce((total, order) => {
        // استفاده از قیمت کل سفارشی اگر موجود باشد، در غیر این صورت از قیمت اصلی
        const itemPrice = order.totalPrice || order.price;
        return total + itemPrice * order.quantity;
    }, 0);
});

// متغیر reactive برای نگهداری آیتم انتخاب شده برای نمایش جزئیات
const selectedItem = ref(null);

// تابع باز کردن جزئیات آیتم - آیتم انتخاب شده را در selectedItem ذخیره می‌کند
const openItem = (item) => {
    selectedItem.value = item;
};

// تابع بستن جزئیات آیتم - آیتم انتخاب شده را پاک می‌کند
const closeItem = () => {
    selectedItem.value = null;
};

// تابع نهایی‌سازی سفارش
// بررسی می‌کند که آیا سفارشی وجود دارد یا نه
// خلاصه سفارش را نمایش می‌دهد و سپس سفارشات را پاک می‌کند
const finalizeOrder = () => {
    if (orders.value.length === 0) {
        alert("لطفاً ابتدا آیتمی به سفارش اضافه کنید");
        return;
    }

    // ایجاد خلاصه سفارش شامل نام، کمیت و تنظیمات انتخاب شده
    const orderSummary = orders.value
        .map((order) => {
            let summary = `${order.name} × ${order.quantity}`;
            
            // اضافه کردن اندازه انتخاب شده
            if (order.selectedSize) {
                summary += ` (${order.selectedSize.name})`;
            }
            
            // نمایش فقط مخلفات انتخاب نشده به صورت "بدون ..."
            if (order.sideDishes && order.sideDishes.length > 0) {
                const unselected = order.sideDishes
                    .filter(sd => !order.selectedSideDishes || !order.selectedSideDishes[sd.id])
                    .map(sd => sd.name);

                if (unselected.length > 0) {
                    summary += ` | مخلفات: بدون ${unselected.join(', ')}`;
                }
            }
            
            return summary;
        })
        .join("\n");

    // نمایش پیام موفقیت با جزئیات سفارش
    alert(
        `سفارش شما با موفقیت ثبت شد!\n\n${orderSummary}\n\nمجموع: ${totalPrice.value.toLocaleString()} تومان`
    );

    // پاک کردن سفارشات پس از ثبت موفق
    orders.value = [];
};

// تابع تغییر تب فعال
// تب انتخاب شده توسط کاربر را در activeTab ذخیره می‌کند
const setActiveTab = (tab) => {
    activeTab.value = tab;
};
</script>

<!-- کامنت اضافی: این کامپوننت برای نمایش جزئیات آیتم به صورت لایه تمام صفحه استفاده می‌شود -->

