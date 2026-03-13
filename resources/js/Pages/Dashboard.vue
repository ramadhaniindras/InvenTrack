<template>
    <Head title="Inventory Dashboard" />

    <AuthenticatedLayout>
        <v-container fluid class="pa-8">
            <v-row mb-6>
                <v-col cols="12">
                    <h1 class="text-h4 font-weight-bold text-grey-darken-3">
                        Inventory Dashboard
                    </h1>
                    <p class="text-subtitle-1 text-grey">
                        Selamat datang kembali, Bro!
                    </p>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="pa-4 border-sm">
                        <v-list-item
                            prepend-icon="mdi-package-variant"
                            title="Total Produk"
                            :subtitle="stats.total_products + ' Item'"
                        ></v-list-item>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card
                        elevation="1"
                        rounded="xl"
                        class="pa-4 border-sm"
                        color="error"
                        variant="tonal"
                    >
                        <v-list-item
                            prepend-icon="mdi-alert-circle"
                            title="Stok Kritis"
                            :subtitle="
                                stats.low_stock_count + ' Produk Perlu Re-stock'
                            "
                        ></v-list-item>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card
                        elevation="1"
                        rounded="xl"
                        class="pa-4 border-sm"
                        color="success"
                        variant="tonal"
                    >
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
                    <v-card elevation="1" rounded="xl" class="pa-6 border-sm">
                        <div class="mb-4">
                            <h3 class="text-h6 font-weight-bold">
                                Trend Pergerakan Barang
                            </h3>
                            <p class="text-caption text-grey">
                                Data 7 hari terakhir
                            </p>
                        </div>
                        <div style="height: 300px">
                            <Bar :data="chartConfig" :options="chartOptions" />
                        </div>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card elevation="1" rounded="xl" class="border-sm">
                        <v-card-title
                            class="pa-4 font-weight-bold text-error d-flex align-center"
                        >
                            <v-icon
                                icon="mdi-alert-decagram"
                                class="me-2"
                            ></v-icon>
                            Stok Menipis
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list
                            lines="two"
                            style="max-height: 315px; overflow-y: auto"
                        >
                            <v-list-item
                                v-for="item in stats.low_stock_list"
                                :key="item.id"
                                :title="item.name"
                                :subtitle="'Sisa: ' + item.stock + ' Pcs'"
                            >
                                <template v-slot:append>
                                    <v-btn
                                        icon="mdi-plus"
                                        size="x-small"
                                        color="primary"
                                        variant="flat"
                                        @click="router.visit('/products')"
                                    ></v-btn>
                                </template>
                            </v-list-item>
                            <v-list-item
                                v-if="stats.low_stock_list.length === 0"
                                class="text-center text-grey"
                            >
                                Stok masih aman terkendali!
                            </v-list-item>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-4">
                <v-col cols="12">
                    <v-card elevation="1" rounded="xl" class="border-sm">
                        <v-card-title class="pa-4 font-weight-bold"
                            >History Terakhir</v-card-title
                        >
                        <v-divider></v-divider>
                        <v-table>
                            <thead>
                                <tr>
                                    <th class="font-weight-bold">Produk</th>
                                    <th class="font-weight-bold text-center">
                                        Tipe
                                    </th>
                                    <th class="font-weight-bold text-center">
                                        Jumlah
                                    </th>
                                    <th class="font-weight-bold text-right">
                                        Waktu
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="log in stats.recent_movements"
                                    :key="log.id"
                                >
                                    <td>
                                        {{
                                            log.product?.name ||
                                            "Produk dihapus"
                                        }}
                                    </td>
                                    <td class="text-center">
                                        <v-chip
                                            :color="
                                                log.type === 'in'
                                                    ? 'success'
                                                    : 'orange'
                                            "
                                            size="x-small"
                                            variant="flat"
                                        >
                                            {{
                                                log.type === "in"
                                                    ? "MASUK"
                                                    : "KELUAR"
                                            }}
                                        </v-chip>
                                    </td>
                                    <td
                                        :class="
                                            log.type === 'in'
                                                ? 'text-success'
                                                : 'text-error'
                                        "
                                        class="text-center font-weight-bold"
                                    >
                                        {{ log.type === "in" ? "+" : "-"
                                        }}{{ log.quantity }}
                                    </td>
                                    <td
                                        class="text-right text-caption text-grey"
                                    >
                                        {{
                                            new Date(
                                                log.created_at,
                                            ).toLocaleString("id-ID")
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                        <v-divider></v-divider>
                        <v-card-actions>
                            <v-btn
                                variant="text"
                                color="primary"
                                block
                                @click="router.visit('/history')"
                            >
                                Lihat Semua Riwayat
                            </v-btn>
                        </v-card-actions>
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
import { computed } from "vue";

// Registrasi ChartJS
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
);

const props = defineProps({
    stats: Object,
    chartData: Object,
});

// Formatter Mata Uang
const formatIDR = (val) => {
    if (!val) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val);
};

// Konfigurasi Grafik
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
</script>
