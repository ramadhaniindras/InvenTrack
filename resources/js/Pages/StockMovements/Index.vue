<template>
  <AuthenticatedLayout>
    <v-app>
        <v-main class="bg-grey-lighten-4">
            <v-container py-10>
                <v-card elevation="1" rounded="lg">
                    <v-toolbar color="white" flat class="border-b">
                        <v-icon
                            icon="mdi-history"
                            class="ms-4 me-2"
                            color="primary"
                        ></v-icon>
                        <v-toolbar-title class="font-weight-bold"
                            >Stock History Log</v-toolbar-title
                        >
                        <v-spacer></v-spacer>
                        <div class="d-flex ga-7 me-4">
                            <v-btn
                                color="primary"
                                variant="flat"
                                prepend-icon="mdi-history"
                                class="text-none font-weight-bold px-6"
                                rounded="lg"
                                @click="$inertia.visit('/products')"
                            >
                                Kembali ke Inventory
                            </v-btn>
                        </div>
                    </v-toolbar>

                    <v-divider></v-divider>

                    <v-data-table
                        :headers="headers"
                        :items="movements.data"
                        hover
                        class="pa-2"
                    >
                        <template v-slot:item.product.name="{ item }">
                            <span class="font-weight-medium">{{
                                item.product.name
                            }}</span>
                            <div class="text-caption text-grey">
                                {{ item.product.sku }}
                            </div>
                        </template>

                        <template v-slot:item.type="{ item }">
                            <v-chip
                                :color="
                                    item.type === 'in' ? 'success' : 'orange'
                                "
                                size="x-small"
                                variant="flat"
                                class="text-uppercase"
                            >
                                {{ item.type === "in" ? "Masuk" : "Keluar" }}
                            </v-chip>
                        </template>

                        <template v-slot:item.quantity="{ item }">
                            <span
                                :class="
                                    item.type === 'in'
                                        ? 'text-success'
                                        : 'text-error'
                                "
                                class="font-weight-bold"
                            >
                                {{ item.type === "in" ? "+" : "-"
                                }}{{ item.quantity }}
                            </span>
                        </template>

                        <template v-slot:item.created_at="{ item }">
                            {{
                                new Date(item.created_at).toLocaleString(
                                    "id-ID",
                                )
                            }}
                        </template>
                    </v-data-table>
                </v-card>
            </v-container>
        </v-main>
    </v-app>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
const props = defineProps({
    movements: Object,
});

const headers = [
    { title: "Tanggal", key: "created_at" },
    { title: "Produk", key: "product.name" },
    { title: "Tipe", key: "type", align: "center" },
    { title: "Jumlah", key: "quantity", align: "center" },
    { title: "Referensi", key: "reference" },
    { title: "Catatan", key: "notes" },
];
</script>
