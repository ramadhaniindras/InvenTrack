<template>
    <AuthenticatedLayout>
        <v-container class="py-10">
            <h1 class="text-h4 font-weight-bold mb-6 text-grey-darken-3">Pusat Laporan</h1>
            
            <v-row>
                <v-col
                    v-for="rep in reportTypes"
                    :key="rep.type"
                    cols="12"
                    md="3" 
                >
                    <v-card
                        border
                        flat
                        elevation="1" 
                        rounded="xl"
                        class="pa-6 text-center h-100 d-flex flex-column"
                    >
                        <v-avatar
                            :color="rep.color"
                            variant="tonal"
                            size="64"
                            class="mb-4 mx-auto"
                        >
                            <v-icon :icon="rep.icon" size="32"></v-icon>
                        </v-avatar>

                        <h3 class="text-h6 font-weight-bold">
                            {{ rep.title }}
                        </h3>

                        <p class="text-caption text-grey mb-6">
                            {{ rep.desc }}
                        </p>

                        <v-spacer></v-spacer>

                        <v-btn
                            block
                            color="primary"
                            variant="flat"
                            rounded="lg"
                            prepend-icon="mdi-download"
                            @click="downloadPDF(rep.type)" 
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

const reportTypes = [
    {
        type: "daily",
        title: "Harian",
        icon: "mdi-calendar-today",
        color: "blue",
        desc: "Transaksi hari ini saja",
    },
    {
        type: "weekly",
        title: "Mingguan",
        icon: "mdi-calendar-week",
        color: "purple",
        desc: "Transaksi sepekan terakhir",
    },
    {
        type: "monthly",
        title: "Bulanan",
        icon: "mdi-calendar-month",
        color: "success",
        desc: "Rekap transaksi bulan ini",
    },
    {
        type: "yearly",
        title: "Tahunan",
        icon: "mdi-calendar-range",
        color: "orange",
        desc: "Rekap transaksi tahun ini",
    },
];

const downloadPDF = (periode) => {
    window.open(`/reports/export?period=${periode}`, '_blank');
};
</script>