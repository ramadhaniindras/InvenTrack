<template>
    <AuthenticatedLayout>
        <v-container fluid class="px-6 py-10">
            <v-card elevation="1" rounded="xl">
                <v-toolbar color="white" flat class="border-b px-4 py-2">
                    <v-icon icon="mdi-package-variant-closed" color="primary" class="me-3"></v-icon>
                    <v-toolbar-title class="font-weight-bold">Inventory</v-toolbar-title>

                    <v-chip size="x-small" variant="tonal" color="primary" class="me-2">
                        Total: <strong>{{ products.length }}</strong>
                    </v-chip>

                    <v-chip v-if="products.filter((p) => p.stock <= p.min_stock).length > 0" size="x-small" variant="tonal" color="error" class="me-2">
                        Restock: <strong>{{ products.filter((p) => p.stock <= p.min_stock).length }}</strong>
                    </v-chip>

                    <v-spacer></v-spacer>

                    <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" placeholder="Cari barang..." variant="outlined" density="compact" hide-details style="max-width: 300px" rounded="lg" class="me-4"></v-text-field>

                    <div class="d-flex ga-2">
                        <v-btn color="primary" variant="tonal" prepend-icon="mdi-history" rounded="lg" @click="$inertia.visit('/history')">History</v-btn>
                        <v-btn color="primary" variant="flat" prepend-icon="mdi-plus" rounded="lg" @click="openDialog">Tambah Barang</v-btn>
                        <v-btn color="error" variant="outlined" prepend-icon="mdi-file-pdf-box" rounded="lg" @click="exportToPdf">PDF</v-btn>
                    </div>
                </v-toolbar>

                <v-data-table :headers="headers" :items="products" :search="search" hover class="pa-2">
                    <template v-slot:item.image="{ item }">
                        <v-avatar size="45" rounded="lg" class="border my-1" color="grey-lighten-4">
                            <v-img :src="item.image ? '/storage/' + item.image : '/images/no-image.png'" cover>
                                <template v-slot:placeholder>
                                    <div class="d-flex align-center justify-center fill-height">
                                        <v-icon color="grey-lighten-1">mdi-package-variant</v-icon>
                                    </div>
                                </template>
                            </v-img>
                        </v-avatar>
                    </template>

                    <template v-slot:item.stock="{ item }">
                        <div class="d-flex align-center ga-2">
                            <v-chip :color="item.stock <= item.min_stock ? 'error' : 'success'" size="small" variant="flat" class="font-weight-bold">
                                {{ item.stock }}
                            </v-chip>
                            <v-btn icon="mdi-qrcode" variant="text" color="purple-lighten-1" size="small" @click="printQrCode(item.id)" title="Cetak QR Code"></v-btn>
                        </div>
                    </template>

                    <template v-slot:item.price="{ item }">
                        <span class="font-weight-medium">{{ formatCurrency(item.price) }}</span>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex align-center justify-end ga-1">
                            <v-btn icon="mdi-arrow-up-bold-circle-outline" variant="text" color="success" size="small" @click="openStockDialog(item, 'in')" title="Stok Masuk"></v-btn>
                            <v-btn icon="mdi-arrow-down-bold-circle-outline" variant="text" color="orange" size="small" @click="openStockDialog(item, 'out')" title="Stok Keluar"></v-btn>
                            
                            <v-menu transition="scale-transition">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon="mdi-dots-vertical" variant="text" color="grey-darken-1" size="small" v-bind="props"></v-btn>
                                </template>
                                <v-list rounded="lg" density="comfortable" class="border shadow-sm">
                                    <template v-if="$page.props.auth.user.role === 'admin'">
                                        <v-list-item prepend-icon="mdi-swap-vertical" title="Sesuaikan Stok" @click="openAdjustDialog(item)"></v-list-item>
                                        <v-list-item prepend-icon="mdi-pencil-outline" title="Edit Barang" color="blue" @click="editItem(item)"></v-list-item>
                                        <v-divider></v-divider>
                                        <v-list-item prepend-icon="mdi-delete-outline" title="Hapus Barang" color="red" @click="deleteItem(item.id)"></v-list-item>
                                    </template>
                                    <v-list-item v-else prepend-icon="mdi-information-outline" title="Detail Info"></v-list-item>
                                </v-list>
                            </v-menu>
                        </div>
                    </template>
                </v-data-table>
            </v-card>

            <v-btn color="primary" icon="mdi-qrcode-scan" size="x-large" elevation="8" style="position: fixed; bottom: 30px; right: 30px; z-index: 100;" @click="openScanner"></v-btn>

            <v-dialog v-model="scannerDialog" max-width="500px" persistent>
                <v-card rounded="xl">
                    <v-card-title class="bg-primary text-white pa-4">
                        <v-icon icon="mdi-qrcode-scan" class="me-2"></v-icon>
                        Scan Barang InvenTrack
                    </v-card-title>
                    <v-card-text class="pt-6">
                        <div v-show="!scannedProduct" id="reader" style="width: 100%"></div>
                        <v-expand-transition>
                            <div v-if="scannedProduct" class="text-center pa-4">
                                <v-avatar size="100" rounded="lg" class="border mb-4">
                                    <v-img :src="scannedProduct.image ? '/storage/' + scannedProduct.image : '/images/no-image.png'" cover></v-img>
                                </v-avatar>
                                <h3 class="text-h6 font-weight-bold">{{ scannedProduct.name }}</h3>
                                <p class="text-subtitle-2 text-grey mb-6">SKU: {{ scannedProduct.sku }} | Stok: {{ scannedProduct.stock }}</p>
                                <div class="d-flex ga-3 justify-center">
                                    <v-btn color="success" prepend-icon="mdi-arrow-up-bold" rounded="lg" @click="actionFromScan('in')">Stok Masuk</v-btn>
                                    <v-btn color="orange" prepend-icon="mdi-arrow-down-bold" rounded="lg" @click="actionFromScan('out')">Stok Keluar</v-btn>
                                </div>
                            </div>
                        </v-expand-transition>
                    </v-card-text>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="closeScanner">Tutup Kamera</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialog" max-width="600px" persistent>
                <v-card rounded="xl">
                    <v-card-title class="pa-4 bg-primary text-white d-flex align-center">
                        <v-icon :icon="isEditing ? 'mdi-pencil' : 'mdi-plus'" class="me-2"></v-icon>
                        <span class="text-h6">{{ isEditing ? "Edit Produk" : "Tambah Produk Baru" }}</span>
                    </v-card-title>
                    <v-card-text class="pt-6">
                        <v-row dense>
                            <v-col cols="12" class="d-flex flex-column align-center mb-4">
                                <v-avatar size="120" rounded="xl" class="border mb-3" color="grey-lighten-4">
                                    <v-img :src="imagePreview || (isEditing && form.old_image ? '/storage/' + form.old_image : '/images/no-image.png')" cover></v-img>
                                </v-avatar>
                                <v-file-input v-model="imageFile" label="Pilih Foto Barang" variant="outlined" prepend-icon="mdi-camera" accept="image/*" density="comfortable" hide-details @change="handleImageChange" @click:clear="clearImage" style="width: 100%"></v-file-input>
                            </v-col>
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
                                <v-text-field v-model.number="form.stock" type="number" label="Stok" variant="outlined" density="comfortable"></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model.number="form.min_stock" type="number" label="Limit Stok" variant="outlined" density="comfortable"></v-text-field>
                            </v-col>
                            <v-col cols="4">
                                <v-text-field v-model.number="form.price" type="number" label="Harga" variant="outlined" density="comfortable" prefix="Rp"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="closeDialog" :disabled="form.processing">Batal</v-btn>
                        <v-btn color="primary" variant="flat" rounded="lg" @click="submit" :loading="form.processing">Simpan</v-btn>
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
                        <div class="mb-4 pa-3 bg-grey-lighten-4 rounded-lg border text-subtitle-1 font-weight-bold text-primary">{{ stockForm.product_name }}</div>
                        <v-text-field v-model.number="stockForm.quantity" type="number" label="Jumlah" variant="outlined" min="1"></v-text-field>
                        <v-text-field v-model="stockForm.reference" label="Referensi (No. Invoice)" variant="outlined"></v-text-field>
                        <v-textarea v-model="stockForm.notes" label="Catatan" variant="outlined" rows="2"></v-textarea>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="stockDialog = false" :disabled="stockForm.processing">Batal</v-btn>
                        <v-btn :color="stockForm.type === 'in' ? 'success' : 'orange'" variant="flat" @click="submitStock" :loading="stockForm.processing">Konfirmasi</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="adjustDialog" max-width="450px" persistent>
                <v-card rounded="xl">
                    <v-card-title class="bg-orange-darken-2 text-white pa-4">
                        <v-icon icon="mdi-wrench" class="me-2"></v-icon>
                        <span>Penyesuaian Stok Manual</span>
                    </v-card-title>
                    <v-card-text class="pt-6">
                        <v-alert type="warning" variant="tonal" density="compact" class="mb-4 text-caption">Gunakan ini jika stok fisik berbeda dengan aplikasi.</v-alert>
                        <v-text-field v-model.number="adjustForm.actual_stock" type="number" label="Stok Sebenarnya (Fisik)" variant="outlined"></v-text-field>
                        <v-textarea v-model="adjustForm.reason" label="Alasan" variant="outlined" rows="2"></v-textarea>
                    </v-card-text>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="adjustDialog = false" :disabled="adjustForm.processing">Batal</v-btn>
                        <v-btn color="orange-darken-2" variant="flat" @click="submitAdjust" :loading="adjustForm.processing">Simpan Perubahan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import { Html5QrcodeScanner } from "html5-qrcode";
import { notify, confirmDelete } from "@/Utils/alert";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, computed, onBeforeUnmount, nextTick } from "vue";

const props = defineProps({
    products: Array,
    categories: Array,
    filters: Object,
});

const scannerDialog = ref(false);
const scannedProduct = ref(null);
let html5QrcodeScanner = null;

const dialog = ref(false);
const stockDialog = ref(false);
const adjustDialog = ref(false);
const isEditing = ref(false);
const search = ref("");
const imagePreview = ref(null);
const imageFile = ref(null);

const headers = [
    { title: "Foto", key: "image", sortable: false, align: "start" },
    { title: "SKU", key: "sku", align: "start", sortable: true },
    { title: "Nama Barang", key: "name", align: "start" },
    { title: "Kategori", key: "category.name", align: "start" },
    { title: "Stok", key: "stock", align: "center" },
    { title: "Harga", key: "price", align: "start" },
    { title: "Aksi", key: "actions", sortable: false, align: "end" },
];

const currentProductStock = computed(() => {
    if (!stockForm.product_id) return 0;
    const product = props.products.find((p) => p.id === stockForm.product_id);
    return product ? product.stock : 0;
});

const form = useForm({
    id: null, sku: "", name: "", category_id: null, stock: 0, min_stock: 5, price: 0, image: null, old_image: null,
});

const stockForm = useForm({
    product_id: null, product_name: "", type: "in", quantity: 1, reference: "", notes: "",
});

const adjustForm = useForm({ product_id: null, actual_stock: 0, reason: "" });

const formatCurrency = (val) => new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(val);

const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => imagePreview.value = e.target.result;
        reader.readAsDataURL(file);
    }
};

const clearImage = () => {
    form.image = null;
    imagePreview.value = null;
    imageFile.value = null;
};

const openDialog = () => {
    isEditing.value = false;
    clearImage();
    form.reset();
    form.clearErrors();
    dialog.value = true;
};

const closeDialog = () => {
    dialog.value = false;
    clearImage();
    form.reset();
};

const editItem = (item) => {
    isEditing.value = true;
    clearImage();
    form.id = item.id;
    form.sku = item.sku;
    form.name = item.name;
    form.category_id = item.category_id;
    form.stock = item.stock;
    form.min_stock = item.min_stock;
    form.price = item.price;
    form.old_image = item.image;
    dialog.value = true;
};

const submit = () => {
    const action = isEditing.value ? route("products.update", form.id) : route("products.store");
    if (isEditing.value) {
        form.transform((data) => ({ ...data, _method: "put" })).post(action, { onSuccess: () => closeDialog() });
    } else {
        form.post(action, { onSuccess: () => closeDialog() });
    }
};

const deleteItem = (id) => {
    confirmDelete(() => {
        router.delete(route("products.destroy", id), { preserveScroll: true });
    });
};

const openStockDialog = (item, type) => {
    stockForm.reset();
    stockForm.product_id = item.id;
    stockForm.product_name = item.name;
    stockForm.type = type;
    stockForm.quantity = 1;
    stockDialog.value = true;
};

const submitStock = () => {
    if (stockForm.type === "out" && stockForm.quantity > currentProductStock.value) {
        notify("Stok tidak mencukupi!", "error");
        return;
    }
    stockForm.post(route("stock-movements.store"), { onSuccess: () => stockDialog.value = false });
};

const openAdjustDialog = (product) => {
    adjustForm.reset();
    adjustForm.product_id = product.id;
    adjustForm.actual_stock = product.stock;
    adjustDialog.value = true;
};

const submitAdjust = () => {
    adjustForm.post(route("stock.adjust"), { onSuccess: () => adjustDialog.value = false });
};

const printQrCode = (id) => window.open(route("products.qr", id), "_blank");

const openScanner = async () => {
    scannerDialog.value = true;
    scannedProduct.value = null;
    await nextTick();
    setTimeout(() => startScanning(), 400);
};

const startScanning = () => {
    if (html5QrcodeScanner) html5QrcodeScanner.clear();
    
    html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
        fps: 10, 
        qrbox: { width: 250, height: 250 },
        rememberLastUsedCamera: true
    });

    html5QrcodeScanner.render((decodedText) => {
        const product = props.products.find((p) => p.sku === decodedText);
        if (product) {
            html5QrcodeScanner.clear();
            scannedProduct.value = product;
            notify("Barang ditemukan: " + product.name, "success");
        } else {
            notify("SKU " + decodedText + " tidak terdaftar!", "error");
        }
    });
};

const actionFromScan = (type) => {
    const product = scannedProduct.value;
    scannerDialog.value = false;
    scannedProduct.value = null;
    openStockDialog(product, type);
};

const closeScanner = () => {
    if (html5QrcodeScanner) html5QrcodeScanner.clear().catch(e => console.error(e));
    scannerDialog.value = false;
    scannedProduct.value = null;
};

const exportToPdf = () => window.open(route("products.pdf"), "_blank");

onBeforeUnmount(() => {
    if (html5QrcodeScanner) html5QrcodeScanner.clear();
});
</script>

<style scoped>
.v-data-table :deep(thead th) {
    font-weight: 700 !important;
    text-transform: uppercase;
    font-size: 0.75rem;
    color: #616161;
}
.shadow-sm {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
}
</style>