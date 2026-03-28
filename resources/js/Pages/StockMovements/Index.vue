<template>
    <Head title="Riwayat Stok" />
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-background">
            <v-row>
                <v-col cols="12">
                    <v-card
                        elevation="0"
                        rounded="xl"
                        class="border-sm shadow-sm bg-surface"
                    >
                        <v-card-title class="d-flex align-center py-6 px-6">
                            <v-avatar
                                color="primary"
                                variant="tonal"
                                size="40"
                                class="me-3"
                            >
                                <v-icon icon="mdi-history"></v-icon>
                            </v-avatar>
                            <div>
                                <div class="text-h6 font-weight-black">
                                    History Stock
                                </div>
                                <div class="text-caption text-medium-emphasis">
                                    Pantau semua mutasi barang bengkel
                                </div>
                            </div>

                            <v-spacer></v-spacer>

                            <v-text-field
                                v-model="search"
                                prepend-inner-icon="mdi-magnify"
                                label="Cari Produk / SKU..."
                                variant="outlined"
                                density="compact"
                                hide-details
                                rounded="lg"
                                style="max-width: 300px"
                                class="bg-background"
                            ></v-text-field>
                        </v-card-title>

                        <v-divider></v-divider>

                        <v-table hover class="bg-surface">
                            <thead>
                                <tr>
                                    <th class="text-overline font-weight-black">
                                        Waktu
                                    </th>
                                    <th class="text-overline font-weight-black">
                                        Produk
                                    </th>
                                    <th
                                        class="text-overline font-weight-black text-center"
                                    >
                                        Tipe
                                    </th>
                                    <th
                                        class="text-overline font-weight-black text-center"
                                    >
                                        Jumlah
                                    </th>
                                    <th class="text-overline font-weight-black">
                                        Oleh (User)
                                    </th>
                                    <th class="text-overline font-weight-black">
                                        Keterangan
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="item in history.data"
                                    :key="item.id"
                                    class="list-item-hover"
                                >
                                    <td class="text-caption font-weight-medium">
                                        {{ formatDate(item.created_at) }}
                                    </td>
                                    <td class="py-3">
                                        <div
                                            class="font-weight-bold text-body-2"
                                        >
                                            {{ item.product?.name }}
                                        </div>
                                        <div
                                            class="text-caption text-primary font-weight-medium"
                                        >
                                            {{ item.product?.sku }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <v-chip
                                            :color="
                                                item.type === 'in'
                                                    ? 'success'
                                                    : 'orange-darken-2'
                                            "
                                            size="x-small"
                                            class="text-uppercase font-weight-black px-3"
                                            variant="flat"
                                        >
                                            <v-icon start size="12">
                                                {{
                                                    item.type === "in"
                                                        ? "mdi-arrow-down-bold"
                                                        : "mdi-arrow-up-bold"
                                                }}
                                            </v-icon>
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
                                        class="font-weight-black text-center text-body-1"
                                    >
                                        {{ item.type === "in" ? "+" : "-"
                                        }}{{ item.quantity }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-center">
                                            <v-avatar
                                                size="24"
                                                color="grey-lighten-2"
                                                class="me-2 text-caption font-weight-bold"
                                            >
                                                {{
                                                    item.user?.name
                                                        ?.charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </v-avatar>
                                            <span class="text-body-2">{{
                                                item.user?.name || "Sistem"
                                            }}</span>
                                        </div>
                                    </td>
                                    <td
                                        class="text-caption text-medium-emphasis"
                                    >
                                        {{
                                            item.notes || item.reference || "-"
                                        }}
                                    </td>
                                </tr>

                                <tr v-if="history.data.length === 0">
                                    <td colspan="6" class="text-center py-15">
                                        <v-icon
                                            size="64"
                                            color="grey-lighten-1"
                                            class="mb-4"
                                            >mdi-database-off-outline</v-icon
                                        >
                                        <div
                                            class="text-grey font-weight-medium"
                                        >
                                            Data tidak ditemukan bro.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>

                        <v-divider></v-divider>

                        <div class="pa-4 d-flex justify-center bg-surface">
                            <v-pagination
                                v-model="history.current_page"
                                :length="history.last_page"
                                :total-visible="5"
                                active-color="primary"
                                variant="flat"
                                density="comfortable"
                                @update:model-value="changePage"
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
import { useForm, router,Head } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import debounce from "lodash/debounce";

const props = defineProps({
    history: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");

const changePage = (page) => {
    router.get(
        route("history.index"),
        { page, search: search.value },
        { preserveState: true, preserveScroll: true },
    );
};

const doSearch = debounce((value) => {
    router.get(
        route("history.index"),
        { search: value },
        { preserveState: true, replace: true },
    );
}, 500);

watch(search, (value) => {
    doSearch(value);
});

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

<style scoped>
.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}
.shadow-sm {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}
.text-overline {
    font-size: 0.7rem !important;
    letter-spacing: 1px !important;
    color: rgba(var(--v-theme-on-surface), 0.6);
}
.list-item-hover:hover {
    background-color: rgba(var(--v-theme-primary), 0.02);
    transition: background-color 0.2s ease;
}
</style>
