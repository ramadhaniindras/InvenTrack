<template>
    <AuthenticatedLayout>
        <v-container class="py-10">
            <h1 class="text-h4 font-weight-bold mb-6 text-grey-darken-3">
                Report Center
            </h1>

            <v-row
                ><v-col
                    v-for="rep in reportTypes"
                    :key="rep.type"
                    cols="12"
                    sm="6"
                    md="3"
                >
                    <v-card
                        border
                        flat
                        elevation="1"
                        rounded="xl"
                        class="pa-6 text-center h-100 d-flex flex-column transition-swing"
                    >
                        <v-avatar
                            :color="rep.color"
                            variant="tonal"
                            size="64"
                            class="mb-4 mx-auto"
                        >
                            <v-icon :icon="rep.icon" size="32"></v-icon>
                        </v-avatar>

                        <h3 class="text-h6 font-weight-bold text-grey-darken-4">
                            {{ rep.title }}
                        </h3>

                        <p
                            class="text-caption text-grey-darken-1 mb-6"
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
                                flat
                                class="custom-input-style"
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
        desc: "Pilih tanggal transaksi spesifik",
        value: new Date().toISOString().substr(0, 10), 
    },
    {
        type: "weekly",
        title: "Mingguan",
        icon: "mdi-calendar-week",
        color: "purple",
        desc: "Rekap 7 hari mulai dari tanggal ini",
        value: new Date().toISOString().substr(0, 10),
    },
    {
        type: "monthly",
        title: "Bulanan",
        icon: "mdi-calendar-month",
        color: "success",
        desc: "Pilih bulan dan tahun",
        value: new Date().toISOString().substr(0, 7), 
    },
    {
        type: "yearly",
        title: "Tahunan",
        icon: "mdi-calendar-range",
        color: "orange",
        desc: "Rekap transaksi satu tahun penuh",
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
        alert("Pilih periode dulu!");
        return;
    }
    window.location.href = `/reports/export?period=${rep.type}&filter=${rep.value}`;
};
</script>

<style scoped>
.transition-swing:hover {
    transform: translateY(-8px);
    transition: all 0.3s ease;
}
</style>
