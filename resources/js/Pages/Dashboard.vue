<template>
    <AuthenticatedLayout>
        <v-container fluid class="pa-8 bg-grey-lighten-4">
            <v-row class="mb-4 align-center">
                <v-col cols="12" md="7">
                    <h1 class="text-h4 font-weight-bold text-grey-darken-3">InvenTrack Dashboard</h1>
                    <p class="text-subtitle-1 text-grey">Selamat datang kembali!</p>
                </v-col>
                
                <v-col cols="12" md="3">
                    <v-select
                        v-model="filterForm.month"
                        label="Bulan"
                        :items="months"
                        item-title="name"
                        item-value="value"
                        variant="outlined"
                        density="compact"
                        hide-details
                        @update:model-value="applyFilter"
                        class="bg-white"
                    ></v-select>
                </v-col>
                
                <v-col cols="12" md="2">
                    <v-select
                        v-model="filterForm.year"
                        label="Tahun"
                        :items="years"
                        variant="outlined"
                        density="compact"
                        hide-details
                        @update:model-value="applyFilter"
                        class="bg-white"
                    ></v-select>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="pa-4 border-sm fill-height">
                        <v-list-item
                            prepend-icon="mdi-package-variant"
                            title="Total Produk"
                            :subtitle="stats.total_products + ' Item'"
                        ></v-list-item>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="pa-4 border-sm fill-height" color="error" variant="tonal">
                        <v-list-item
                            prepend-icon="mdi-alert-circle"
                            title="Stok Kritis"
                            :subtitle="stats.low_stock_count + ' Produk Perlu Re-stock'"
                        ></v-list-item>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="pa-4 border-sm fill-height" color="success" variant="tonal">
                        <v-list-item
                            prepend-icon="mdi-cash-multiple"
                            title="Estimasi Nilai Aset"
                            :subtitle="formatIDR(stats.total_asset_value)"
                        ></v-list-item>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-4">
                <v-col cols="12" md="8">
                    <v-card elevation="1" rounded="xl" class="pa-6 border-sm fill-height">
                        <div class="mb-4">
                            <h3 class="text-h6 font-weight-bold">Trend Pergerakan Barang</h3>
                            <p class="text-caption text-grey">Data aktivitas harian dalam bulan terpilih</p>
                        </div>
                        <div style="height: 350px">
                            <Bar :data="chartConfig" :options="chartOptions" />
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="pa-6 border-sm fill-height">
                        <h3 class="text-h6 font-weight-bold mb-4">5 Barang Terlaris</h3>
                        <v-list class="px-0">
                            <div v-for="(item, index) in topProducts" :key="index" class="mb-4">
                                <div class="d-flex justify-space-between mb-1">
                                    <span class="font-weight-medium text-truncate" style="max-width: 150px;">{{ item.name }}</span>
                                    <span class="text-primary font-weight-bold">{{ item.total }} Pcs</span>
                                </div>
                                <v-progress-linear
                                    :model-value="calculatePercent(item.total)"
                                    color="primary"
                                    height="10"
                                    rounded
                                    striped
                                ></v-progress-linear>
                            </div>
                            <div v-if="topProducts.length === 0" class="text-center text-grey py-4">
                                Belum ada data keluar.
                            </div>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-4">
                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="border-sm fill-height">
                        <v-card-title class="pa-4 font-weight-bold text-error d-flex align-center">
                            <v-icon icon="mdi-alert-decagram" class="me-2"></v-icon>
                            Stok Menipis
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list lines="two" style="max-height: 350px; overflow-y: auto">
                            <v-list-item
                                v-for="item in stats.low_stock_list"
                                :key="item.id"
                                :title="item.name"
                                :subtitle="'Sisa: ' + item.stock + ' Pcs'"
                            >
                                <template v-slot:append>
                                    <v-btn icon="mdi-plus" size="x-small" color="primary" variant="flat" @click="router.visit('/products')"></v-btn>
                                </template>
                            </v-list-item>
                            <v-list-item v-if="stats.low_stock_list.length === 0" class="text-center text-grey py-4">
                                Stok masih aman terkendali!
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>

                <v-col cols="12" md="8">
                    <v-card elevation="1" rounded="xl" class="border-sm fill-height">
                        <v-card-title class="pa-4 font-weight-bold d-flex justify-space-between align-center">
                            History Terakhir
                            <v-btn variant="text" color="primary" @click="router.visit('/history')">Lihat Semua</v-btn>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-table density="comfortable">
                            <thead>
                                <tr>
                                    <th class="text-left font-weight-bold">Produk</th>
                                    <th class="text-center font-weight-bold">Tipe</th>
                                    <th class="text-center font-weight-bold">Jumlah</th>
                                    <th class="text-right font-weight-bold">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in stats.recent_movements" :key="log.id">
                                    <td>{{ log.product?.name || "Produk dihapus" }}</td>
                                    <td class="text-center">
                                        <v-chip :color="log.type === 'in' ? 'success' : 'orange'" size="x-small" variant="flat">
                                            {{ log.type === "in" ? "MASUK" : "KELUAR" }}
                                        </v-chip>
                                    </td>
                                    <td :class="log.type === 'in' ? 'text-success' : 'text-error'" class="text-center font-weight-bold">
                                        {{ log.type === "in" ? "+" : "-" }}{{ log.quantity }}
                                    </td>
                                    <td class="text-right text-caption text-grey">
                                        {{ new Date(log.created_at).toLocaleString("id-ID") }}
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
import { Head, router } from "@inertiajs/vue3";
import { Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";
import { computed, ref } from "vue"; // REF HARUS DI-IMPORT BRO!

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    stats: Object,
    chartData: Object,
    topProducts: Array,
    filters: Object,
});

const calculatePercent = (val) => {
    const max = props.topProducts[0]?.total || 100;
    return (val / max) * 100;
};

const formatIDR = (val) => {
    if (!val) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val);
};

const chartConfig = computed(() => ({
    labels: props.chartData?.labels || [],
    datasets: [
        {
            label: "Masuk",
            backgroundColor: "#4CAF50",
            data: props.chartData?.in || [],
            borderRadius: 6,
        },
        {
            label: "Keluar",
            backgroundColor: "#F44336",
            data: props.chartData?.out || [],
            borderRadius: 6,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: "bottom" },
    },
    scales: {
        y: { beginAtZero: true, grid: { color: "#f0f0f0" } },
        x: { grid: { display: false } },
    },
};

// Setup state filter dari props backend
const filterForm = ref({
    month: props.filters.month,
    year: props.filters.year,
});

const months = [
    { name: "Januari", value: 1 }, { name: "Februari", value: 2 },
    { name: "Maret", value: 3 }, { name: "April", value: 4 },
    { name: "Mei", value: 5 }, { name: "Juni", value: 6 },
    { name: "Juli", value: 7 }, { name: "Agustus", value: 8 },
    { name: "September", value: 9 }, { name: "Oktober", value: 10 },
    { name: "November", value: 11 }, { name: "Desember", value: 12 },
];

const years = Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i);

const applyFilter = () => {
    router.get(
        route("dashboard"),
        {
            month: filterForm.value.month,
            year: filterForm.value.year,
        },
        { preserveState: true, preserveScroll: true }
    );
};
</script>