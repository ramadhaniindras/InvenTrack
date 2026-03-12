<template>
    <AuthenticatedLayout>
        <v-app>
            <v-main class="bg-grey-lighten-4">
                <v-container fluid class="px-6 py-10">
                    <v-card elevation="1" rounded="xl">
                        <v-toolbar color="white" flat class="border-b px-4 py-2">
                            <v-icon icon="mdi-package-variant-closed" color="primary" class="me-3"></v-icon>
                            <v-toolbar-title class="font-weight-bold">Inventory List</v-toolbar-title>
                            
                            <v-spacer></v-spacer>

                            <v-text-field
                                v-model="search"
                                prepend-inner-icon="mdi-magnify"
                                placeholder="Cari barang atau SKU..."
                                variant="outlined"
                                density="compact"
                                hide-details
                                color="primary"
                                class="me-4"
                                style="max-width: 300px"
                                rounded="lg"
                                clearable
                            ></v-text-field>

                            <div class="d-flex ga-2">
                                <v-btn color="primary" variant="tonal" prepend-icon="mdi-history" rounded="lg" class="text-none font-weight-bold" @click="$inertia.visit('/history')">
                                    History
                                </v-btn>
                                <v-btn color="primary" variant="flat" prepend-icon="mdi-plus" rounded="lg" class="text-none font-weight-bold" @click="openDialog">
                                    Tambah Barang
                                </v-btn>
                                <v-btn color="error" variant="outlined" prepend-icon="mdi-file-pdf-box" rounded="lg" class="text-none" @click="exportToPdf">
                                    PDF
                                </v-btn>
                            </div>
                        </v-toolbar>

                        <v-alert v-if="$page.props.filters?.category_id" color="primary" variant="tonal" class="ma-4 rounded-lg" density="compact">
                            <div class="d-flex align-center justify-space-between">
                                <span>Menampilkan produk untuk kategori spesifik</span>
                                <v-btn size="x-small" color="primary" variant="flat" @click="$inertia.visit(route('products.index'))">Reset Filter</v-btn>
                            </div>
                        </v-alert>

                        <v-data-table
                            :headers="headers"
                            :items="products"
                            :search="search"
                            hover
                            class="pa-2"
                        >
                            <template v-slot:item.stock="{ item }">
                                <v-chip
                                    :color="item.stock <= item.min_stock ? 'error' : 'success'"
                                    size="small"
                                    variant="flat"
                                    class="font-weight-bold"
                                >
                                    {{ item.stock }}
                                </v-chip>
                            </template>

                            <template v-slot:item.price="{ item }">
                                <span class="font-weight-medium">{{ formatCurrency(item.price) }}</span>
                            </template>

                            <template v-slot:item.actions="{ item }">
                                <div class="d-flex align-center justify-end ga-1">
                                    <v-btn icon="mdi-arrow-up-bold" variant="text" color="success" size="small" @click="openStockDialog(item, 'in')" title="Stok Masuk"></v-btn>
                                    <v-btn icon="mdi-arrow-down-bold" variant="text" color="orange" size="small" @click="openStockDialog(item, 'out')" title="Stok Keluar"></v-btn>
                                    
                                    <v-divider vertical inset class="mx-2"></v-divider>

                                    <template v-if="$page.props.auth.user.role === 'admin'">
                                        <v-btn icon="mdi-pencil" variant="text" color="blue" size="small" @click="editItem(item)"></v-btn>
                                        <v-btn icon="mdi-delete" variant="text" color="red" size="small" @click="deleteItem(item.id)"></v-btn>
                                    </template>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>

                    <v-dialog v-model="dialog" max-width="600px" persistent>
                        <v-card rounded="xl">
                            <v-card-title class="pa-4 bg-primary text-white d-flex align-center">
                                <v-icon :icon="isEditing ? 'mdi-pencil' : 'mdi-plus'" class="me-2"></v-icon>
                                <span class="text-h6">{{ isEditing ? "Edit Produk" : "Tambah Produk Baru" }}</span>
                            </v-card-title>
                            <v-card-text class="pt-6">
                                <v-row dense>
                                    <v-col cols="12" md="6">
                                        <v-text-field v-model="form.sku" label="SKU / Kode Barang" variant="outlined" density="comfortable" :error-messages="form.errors.sku"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6">
                                        <v-select v-model="form.category_id" label="Kategori" :items="categories" item-title="name" item-value="id" variant="outlined" density="comfortable" :error-messages="form.errors.category_id"></v-select>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="form.name" label="Nama Barang" variant="outlined" density="comfortable" :error-messages="form.errors.name"></v-text-field>
                                    </v-col>
                                    <v-col cols="4">
                                        <v-text-field v-model="form.stock" type="number" label="Stok Awal" variant="outlined" density="comfortable"></v-text-field>
                                    </v-col>
                                    <v-col cols="4">
                                        <v-text-field v-model="form.min_stock" type="number" label="Limit Stok" variant="outlined" density="comfortable"></v-text-field>
                                    </v-col>
                                    <v-col cols="4">
                                        <v-text-field v-model="form.price" type="number" label="Harga" variant="outlined" density="comfortable" prefix="Rp"></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions class="pa-4">
                                <v-spacer></v-spacer>
                                <v-btn variant="text" rounded="lg" @click="closeDialog" :disabled="form.processing">Batal</v-btn>
                                <v-btn color="primary" variant="flat" rounded="lg" class="px-6" @click="submit" :loading="form.processing">Simpan Produk</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>

                    <v-dialog v-model="stockDialog" max-width="450px" persistent>
                        <v-card rounded="xl">
                            <v-card-title :class="stockForm.type === 'in' ? 'bg-success' : 'bg-orange'" class="text-white pa-4">
                                <v-icon icon="mdi-swap-vertical" class="me-2"></v-icon>
                                <span>{{ stockForm.type === "in" ? "Barang Masuk" : "Barang Keluar" }}</span>
                            </v-card-title>
                            <v-card-text class="pt-6">
                                <div class="mb-4 pa-3 bg-grey-lighten-4 rounded-lg border">
                                    <div class="text-caption text-grey">Produk Seleksi:</div>
                                    <div class="text-subtitle-1 font-weight-bold text-primary">{{ stockForm.product_name }}</div>
                                </div>
                                <v-row dense>
                                    <v-col cols="12">
                                        <v-text-field v-model="stockForm.quantity" type="number" label="Jumlah" variant="outlined" min="1"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field v-model="stockForm.reference" label="Referensi (No. PO / Invoice)" variant="outlined"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-textarea v-model="stockForm.notes" label="Catatan Tambahan" variant="outlined" rows="2"></v-textarea>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions class="pa-4">
                                <v-spacer></v-spacer>
                                <v-btn variant="text" rounded="lg" @click="stockDialog = false">Batal</v-btn>
                                <v-btn :color="stockForm.type === 'in' ? 'success' : 'orange'" variant="flat" rounded="lg" class="px-6" @click="submitStock" :loading="stockForm.processing">Konfirmasi</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-container>
            </v-main>
        </v-app>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    products: Array,
    categories: Array,
    filters: Object
});

const dialog = ref(false);
const stockDialog = ref(false);
const isEditing = ref(false);
const search = ref("");

const headers = [
    { title: "SKU", key: "sku", align: "start", sortable: true },
    { title: "Nama Barang", key: "name", align: "start" },
    { title: "Kategori", key: "category.name", align: "start" },
    { title: "Stok", key: "stock", align: "center" },
    { title: "Harga", key: "price", align: "start" },
    { title: "Aksi", key: "actions", sortable: false, align: "end" },
];

const form = useForm({
    id: null,
    sku: "",
    name: "",
    category_id: null,
    stock: 0,
    min_stock: 5,
    price: 0,
});

const stockForm = useForm({
    product_id: null,
    product_name: "",
    type: "in",
    quantity: 1,
    reference: "",
    notes: "",
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(val);
};

const openDialog = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    dialog.value = true;
};

const closeDialog = () => {
    dialog.value = false;
    form.reset();
};

const editItem = (item) => {
    isEditing.value = true;
    Object.assign(form, item);
    dialog.value = true;
};

const submit = () => {
    const action = isEditing.value ? route("products.update", form.id) : route("products.store");
    const method = isEditing.value ? 'put' : 'post';
    
    form[method](action, {
        onSuccess: () => closeDialog(),
    });
};

const deleteItem = (id) => {
    if (confirm("Yakin mau hapus barang ini?")) {
        form.delete(route("products.destroy", id), { preserveScroll: true });
    }
};

const openStockDialog = (item, type) => {
    stockForm.reset();
    stockForm.product_id = item.id;
    stockForm.product_name = item.name;
    stockForm.type = type;
    stockDialog.value = true;
};

const submitStock = () => {
    stockForm.post(route("stock-movements.store"), {
        onSuccess: () => {
            stockDialog.value = false;
        },
    });
};

const exportToPdf = () => {
    window.open(route("products.pdf"), "_blank");
};
</script>