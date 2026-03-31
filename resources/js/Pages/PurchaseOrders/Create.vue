<template>
    <Head title="Buat PO Baru" />
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-background">
            <v-card elevation="0" rounded="xl" class="border-sm shadow-sm bg-surface">
                <v-toolbar flat class="border-b px-4 py-2 bg-surface">
                    <v-icon icon="mdi-file-document-edit-outline" color="primary" class="me-3"></v-icon>
                    <v-toolbar-title class="font-weight-bold">Buat Purchase Order Manual</v-toolbar-title>
                </v-toolbar>

                <v-card-text class="pa-6">
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-select
                                v-model="form.supplier_id"
                                label="Pilih Supplier"
                                :items="suppliers"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-truck-delivery"
                                :error-messages="form.errors.supplier_id"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                label="Catatan (Opsional)"
                                v-model="form.notes"
                                variant="outlined"
                                density="comfortable"
                                prepend-inner-icon="mdi-note-text-outline"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-divider class="my-6"></v-divider>

                    <div class="d-flex align-center justify-space-between mb-4">
                        <h3 class="text-h6 font-weight-bold">Daftar Barang</h3>
                        <v-btn color="primary" variant="tonal" prepend-icon="mdi-plus" @click="addItem" rounded="lg">
                            Tambah Baris
                        </v-btn>
                    </div>

                    <v-row v-for="(item, index) in form.items" :key="index" class="mb-2 align-center border rounded-lg pa-2 ma-0 bg-background">
                        <v-col cols="12" md="5">
                            <v-autocomplete
                                v-model="item.product_id"
                                :items="products"
                                item-title="name"
                                item-value="id"
                                label="Cari Produk"
                                variant="outlined"
                                density="compact"
                                hide-details
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="6" md="3">
                            <v-text-field
                                v-model.number="item.quantity"
                                label="Jumlah"
                                type="number"
                                variant="outlined"
                                density="compact"
                                hide-details
                            ></v-text-field>
                        </v-col>
                        <v-col cols="6" md="3">
                            <v-text-field
                                v-model.number="item.unit_price"
                                label="Harga Satuan (Rp)"
                                type="number"
                                variant="outlined"
                                density="compact"
                                hide-details
                                prefix="Rp"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="1" class="text-right">
                            <v-btn icon="mdi-delete-outline" color="error" variant="text" @click="removeItem(index)" :disabled="form.items.length === 1"></v-btn>
                        </v-col>
                    </v-row>

                    <v-alert v-if="form.errors.items" type="error" variant="tonal" class="mt-4">
                        {{ form.errors.items }}
                    </v-alert>
                </v-card-text>

                <v-divider></v-divider>
                <v-card-actions class="pa-6">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="$inertia.visit('/purchase-orders')" class="me-2">Batal</v-btn>
                    <v-btn color="primary" variant="flat" size="large" rounded="lg" @click="submit" :loading="form.processing">
                        Simpan & Buat PO
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { notify } from "@/Utils/alert";

const props = defineProps({
    suppliers: Array,
    products: Array,
});

const form = useForm({
    supplier_id: null,
    notes: '',
    items: [
        { product_id: null, quantity: 1, unit_price: 0 }
    ],
});

const addItem = () => {
    form.items.push({ product_id: null, quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

const submit = () => {
    form.post(route('purchase-orders.store'), {
        onSuccess: () => {
            notify("PO Berhasil dibuat!", "success");
            form.reset();
        },
        onError: () => {
            notify("Cek kembali inputan lu, Bro!", "error");
        }
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
</style>