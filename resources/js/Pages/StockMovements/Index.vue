<template>
    <Head title="Riwayat Stok" />

    <AuthenticatedLayout>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-card border flat class="rounded-lg">
                        <v-card-title class="d-flex align-center py-4">
                            <v-icon
                                icon="mdi-history"
                                class="me-2"
                                color="primary"
                            ></v-icon>
                            Histrory Stock
                            <v-spacer></v-spacer>

                            <v-text-field
                                v-model="search"
                                prepend-inner-icon="mdi-magnify"
                                label="Cari Produk / SKU..."
                                variant="solo-filled"
                                flat
                                hide-details
                                density="compact"
                                style="max-width: 300px"
                            ></v-text-field>
                        </v-card-title>

                        <v-divider></v-divider>

                        <v-table hover>
                            <thead>
                                <tr>
                                    <th class="text-left font-weight-bold">
                                        Waktu
                                    </th>
                                    <th class="text-left font-weight-bold">
                                        Produk
                                    </th>
                                    <th class="text-left font-weight-bold">
                                        Tipe
                                    </th>
                                    <th class="text-left font-weight-bold">
                                        Jumlah
                                    </th>
                                    <th class="text-left font-weight-bold">
                                        User
                                    </th>
                                    <th class="text-left font-weight-bold">
                                        Ket.
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in history.data" :key="item.id">
                                    <td>{{ formatDate(item.created_at) }}</td>
                                    <td>
                                        <div class="font-weight-bold">
                                            {{ item.product?.name }}
                                        </div>
                                        <div class="text-caption text-grey">
                                            {{ item.product?.sku }}
                                        </div>
                                    </td>
                                    <td>
                                        <v-chip
                                            :color="
                                                item.type === 'in'
                                                    ? 'success'
                                                    : 'error'
                                            "
                                            size="x-small"
                                            class="text-uppercase"
                                            variant="flat"
                                        >
                                            {{
                                                item.type === "in"
                                                    ? "Masuk"
                                                    : "Keluar"
                                            }}
                                        </v-chip>
                                    </td>
                                    <td
                                        :class="
                                            item.type === 'in'
                                                ? 'text-success'
                                                : 'text-error'
                                        "
                                        class="font-weight-bold"
                                    >
                                        {{ item.type === "in" ? "+" : "-"
                                        }}{{ item.quantity }}
                                    </td>
                                    <td>{{ item.user?.name || "Sistem" }}</td>
                                    <td class="text-caption">
                                        {{ item.notes || "-" }}
                                    </td>
                                </tr>
                                <tr v-if="history.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-center py-10 text-grey"
                                    >
                                        Data tidak ditemukan bro.
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>

                        <v-divider></v-divider>

                        <div class="pa-4 d-flex justify-center">
                            <v-pagination
                                v-model="history.current_page"
                                :length="history.last_page"
                                :total-visible="5"
                                @update:model-value="
                                    (page) =>
                                        router.get(
                                            route('history.index'),
                                            { page, search },
                                            { preserveState: true },
                                        )
                                "
                            ></v-pagination>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import debounce from "lodash/debounce";

const props = defineProps({
    history: Object,
    filters: Object,
});

const search = ref(props.filters.search);

// Fungsi Search otomatis (pake debounce biar gak berat)
const doSearch = debounce((value) => {
    router.get(
        route("history.index"),
        { search: value },
        {
            preserveState: true,
            replace: true,
        },
    );
}, 500);

watch(search, (value) => {
    doSearch(value);
});

// Helper buat format tanggal
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
