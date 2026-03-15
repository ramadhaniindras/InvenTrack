<template>
    <AuthenticatedLayout>
        <v-container fluid class="px-6 py-10">
            <v-card elevation="1" rounded="xl">
                <v-toolbar color="white" flat class="border-b px-4 py-2">
                    <v-icon
                        icon="mdi-package-variant-closed"
                        color="primary"
                        class="me-3"
                    ></v-icon>
                    <v-toolbar-title class="font-weight-bold"
                        >Inventory</v-toolbar-title
                    >

                    <v-chip
                        size="x-small"
                        variant="tonal"
                        color="primary"
                        class="me-2"
                    >
                        Total: <strong>{{ products.length }}</strong>
                    </v-chip>

                    <v-chip
                        v-if="products.filter((p) => p.stock <= p.min_stock).length > 0"
                        size="x-small"
                        variant="tonal"
                        color="error"
                        class="me-2"
                    >
                        Restock:
                        <strong>{{
                            products.filter((p) => p.stock <= p.min_stock).length
                        }}</strong>
                    </v-chip>

                    <v-spacer></v-spacer>

                    <v-text-field
                        v-model="search"
                        prepend-inner-icon="mdi-magnify"
                        placeholder="Cari barang..."
                        variant="outlined"
                        density="compact"
                        hide-details
                        style="max-width: 300px"
                        rounded="lg"
                        class="me-4"
                    ></v-text-field>

                    <div class="d-flex ga-2">
                        <v-btn
                            color="primary"
                            variant="tonal"
                            prepend-icon="mdi-history"
                            rounded="lg"
                            @click="$inertia.visit('/history')"
                        >
                            History
                        </v-btn>
                        <v-btn
                            color="primary"
                            variant="flat"
                            prepend-icon="mdi-plus"
                            rounded="lg"
                            @click="openDialog"
                        >
                            Tambah Barang
                        </v-btn>
                        <v-btn
                            color="error"
                            variant="outlined"
                            prepend-icon="mdi-file-pdf-box"
                            rounded="lg"
                            @click="exportToPdf"
                        >
                            PDF
                        </v-btn>
                    </div>
                </v-toolbar>

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
                        <span class="font-weight-medium">{{
                            formatCurrency(item.price)
                        }}</span>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex align-center justify-end ga-1">
                            <v-btn
                                icon="mdi-arrow-up-bold"
                                variant="text"
                                color="success"
                                size="small"
                                @click="openStockDialog(item, 'in')"
                                title="Stok Masuk"
                            ></v-btn>
                            <v-btn
                                icon="mdi-arrow-down-bold"
                                variant="text"
                                color="orange"
                                size="small"
                                @click="openStockDialog(item, 'out')"
                                title="Stok Keluar"
                            ></v-btn>

                            <v-divider vertical inset class="mx-2"></v-divider>

                            <template v-if="$page.props.auth.user.role === 'admin'">
                                <v-btn
                                    icon="mdi-swap-vertical"
                                    size="x-small"
                                    color="orange-darken-2"
                                    variant="text"
                                    @click="openAdjustDialog(item)"
                                    title="Sesuaikan Stok"
                                ></v-btn>
                                <v-btn
                                    icon="mdi-pencil"
                                    variant="text"
                                    color="blue"
                                    size="small"
                                    @click="editItem(item)"
                                    title="Edit"
                                ></v-btn>
                                <v-btn
                                    icon="mdi-delete"
                                    variant="text"
                                    color="red"
                                    size="small"
                                    @click="deleteItem(item.id)"
                                    title="Hapus"
                                ></v-btn>
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
                        <div class="mb-4 pa-3 bg-grey-lighten-4 rounded-lg border text-subtitle-1 font-weight-bold text-primary">
                            {{ stockForm.product_name }}
                        </div>
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
import { notify, confirmDelete } from "@/Utils/alert";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    products: Array,
    categories: Array,
    filters: Object,
});

const dialog = ref(false);
const stockDialog = ref(false);
const adjustDialog = ref(false);
const isEditing = ref(false);
const search = ref("");

const currentProductStock = computed(() => {
    if (!stockForm.product_id) return 0;
    const product = props.products.find((p) => p.id === stockForm.product_id);
    return product ? product.stock : 0;
});

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


const adjustForm = useForm({ 
    product_id: null, 
    actual_stock: 0, 
    reason: "" 
});

const formatCurrency = (val) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(val);


const openDialog = () => {
    isEditing.value = false;
    form.reset(); 
    form.clearErrors();
    dialog.value = true;
};

const closeDialog = () => {
    dialog.value = false;
    form.reset();
    form.clearErrors();
};

const editItem = (item) => {
    isEditing.value = true;
    form.clearErrors();
    form.id = item.id;
    form.sku = item.sku;
    form.name = item.name;
    form.category_id = item.category_id;
    form.stock = item.stock;
    form.min_stock = item.min_stock;
    form.price = item.price;
    dialog.value = true;
};

const submit = () => {
    const action = isEditing.value
        ? route("products.update", form.id)
        : route("products.store");
    const method = isEditing.value ? "put" : "post";

    form[method](action, {
        onSuccess: () => closeDialog(),
    });
};

const deleteItem = (id) => {
    confirmDelete(() => {
        router.delete(route("products.destroy", id), {
            preserveScroll: true,
        });
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
        notify("Stok tidak mencukupi, Bro!", "error");
        return;
    }
    stockForm.post(route("stock-movements.store"), {
        onSuccess: () => {
            stockDialog.value = false;
            stockForm.reset(); 
        },
    });
};


const openAdjustDialog = (product) => {
    adjustForm.reset();
    adjustForm.product_id = product.id;
    adjustForm.actual_stock = product.stock;
    adjustDialog.value = true;
};

const submitAdjust = () => {
    if (adjustForm.processing) return;
    adjustForm.post(route("stock.adjust"), {
        onSuccess: () => {
            adjustDialog.value = false;
            adjustForm.reset(); 
        },
    });
};

const exportToPdf = () => {
    window.open(route("products.pdf"), "_blank");
};
</script>