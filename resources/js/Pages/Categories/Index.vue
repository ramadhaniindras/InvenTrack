<template>
    <AuthenticatedLayout>
        <v-app>
            <v-main class="bg-grey-lighten-4">
                <v-container py-10>
                    <v-card elevation="1" rounded="lg">
                        <v-toolbar color="white" flat class="border-b">
                            <v-icon icon="mdi-tag-multiple" class="ms-4 me-2" color="primary"></v-icon>
                            <v-toolbar-title class="font-weight-bold">Manajemen Kategori</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-btn color="primary" variant="flat" prepend-icon="mdi-plus" class="text-none me-4"
                                @click="openDialog">
                                Tambah Kategori
                            </v-btn>
                        </v-toolbar>

                        <v-divider></v-divider>

                        <v-data-table :headers="headers" :items="categories" hover class="pa-2">
                            <template v-slot:item.name="{ item }">
                                <v-btn variant="text" color="primary" class="text-none font-weight-bold px-0"
                                    @click="$inertia.visit(route('products.index', { category_id: item.id }))">
                                    {{ item.name }}
                                    <v-icon icon="mdi-open-in-new" size="x-small" class="ms-1"></v-icon>
                                </v-btn>
                            </template>

                            <template v-slot:item.products_count="{ item }">
                                <v-chip size="small" variant="tonal" color="grey-darken-1">
                                    {{ item.products_count }} Produk
                                </v-chip>
                            </template>

                            <template v-slot:item.actions="{ item }">
                                <v-btn icon="mdi-pencil" variant="text" color="blue" size="small"
                                    @click="editItem(item)"></v-btn>
                                <v-btn icon="mdi-delete" variant="text" color="red" size="small"
                                    @click="deleteItem(item.id)"></v-btn>
                            </template>
                        </v-data-table>
                    </v-card>

                    <v-dialog v-model="dialog" max-width="400px" persistent>
                        <v-card rounded="lg">
                            <v-card-title class="pa-4 bg-primary text-white">
                                <span class="text-h6">{{ isEditing ? "Edit Kategori" : "Kategori Baru" }}</span>
                            </v-card-title>
                            <v-card-text class="pt-6">
                                <v-text-field v-model="form.name" label="Nama Kategori" variant="outlined"
                                    density="comfortable" placeholder="Contoh: Elektronik, Suku Cadang"
                                    :error-messages="form.errors.name" @keyup.enter="submit"></v-text-field>
                            </v-card-text>
                            <v-divider></v-divider>
                            <v-card-actions class="pa-4">
                                <v-spacer></v-spacer>
                                <v-btn variant="text" @click="closeDialog" :disabled="form.processing">Batal</v-btn>
                                <v-btn color="primary" variant="flat" @click="submit"
                                    :loading="form.processing">Simpan</v-btn>
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

const props = defineProps({ categories: Array });

const dialog = ref(false);
const isEditing = ref(false);

const headers = [
    { title: "Nama Kategori (Klik untuk Filter)", key: "name", align: "start" },
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
};

const submit = () => {
    if (isEditing.value) {
        form.put(route("categories.update", form.id), {
            onSuccess: () => closeDialog(),
        });
    } else {
        form.post(route("categories.store"), {
            onSuccess: () => closeDialog(),
        });
    }
};

const deleteItem = (id) => {
    if (confirm("Hapus kategori ini? Pastikan tidak ada produk di dalamnya.")) {
        form.delete(route("categories.destroy", id));
    }
};
</script>