<template>
    <AuthenticatedLayout>
        <v-app>
            <v-main class="bg-grey-lighten-4 pt-4">
                <v-container py-10>
                    <h1 class="text-h4 font-weight-bold mb-6">Pusat Laporan</h1>
                    <v-row>
                        <v-col
                            v-for="rep in reportTypes"
                            :key="rep.type"
                            cols="12"
                            md="4"
                        >
                            <v-card
                                border
                                flat
                                elevation="1" 
                                rounded="xl"
                                class="pa-6 text-center"
                            >
                                <v-avatar
                                    :color="rep.color"
                                    variant="tonal"
                                    size="64"
                                    class="mb-4"
                                >
                                    <v-icon :icon="rep.icon" size="32"></v-icon>
                                </v-avatar>
                                <h3 class="text-h6 font-weight-bold">
                                    {{ rep.title }}
                                </h3>
                                <p class="text-caption text-grey mb-6">
                                    {{ rep.desc }}
                                </p>
                                <v-btn
                                    block
                                    color="primary"
                                    variant="flat"
                                    rounded="lg"
                                    prepend-icon="mdi-download"
                                    @click="download(rep.type)"
                                >
                                    Download PDF
                                </v-btn>
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
        type: "yearly",
        title: "Tahunan",
        icon: "mdi-calendar-range",
        color: "orange",
        desc: "Rekap transaksi tahun ini",
    },
];

const download = (type) => {
    window.open(route("reports.export", { type }), "_blank");
};
</script>
