<template>
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-background min-vh-100">
            <h1
                class="text-h4 font-weight-black mb-6 text-primary d-flex align-center"
            >
                <v-icon icon="mdi-file-chart-outline" class="me-3"></v-icon>
                Report Center
            </h1>

            <v-row>
                <v-col
                    v-for="rep in reportTypes"
                    :key="rep.type"
                    cols="12"
                    sm="6"
                    md="3"
                >
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="pa-6 text-center h-100 d-flex flex-column transition-swing border-sm bg-surface"
                    >
                        <v-avatar
                            :color="rep.color"
                            variant="tonal"
                            size="64"
                            class="mb-4 mx-auto rounded-xl"
                        >
                            <v-icon :icon="rep.icon" size="32"></v-icon>
                        </v-avatar>

                        <h3 class="text-h6 font-weight-bold mb-1">
                            Laporan {{ rep.title }}
                        </h3>

                        <p
                            class="text-caption text-medium-emphasis mb-6"
                            style="min-height: 40px"
                        >
                            {{ rep.desc }}
                        </p>

                        <v-spacer></v-spacer>

                        <div class="mb-6">
                            <v-text-field
                                v-model="rep.value"
                                :type="getInputType(rep.type)"
                                label="Pilih Periode"
                                density="comfortable"
                                variant="outlined"
                                rounded="lg"
                                hide-details="auto"
                                color="primary"
                                class="custom-date-input"
                            ></v-text-field>
                        </div>

                        <v-btn
                            block
                            color="primary"
                            variant="flat"
                            rounded="lg"
                            prepend-icon="mdi-download"
                            class="text-none font-weight-bold py-6"
                            @click="downloadPDF(rep)"
                        >
                            Download PDF
                        </v-btn>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";

const reportTypes = ref([
    {
        type: "daily",
        title: "Harian",
        icon: "mdi-calendar-today",
        color: "blue",
        desc: "Pilih tanggal transaksi spesifik untuk melihat detail harian",
        value: new Date().toISOString().substr(0, 10),
    },
    {
        type: "weekly",
        title: "Mingguan",
        icon: "mdi-calendar-week",
        color: "purple",
        desc: "Rekap pergerakan stok selama 7 hari ke depan",
        value: new Date().toISOString().substr(0, 10),
    },
    {
        type: "monthly",
        title: "Bulanan",
        icon: "mdi-calendar-month",
        color: "success",
        desc: "Rekapitulasi stok berdasarkan bulan dan tahun",
        value: new Date().toISOString().substr(0, 7),
    },
    {
        type: "yearly",
        title: "Tahunan",
        icon: "mdi-calendar-range",
        color: "orange",
        desc: "Analisa pergerakan stok selama satu tahun penuh",
        value: new Date().getFullYear().toString(),
    },
]);

const getInputType = (type) => {
    if (type === "monthly") return "month";
    if (type === "yearly") return "number";
    return "date";
};

const downloadPDF = (rep) => {
    if (!rep.value) {
        alert("Pilih periode dulu, Bro!");
        return;
    }
    // Menggunakan window.open agar user tidak terlempar dari halaman
    const url = `/reports/export?period=${rep.type}&filter=${rep.value}`;
    window.open(url, "_blank");
};
</script>

<style scoped>
.transition-swing {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.transition-swing:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 20px -10px rgba(var(--v-theme-primary), 0.2) !important;
}

.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}

/* Biar icon date picker nyesuain warna teks di mode gelap */
.custom-date-input :deep(input::-webkit-calendar-picker-indicator) {
    filter: invert(var(--v-theme-on-surface-variant-filter, 0));
    cursor: pointer;
}
</style>
