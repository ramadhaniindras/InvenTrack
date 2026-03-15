<template>
    <AuthenticatedLayout>
        <v-container fluid class="px-6 py-10">
            <v-card elevation="1" rounded="xl">
                <v-toolbar color="white" flat class="border-b px-4">
                    <v-icon icon="mdi-tag-multiple" color="primary" class="me-3"></v-icon>
                    <v-toolbar-title class="font-weight-bold">Category Management</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn 
                        color="primary" 
                        variant="flat" 
                        prepend-icon="mdi-plus" 
                        rounded="lg"
                        @click="openDialog"
                    >
                        Tambah Kategori
                    </v-btn>
                </v-toolbar>

                <v-data-table 
                    :headers="headers" 
                    :items="categories" 
                    hover 
                    class="pa-2"
                >
                    <template v-slot:item.name="{ item }">
                        <v-btn 
                            variant="text" 
                            color="primary" 
                            class="text-none font-weight-bold px-0"
                            @click="router.visit(route('products.index', { category_id: item.id }))"
                        >
                            {{ item.name }}
                            <v-icon icon="mdi-open-in-new" size="x-small" class="ms-1"></v-icon>
                        </v-btn>
                    </template>

                    <template v-slot:item.products_count="{ item }">
                        <v-chip size="small" variant="tonal" color="grey-darken-1" class="font-weight-bold">
                            {{ item.products_count }} Produk
                        </v-chip>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex justify-end ga-1">
                            <v-btn 
                                icon="mdi-pencil" 
                                variant="text" 
                                color="blue" 
                                size="small"
                                @click="editItem(item)"
                            ></v-btn>
                            <v-btn 
                                icon="mdi-delete" 
                                variant="text" 
                                color="red" 
                                size="small"
                                @click="handleDelete(item.id)"
                            ></v-btn>
                        </div>
                    </template>

                    <template v-slot:no-data>
                        <div class="pa-10 text-center">
                            <v-icon size="64" color="grey-lighten-1">mdi-tag-off</v-icon>
                            <p class="text-grey mt-2 font-weight-medium">Belum ada kategori barang.</p>
                        </div>
                    </template>
                </v-data-table>
            </v-card>

            <v-dialog v-model="dialog" max-width="400px" persistent>
                <v-card rounded="xl">
                    <v-card-title class="pa-4 bg-primary text-white d-flex align-center">
                        <v-icon :icon="isEditing ? 'mdi-pencil' : 'mdi-plus'" class="me-2"></v-icon>
                        <span class="text-h6 font-weight-bold">
                            {{ isEditing ? "Edit Kategori" : "Kategori Baru" }}
                        </span>
                    </v-card-title>
                    <v-card-text class="pt-6">
                        <v-text-field 
                            v-model="form.name" 
                            label="Nama Kategori" 
                            variant="outlined"
                            density="comfortable" 
                            placeholder="Contoh: Oli, Ban, Sparepart"
                            :error-messages="form.errors.name" 
                            @keyup.enter="submit"
                        ></v-text-field>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="closeDialog" :disabled="form.processing">Batal</v-btn>
                        <v-btn 
                            color="primary" 
                            variant="flat" 
                            rounded="lg"
                            @click="submit"
                            :loading="form.processing"
                        >
                            Simpan
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { notify, confirmDelete } from "@/Utils/alert"; 

const props = defineProps({ categories: Array });

const dialog = ref(false);
const isEditing = ref(false);

const headers = [
    { title: "Nama Kategori (Klik untuk Lihat Produk)", key: "name", align: "start" },
    { title: "Total Barang", key: "products_count", align: "center" },
    { title: "Aksi", key: "actions", sortable: false, align: "end" },
];

const form = useForm({
    id: null,
    name: "",
});

const openDialog = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    dialog.value = true;
};

const editItem = (item) => {
    isEditing.value = true;
    form.id = item.id;
    form.name = item.name;
    dialog.value = true;
};

const closeDialog = () => {
    dialog.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    const action = isEditing.value 
        ? route("categories.update", form.id) 
        : route("categories.store");
    
    const method = isEditing.value ? "put" : "post";

    form[method](action, {
        onSuccess: () => {
            closeDialog();
        },
    });
};


const handleDelete = (id) => {
    confirmDelete(() => {
        router.delete(route("categories.destroy", id), {
            preserveScroll: true,

        });
    });
};
</script>