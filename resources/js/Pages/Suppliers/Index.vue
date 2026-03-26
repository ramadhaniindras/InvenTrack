<template>
    <AuthenticatedLayout>
        <v-container fluid class="px-6 py-10">
            <v-card elevation="1" rounded="xl">
                <v-toolbar color="white" flat class="border-b px-4 py-2">
                    <v-icon
                        icon="mdi-truck-delivery-outline"
                        color="primary"
                        class="me-3"
                    ></v-icon>
                    <v-toolbar-title class="font-weight-bold"
                        >Master Supplier</v-toolbar-title
                    >

                    <v-spacer></v-spacer>

                    <v-text-field
                        v-model="search"
                        prepend-inner-icon="mdi-magnify"
                        placeholder="Cari supplier..."
                        variant="outlined"
                        density="compact"
                        hide-details
                        style="max-width: 300px"
                        rounded="lg"
                        class="me-4"
                    ></v-text-field>

                    <v-btn
                        color="primary"
                        variant="flat"
                        prepend-icon="mdi-plus"
                        rounded="lg"
                        @click="openDialog"
                        >Tambah Supplier</v-btn
                    >
                </v-toolbar>

                <v-data-table
                    :headers="headers"
                    :items="suppliers"
                    :search="search"
                    hover
                    class="pa-2"
                >
                    <template v-slot:item.phone="{ item }">
                        <v-btn
                            variant="text"
                            color="success"
                            size="small"
                            prepend-icon="mdi-whatsapp"
                            :href="'https://wa.me/' + item.phone"
                            target="_blank"
                        >
                            {{ item.phone }}
                        </v-btn>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex ga-2 justify-end">
                            <v-btn
                                icon="mdi-pencil-outline"
                                variant="text"
                                color="blue"
                                size="small"
                                @click="editItem(item)"
                            ></v-btn>
                            <v-btn
                                icon="mdi-delete-outline"
                                variant="text"
                                color="red"
                                size="small"
                                @click="deleteItem(item.id)"
                            ></v-btn>
                            <v-btn
                                icon="mdi-file-document-edit-outline"
                                variant="text"
                                color="orange-darken-2"
                                size="small"
                                title="Buat Purchase Order"
                                @click="generatePO(item.id)"
                            ></v-btn>
                        </div>
                    </template>
                </v-data-table>
            </v-card>
        </v-container>

        <v-dialog v-model="dialog" max-width="500px" persistent>
            <v-card rounded="xl">
                <v-card-title class="pa-4 bg-primary text-white">
                    <v-icon
                        :icon="isEditing ? 'mdi-pencil' : 'mdi-plus'"
                        class="me-2"
                    ></v-icon>
                    {{ isEditing ? "Edit Supplier" : "Tambah Supplier Baru" }}
                </v-card-title>
                <v-card-text class="pt-6">
                    <v-text-field
                        v-model="form.name"
                        label="Nama Toko / Perusahaan"
                        variant="outlined"
                        :error-messages="form.errors.name"
                    ></v-text-field>
                    <v-text-field
                        v-model="form.contact_person"
                        label="Nama Sales / CP"
                        variant="outlined"
                    ></v-text-field>
                    <v-text-field
                        v-model="form.phone"
                        label="Nomor WhatsApp"
                        variant="outlined"
                        placeholder="62812xxx"
                        :error-messages="form.errors.phone"
                    ></v-text-field>
                    <v-textarea
                        v-model="form.address"
                        label="Alamat"
                        variant="outlined"
                        rows="2"
                    ></v-textarea>
                </v-card-text>
                <v-card-actions class="pa-4">
                    <v-spacer></v-spacer>
                    <v-btn variant="text" @click="closeDialog">Batal</v-btn>
                    <v-btn
                        color="primary"
                        variant="flat"
                        rounded="lg"
                        @click="submit"
                        :loading="form.processing"
                        >Simpan</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { notify, confirmDelete } from "@/Utils/alert";

const props = defineProps({ suppliers: Array });

const search = ref("");
const dialog = ref(false);
const isEditing = ref(false);

const headers = [
    { title: "Nama Supplier", key: "name", align: "start" },
    { title: "Sales / CP", key: "contact_person" },
    { title: "WhatsApp", key: "phone" },
    { title: "Alamat", key: "address" },
    { title: "Aksi", key: "actions", sortable: false, align: "end" },
];

const form = useForm({
    id: null,
    name: "",
    contact_person: "",
    phone: "",
    address: "",
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
    form.contact_person = item.contact_person;
    form.phone = item.phone;
    form.address = item.address;
    dialog.value = true;
};

const closeDialog = () => {
    dialog.value = false;
    form.reset();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route("suppliers.update", form.id), {
            onSuccess: () => closeDialog(),
        });
    } else {
        form.post(route("suppliers.store"), { onSuccess: () => closeDialog() });
    }
};

const deleteItem = (id) => {
    confirmDelete(() => {
        router.delete(route("suppliers.destroy", id));
    });
};
const generatePO = (id) => {
    window.location.href = route("po.generate", id);
};
</script>
