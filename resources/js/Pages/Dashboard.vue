<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />
        <v-container fluid class="pa-4 pa-md-8 bg-background">
            <v-card
                variant="flat"
                rounded="xl"
                class="mb-8 pa-4 border-sm shadow-sm bg-surface"
            >
                <v-row align="center">
                    <v-col cols="12" md="6">
                        <div class="d-flex align-center">
                            <v-avatar
                                color="primary"
                                variant="tonal"
                                size="48"
                                class="me-4"
                                rounded="lg"
                            >
                                <v-icon size="28"
                                    >mdi-view-dashboard-outline</v-icon
                                >
                            </v-avatar>
                            <div>
                                <h1 class="text-h5 font-weight-black">
                                    InvenTrack Dashboard
                                </h1>
                                <p
                                    class="text-caption text-medium-emphasis font-weight-medium"
                                >
                                    {{
                                        new Date().toLocaleDateString("id-ID", {
                                            weekday: "long",
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        })
                                    }}
                                </p>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="6" class="text-md-right">
                        <div class="d-flex ga-2 justify-md-end">
                            <v-select
                                v-model="filterForm.month"
                                :items="months"
                                item-title="name"
                                item-value="value"
                                label="Bulan"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="lg"
                                style="max-width: 160px"
                                @update:model-value="applyFilter"
                            ></v-select>
                            <v-select
                                v-model="filterForm.year"
                                :items="years"
                                label="Tahun"
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="lg"
                                style="max-width: 120px"
                                @update:model-value="applyFilter"
                            ></v-select>
                        </div>
                    </v-col>
                </v-row>
            </v-card>

            <v-row class="mb-8">
                <v-col
                    v-for="(stat, i) in statCards"
                    :key="i"
                    cols="12"
                    sm="6"
                    md="3"
                >
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="pa-5 border-sm stat-card fill-height bg-surface"
                        :style="`border-left: 5px solid ${stat.realColor} !important`"
                    >
                        <div class="d-flex justify-space-between align-center">
                            <div>
                                <div
                                    class="text-overline text-medium-emphasis font-weight-bold mb-1"
                                >
                                    {{ stat.title }}
                                </div>
                                <div class="text-h4 font-weight-black mb-1">
                                    {{ stat.value }}
                                </div>
                            </div>
                            <v-avatar
                                rounded="xl"
                                size="56"
                                :color="stat.color"
                                variant="tonal"
                            >
                                <v-icon size="32">{{ stat.icon }}</v-icon>
                            </v-avatar>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm pa-6 fill-height bg-surface"
                    >
                        <div
                            class="d-flex justify-space-between align-center mb-8"
                        >
                            <div>
                                <h3 class="text-h6 font-weight-black">
                                    Trend Masuk vs Keluar
                                </h3>
                                <p class="text-caption text-medium-emphasis">
                                    Data harian pergerakan stok bengkel
                                </p>
                            </div>
                            <div class="d-flex ga-2">
                                <v-badge
                                    dot
                                    color="success"
                                    inline
                                    label="Masuk"
                                ></v-badge>
                                <v-badge
                                    dot
                                    color="error"
                                    inline
                                    label="Keluar"
                                ></v-badge>
                            </div>
                        </div>
                        <div style="height: 380px">
                            <Line :data="chartConfig" :options="chartOptions" />
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm pa-6 fill-height bg-surface"
                    >
                        <h3
                            class="text-h6 font-weight-black mb-6 d-flex align-center"
                        >
                            <v-icon color="orange-darken-2" class="me-2"
                                >mdi-fire</v-icon
                            >
                            Best Sellers
                        </h3>
                        <div v-if="topProducts && topProducts.length > 0">
                            <v-hover
                                v-for="(item, index) in topProducts"
                                :key="index"
                                v-slot="{ isHovering, props }"
                            >
                                <div
                                    v-bind="props"
                                    class="mb-6 px-2 pt-2 pb-1 rounded-lg transition-all"
                                    :class="isHovering ? 'bg-background' : ''"
                                >
                                    <div
                                        class="d-flex justify-space-between align-center mb-2"
                                    >
                                        <span
                                            class="text-subtitle-2 font-weight-bold"
                                            >{{ item.name }}</span
                                        >
                                        <v-chip
                                            size="x-small"
                                            color="primary"
                                            variant="flat"
                                            class="font-weight-black"
                                        >
                                            {{ item.total }} Pcs
                                        </v-chip>
                                    </div>
                                    <v-progress-linear
                                        :model-value="
                                            calculatePercent(item.total)
                                        "
                                        color="primary"
                                        height="8"
                                        rounded
                                    ></v-progress-linear>
                                </div>
                            </v-hover>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-6">
                <v-col cols="12" md="5">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm bg-surface fill-height overflow-hidden"
                    >
                        <v-toolbar flat class="px-4 border-b">
                            <v-icon color="error" class="me-2"
                                >mdi-alert-circle-outline</v-icon
                            >
                            <v-toolbar-title
                                class="text-subtitle-1 font-weight-black"
                                >Wajib Order!</v-toolbar-title
                            >
                            <v-btn
                                v-if="$page.props.auth.user.role === 'admin'"
                                icon="mdi-truck-fast-outline"
                                variant="tonal"
                                color="primary"
                                size="small"
                                rounded="lg"
                                @click="router.visit('/suppliers')"
                            ></v-btn>
                        </v-toolbar>

                        <v-list lines="two" class="pa-0 bg-transparent">
                            <v-list-item
                                v-for="item in stats.low_stock_list"
                                :key="item.id"
                                class="px-6 py-3 border-b list-hover"
                            >
                                <template v-slot:prepend>
                                    <v-avatar
                                        color="error"
                                        variant="tonal"
                                        rounded="lg"
                                        size="48"
                                    >
                                        <span
                                            class="text-error text-h6 font-weight-black"
                                            >{{ item.stock }}</span
                                        >
                                    </v-avatar>
                                </template>
                                <v-list-item-title
                                    class="font-weight-bold text-body-1"
                                    >{{ item.name }}</v-list-item-title
                                >
                                <v-list-item-subtitle
                                    class="text-medium-emphasis font-weight-medium"
                                >
                                    Supplier:
                                    {{ item.supplier?.name || "Tentukan!" }}
                                </v-list-item-subtitle>
                                <template v-slot:append>
                                    <v-btn
                                        v-if="
                                            $page.props.auth.user.role ===
                                            'admin'
                                        "
                                        icon="mdi-whatsapp"
                                        variant="tonal"
                                        color="success"
                                        size="small"
                                        class="rounded-lg"
                                        @click="sendWhatsApp(item)"
                                    ></v-btn>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>

                <v-col cols="12" md="7">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm bg-surface fill-height"
                    >
                        <v-toolbar flat class="px-4 border-b">
                            <v-icon color="indigo" class="me-2"
                                >mdi-history</v-icon
                            >
                            <v-toolbar-title
                                class="text-subtitle-1 font-weight-black"
                                >Aktivitas Terakhir</v-toolbar-title
                            >
                            <v-btn
                                variant="text"
                                size="small"
                                color="primary"
                                class="font-weight-bold px-4"
                                @click="router.visit('/history')"
                            >
                                Lihat Semua
                            </v-btn>
                        </v-toolbar>
                        <v-table
                            density="comfortable"
                            class="px-4 bg-transparent"
                        >
                            <thead>
                                <tr class="text-uppercase">
                                    <th
                                        class="text-caption font-weight-black text-medium-emphasis"
                                    >
                                        Barang
                                    </th>
                                    <th
                                        class="text-center text-caption font-weight-black text-medium-emphasis"
                                    >
                                        Aksi
                                    </th>
                                    <th
                                        class="text-center text-caption font-weight-black text-medium-emphasis"
                                    >
                                        Qty
                                    </th>
                                    <th
                                        class="text-right text-caption font-weight-black text-medium-emphasis"
                                    >
                                        Waktu
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="log in stats.recent_movements"
                                    :key="log.id"
                                >
                                    <td class="font-weight-bold text-body-2">
                                        {{ log.product?.name }}
                                    </td>
                                    <td class="text-center">
                                        <v-chip
                                            :color="
                                                log.type === 'in'
                                                    ? 'success'
                                                    : 'orange-darken-2'
                                            "
                                            size="x-small"
                                            class="font-weight-black px-3"
                                            variant="flat"
                                        >
                                            {{
                                                log.type === "in"
                                                    ? "MASUK"
                                                    : "KELUAR"
                                            }}
                                        </v-chip>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            :class="
                                                log.type === 'in'
                                                    ? 'text-success'
                                                    : 'text-error'
                                            "
                                            class="font-weight-black"
                                        >
                                            {{ log.type === "in" ? "+" : "-"
                                            }}{{ log.quantity }}
                                        </span>
                                    </td>
                                    <td
                                        class="text-right text-caption text-medium-emphasis"
                                    >
                                        {{
                                            new Date(
                                                log.created_at,
                                            ).toLocaleTimeString("id-ID", {
                                                hour: "2-digit",
                                                minute: "2-digit",
                                            })
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router,Head } from "@inertiajs/vue3";
import { Line } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
} from "chart.js";
import { computed, ref } from "vue";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
);

const props = defineProps({
    stats: Object,
    chartData: Object,
    topProducts: Array,
    filters: Object,
});

const formatIDR = (val) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val || 0);
};

const statCards = computed(() => {
    if (!props.stats) return [];
    return [
        {
            title: "Total Produk",
            value: props.stats.total_products || 0,
            icon: "mdi-package-variant-closed",
            color: "indigo",
            realColor: "#3F51B5",
        },
        {
            title: "Supplier",
            value: props.stats.total_suppliers || 0,
            icon: "mdi-truck-delivery-outline",
            color: "primary",
            realColor: "#2196F3",
        },
        {
            title: "Stok Kritis",
            value: props.stats.low_stock_count || 0,
            icon: "mdi-alert-circle-outline",
            color: "error",
            realColor: "#F44336",
        },
        {
            title: "Nilai Aset",
            value: formatIDR(props.stats.total_asset_value),
            icon: "mdi-cash-multiple",
            color: "success",
            realColor: "#4CAF50",
        },
    ];
});

const sendWhatsApp = (item) => {
    const rawPhone = item.supplier?.phone || "";
    if (!rawPhone) {
        alert(
            "Nomor HP Supplier belum ada, Isi dulu di data Master Supplier.",
        );
        return;
    }

    const downloadUrl = route("po.download", { product: item.id });
    const link = document.createElement("a");
    link.href = downloadUrl;
    link.setAttribute("download", "");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    let phone = rawPhone.replace(/\D/g, "");
    if (phone.startsWith("0")) {
        phone = "62" + phone.slice(1);
    }

    const message =
        `Halo ${item.supplier.name}, saya mau order barang *${item.name}*.%0A%0A` +
        `File PO saya lampirkan di bawah ini ya.%0A` +
        `Mohon segera dicek. Terima kasih!`;

    const waUrl = `https://wa.me/${phone}?text=${message}`;

    window.open(waUrl, "_blank");
};

const calculatePercent = (val) => {
    const max = props.topProducts?.[0]?.total || 100;
    return (val / max) * 100;
};

const chartConfig = computed(() => ({
    labels: props.chartData?.labels || [],
    datasets: [
        {
            label: "Barang Masuk",
            borderColor: "#4CAF50",
            backgroundColor: "rgba(76, 175, 80, 0.1)",
            data: props.chartData?.in || [],
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: "#4CAF50",
            borderWidth: 3,
        },
        {
            label: "Barang Keluar",
            borderColor: "#F44336",
            backgroundColor: "rgba(244, 67, 54, 0.1)",
            data: props.chartData?.out || [],
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: "#F44336",
            borderWidth: 3,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: "index", intersect: false },
    plugins: {
        legend: {
            position: "top",
            align: "end",
            labels: {
                usePointStyle: true,
                boxWidth: 8,
                font: { size: 12, weight: "bold" },
            },
        },
        tooltip: {
            backgroundColor: "rgba(255, 255, 255, 0.9)",
            titleColor: "#333",
            bodyColor: "#666",
            borderColor: "#ddd",
            borderWidth: 1,
            padding: 12,
            displayColors: true,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { borderDash: [5, 5], color: "#e0e0e0", drawBorder: false },
            ticks: { stepSize: 5 },
        },
        x: { grid: { display: false } },
    },
};

const filterForm = ref({
    month: props.filters?.month || new Date().getMonth() + 1,
    year: props.filters?.year || new Date().getFullYear(),
});

const months = [
    { name: "Januari", value: 1 },
    { name: "Februari", value: 2 },
    { name: "Maret", value: 3 },
    { name: "April", value: 4 },
    { name: "Mei", value: 5 },
    { name: "Juni", value: 6 },
    { name: "Juli", value: 7 },
    { name: "Agustus", value: 8 },
    { name: "September", value: 9 },
    { name: "Oktober", value: 10 },
    { name: "November", value: 11 },
    { name: "Desember", value: 12 },
];

const years = Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i);

const applyFilter = () => {
    router.get(
        route("dashboard"),
        { month: filterForm.value.month, year: filterForm.value.year },
        { preserveState: true, preserveScroll: true },
    );
};
</script>

<style scoped>
/* GANTI: Warna border pake variabel opacity biar masuk ke tema dark & light */
.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}
.shadow-sm {
    box-shadow: 0 2px 12px -4px rgba(0, 0, 0, 0.05) !important;
}
.stat-card {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1) !important;
}
/* GANTI: list-hover pake background adaptif */
.list-hover:hover {
    background-color: rgba(var(--v-theme-primary), 0.05);
    transition: background 0.2s;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
