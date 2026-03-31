<template>
    <Head title="Manual PO" />
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-background">
            <v-card
                elevation="0"
                rounded="xl"
                class="border-sm shadow-sm bg-surface"
            >
                <v-toolbar flat class="border-b px-4 py-2 bg-surface">
                    <v-icon
                        icon="mdi-file-document-edit-outline"
                        color="primary"
                        class="me-3"
                    ></v-icon>
                    <v-toolbar-title class="font-weight-bold">
                        Buat Purchase Order Manual
                    </v-toolbar-title>
                    
                    <v-spacer></v-spacer>

                    <v-btn
                        variant="text"
                        prepend-icon="mdi-arrow-left"
                        @click="router.visit(route('products.index'))"
                        rounded="lg"
                    >
                        Kembali
                    </v-btn>
                </v-toolbar>

                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="form.supplier_id"
                                label="1. Pilih Supplier Tujuan"
                                :items="suppliers"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-truck-delivery"
                                :error-messages="form.errors.supplier_id"
                                @update:model-value="onSupplierChange"
                                placeholder="Cari supplier..."
                                rounded="lg"
                            ></v-select>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="form.notes"
                                label="Catatan"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-note-text-outline"
                                
                                rounded="lg"
                                :disabled="!form.supplier_id"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-divider class="my-6"></v-divider>

                    <div class="d-flex align-center justify-space-between mb-4">
                        <div>
                            <h3 class="text-h6 font-weight-bold">2. Daftar Barang Pesanan</h3>
                            <span v-if="!form.supplier_id" class="text-caption text-error">
                                * Pilih supplier terlebih dahulu untuk memfilter barang
                            </span>
                            <span v-else class="text-caption text-medium-emphasis">
                                Menampilkan barang khusus dari supplier yang dipilih
                            </span>
                        </div>
                        
                        <v-btn
                            color="primary"
                            variant="tonal"
                            prepend-icon="mdi-plus"
                            @click="addItem"
                            rounded="lg"
                            :disabled="!form.supplier_id"
                        >
                            Tambah Baris
                        </v-btn>
                    </div>

                    <v-row
                        v-for="(item, index) in form.items"
                        :key="index"
                        class="mb-3 align-center border rounded-lg pa-2 ma-0 bg-background transition-swing"
                        :style="!form.supplier_id ? 'opacity: 0.5; pointer-events: none;' : ''"
                    >
                        <v-col cols="12" md="5">
                            <v-autocomplete
                                v-model="item.product_id"
                                :items="filteredProducts"
                                item-title="name"
                                item-value="id"
                                label="Cari Produk"
                                variant="outlined"
                                density="compact"
                                hide-details
                                no-data-text="Barang tidak ditemukan"
                                prepend-inner-icon="mdi-magnify"
                                @update:model-value="onProductChange(index)"
                            ></v-autocomplete>
                        </v-col>

                        <v-col cols="6" md="2">
                            <v-text-field
                                v-model.number="item.quantity"
                                label="Jumlah"
                                type="number"
                                variant="outlined"
                                density="compact"
                                hide-details
                                min="1"
                                prepend-inner-icon="mdi-numeric"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="6" md="4">
                            <v-text-field
                                v-model.number="item.unit_price"
                                label="Harga Satuan"
                                type="number"
                                variant="outlined"
                                density="compact"
                                hide-details
                                prefix="Rp"
                                prepend-inner-icon="mdi-cash"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="1" class="text-right">
                            <v-btn
                                icon="mdi-delete-outline"
                                color="red"
                                variant="text"
                                @click="removeItem(index)"
                                :disabled="form.items.length === 1"
                            ></v-btn>
                        </v-col>
                    </v-row>

                    <v-alert
                        v-if="form.errors.items"
                        type="error"
                        variant="tonal"
                        class="mt-4 rounded-lg"
                        closable
                    >
                        {{ form.errors.items }}
                    </v-alert>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions class="pa-6">
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primary"
                        variant="flat"
                        size="large"
                        rounded="lg"
                        block
                        @click="submit"
                        :loading="form.processing"
                        :disabled="!form.supplier_id"
                        prepend-icon="mdi-content-save-check"
                    >
                        Simpan & Download PDF Pesanan
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { notify } from "@/Utils/alert";

const props = defineProps({
    suppliers: Array,
    products: Array,
});

const form = useForm({
    supplier_id: null,
    notes: "",
    items: [{ product_id: null, quantity: 1, unit_price: 0 }],
});

// FILTER BARANG: Hanya munculkan barang yang supplier_id nya cocok dengan pilihan
const filteredProducts = computed(() => {
    if (!form.supplier_id) return [];
    return props.products.filter(p => p.supplier_id === form.supplier_id);
});

// EVENT: Kalau ganti supplier, reset list barang (biar gak campur aduk)
const onSupplierChange = () => {
    form.items = [{ product_id: null, quantity: 1, unit_price: 0 }];
    notify("Supplier dipilih, daftar barang disesuaikan.", "info");
};

// EVENT: Kalau pilih produk, otomatis isi harga satuannya
const onProductChange = (index) => {
    const selectedProductId = form.items[index].product_id;
    if (selectedProductId) {
        const product = props.products.find((p) => p.id === selectedProductId);
        if (product) {
            form.items[index].unit_price = product.price;
        }
    }
};

const addItem = () => {
    form.items.push({ product_id: null, quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route("manual-po.store"), {
        onSuccess: () => {
            // Setelah sukses simpan, panggil route download latest yang di Controller
            window.open(route('manual-po.download-latest'), '_blank');
            form.reset();
            notify("Purchase Order Berhasil Dibuat!", "success");
        },
        onError: () => {
            notify("Periksa kembali", "error");
        }
    });
};
</script>

<style scoped>
/* Menyamakan style dengan Master Supplier lu */
.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}

.shadow-sm {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}


.transition-swing {
    transition: all 0.3s ease-in-out;
}

/* Biar rapi pas di hover row barangnya */
.transition-swing:hover {
    border-color: rgba(var(--v-theme-primary), 0.5) !important;
    background-color: rgba(var(--v-theme-primary), 0.02) !important;
}
</style>