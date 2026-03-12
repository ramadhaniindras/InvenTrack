<template >
    <AuthenticatedLayout>
      <v-app>
        <v-main class="bg-grey-lighten-4 pt-4">
            <v-container py-10>
                <h1 class="text-h4 font-weight-bold mb-6 text-grey-darken-3">
                    Inventory Dashboard
                </h1>

                <v-row>
                    <v-col cols="12" md="4">
                        <v-card elevation="1" rounded="xl" class="pa-4">
                            <v-list-item
                                prepend-icon="mdi-package-variant"
                                title="Total Produk"
                                :subtitle="stats.total_products + ' Item'"
                            ></v-list-item>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card elevation="1" rounded="xl" class="pa-4">
                            <v-list-item
                                prepend-icon="mdi-alert-circle"
                                title="Stok Kritis"
                                :subtitle="stats.low_stock_count + ' Produk'"
                                base-color="error"
                            ></v-list-item>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-card elevation="1" rounded="xl" class="pa-4">
                            <v-list-item
                                prepend-icon="mdi-cash-multiple"
                                title="Nilai Aset"
                                :subtitle="formatIDR(stats.total_asset_value)"
                                base-color="success"
                            ></v-list-item>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row class="mt-6">
                    <v-col cols="12">
                        <v-card elevation="1" rounded="xl" class="pa-6">
                            <div class="d-flex align-center mb-6">
                                <div>
                                    <h3 class="text-h6 font-weight-bold">
                                        Trend Pergerakan Barang
                                    </h3>
                                    <p class="text-caption text-grey">
                                        Statistik stok masuk & keluar 7 hari
                                        terakhir
                                    </p>
                                </div>
                            </div>

                            <div style="height: 350px">
                                <Bar
                                    :data="chartConfig"
                                    :options="chartOptions"
                                />
                            </div>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row class="mt-6">
                    <v-col cols="12">
                        <v-card elevation="1" rounded="xl">
                            <v-card-title class="pa-4 font-weight-bold"
                                >History Terakhir</v-card-title
                            >
                            <v-divider></v-divider>
                            <v-table density="comfortable">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Tipe</th>
                                        <th>Jumlah</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="log in stats.recent_movements"
                                        :key="log.id"
                                    >
                                        <td>{{ log.product.name }}</td>
                                        <td>
                                            <v-chip
                                                :color="
                                                    log.type === 'in'
                                                        ? 'success'
                                                        : 'orange'
                                                "
                                                size="x-small"
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
                                        >
                                            {{ log.type === "in" ? "+" : "-"
                                            }}{{ log.quantity }}
                                        </td>
                                        <td class="text-grey text-caption">
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
                                    @click="$inertia.visit('/history')"
                                    >Lihat Semua Riwayat</v-btn
                                >
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
        </v-app>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
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


const formatIDR = (val) => {
  if (!val) return 'Rp 0'; 
  return new Intl.NumberFormat('id-ID', { 
    style: 'currency', 
    currency: 'IDR', 
    minimumFractionDigits: 0 
  }).format(val);
};

const chartConfig = computed(() => ({
    labels: props.chartData.labels,
    datasets: [
        {
            label: "Stok Masuk",
            backgroundColor: "#4CAF50",
            data: props.chartData.in,
            borderRadius: 6,
        },
        {
            label: "Stok Keluar",
            backgroundColor: "#F44336",
            data: props.chartData.out,
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
