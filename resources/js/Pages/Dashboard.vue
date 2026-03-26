<template>
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-grey-lighten-4">
            <v-card
                variant="flat"
                rounded="xl"
                class="mb-8 pa-4 border-sm shadow-sm bg-white"
            >
                <v-row align="center">
                    <v-col cols="12" md="6">
                        <div class="d-flex align-center">
                            <v-avatar
                                color="primary-lighten-5"
                                size="48"
                                class="me-4"
                                rounded="lg"
                            >
                                <v-icon color="primary" size="28"
                                    >mdi-view-dashboard-outline</v-icon
                                >
                            </v-avatar>
                            <div>
                                <h1
                                    class="text-h5 font-weight-black text-grey-darken-4"
                                >
                                    InvenTrack Dashboard
                                </h1>
                                <p
                                    class="text-caption text-grey-darken-1 font-weight-medium"
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
                    <v-col cols="12" md="6">
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
                        class="pa-5 border-sm stat-card fill-height bg-white"
                        :style="`border-left: 5px solid ${stat.realColor} !important`"
                    >
                        <div class="d-flex justify-space-between align-center">
                            <div>
                                <div
                                    class="text-overline text-grey-darken-1 font-weight-bold mb-1"
                                >
                                    {{ stat.title }}
                                </div>
                                <div
                                    class="text-h4 font-weight-black mb-1 text-grey-darken-4"
                                >
                                    {{ stat.value }}
                                </div>
                            </div>
                            <v-avatar rounded="xl" size="56">
                                <v-icon :color="stat.color" size="32">{{
                                    stat.icon
                                }}</v-icon>
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
                        class="border-sm pa-6 fill-height bg-white"
                    >
                        <div
                            class="d-flex justify-space-between align-center mb-8"
                        >
                            <div>
                                <h3 class="text-h6 font-weight-black">
                                    Trend Masuk vs Keluar
                                </h3>
                                <p class="text-caption text-grey">
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
                        class="border-sm pa-6 fill-height bg-white"
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
                                    :class="
                                        isHovering ? 'bg-grey-lighten-4' : ''
                                    "
                                >
                                    <div
                                        class="d-flex justify-space-between align-center mb-2"
                                    >
                                        <span
                                            class="text-subtitle-2 font-weight-bold text-grey-darken-3"
                                            >{{ item.name }}</span
                                        >
                                        <v-chip
                                            size="x-small"
                                            color="primary"
                                            variant="flat"
                                            class="font-weight-black"
                                            >{{ item.total }} Pcs</v-chip
                                        >
                                    </div>
                                    <v-progress-linear
                                        :model-value="
                                            calculatePercent(item.total)
                                        "
                                        color="primary"
                                        height="8"
                                        rounded
                                        buffer-value="100"
                                    ></v-progress-linear>
                                </div>
                            </v-hover>
                        </div>
                        <div
                            v-else
                            class="text-center py-10 fill-height d-flex flex-column align-center justify-center"
                        >
                            <v-icon size="64" color="grey-lighten-2"
                                >mdi-chart-line-variant</v-icon
                            >
                            <p class="text-grey mt-2">Belum ada data</p>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-6">
                <v-col cols="12" md="5">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm bg-white fill-height overflow-hidden"
                    >
                        <v-toolbar color="white" flat class="px-4 border-b-sm">
                            <v-icon color="error" class="me-2"
                                >mdi-alert-circle-outline</v-icon
                            >
                            <v-toolbar-title
                                class="text-subtitle-1 font-weight-black"
                                >Rekomendasi Order!</v-toolbar-title
                            >
                            <v-btn
                                icon="mdi-truck-fast-outline"
                                variant="tonal"
                                color="primary"
                                size="small"
                                rounded="lg"
                                @click="router.visit('/suppliers')"
                            ></v-btn>
                        </v-toolbar>
                        <v-list lines="two" class="pa-0">
                            <v-list-item
                                v-for="item in stats.low_stock_list"
                                :key="item.id"
                                class="px-6 py-3 border-b-sm list-hover"
                            >
                                <template v-slot:prepend>
                                    <v-avatar
                                        color="red-lighten-5"
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
                                    class="text-grey-darken-1 font-weight-medium"
                                >
                                    Supplier:
                                    {{ item.supplier?.name || "Tentukan!" }}
                                </v-list-item-subtitle>
                                <template v-slot:append>
                                    <v-btn
                                        icon="mdi-chevron-right"
                                        variant="text"
                                        size="small"
                                        color="grey"
                                        @click="router.visit('/products')"
                                    ></v-btn>
                                </template>
                            </v-list-item>
                            <div
                                v-if="
                                    !stats.low_stock_list ||
                                    stats.low_stock_list.length === 0
                                "
                                class="text-center py-10 px-4 fill-height d-flex flex-column align-center justify-center"
                            >
                                <v-icon size="48" color="success"
                                    >mdi-check-decagram</v-icon
                                >
                                <p
                                    class="text-grey-darken-1 font-weight-bold mt-2"
                                >
                                    Stok aman terkendali!
                                </p>
                            </div>
                        </v-list>
                    </v-card>
                </v-col>

                <v-col cols="12" md="7">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm bg-white fill-height"
                    >
                        <v-toolbar color="white" flat class="px-4 border-b-sm">
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
                                >Lihat Semua</v-btn
                            >
                        </v-toolbar>
                        <v-table
                            density="comfortable"
                            class="px-4 bg-transparent"
                        >
                            <thead>
                                <tr class="text-uppercase">
                                    <th
                                        class="text-caption font-weight-black text-grey"
                                    >
                                        Barang
                                    </th>
                                    <th
                                        class="text-center text-caption font-weight-black text-grey"
                                    >
                                        Aksi
                                    </th>
                                    <th
                                        class="text-center text-caption font-weight-black text-grey"
                                    >
                                        Qty
                                    </th>
                                    <th
                                        class="text-right text-caption font-weight-black text-grey"
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
                                            {{ log.type === "in" ? "+" : "-" }}
                                            {{ log.quantity }}
                                        </span>
                                    </td>
                                    <td
                                        class="text-right text-caption text-grey-darken-1"
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
import { router } from "@inertiajs/vue3";
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

// Fixed logic for stat cards to prevent "undefined" errors
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
.border-sm {
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
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
.list-hover:hover {
    background-color: #f8f9fa;
    transition: background 0.2s;
}
.transition-all {
    transition: all 0.3s ease;
}
</style>
