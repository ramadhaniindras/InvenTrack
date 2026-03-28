<template>
    <Head title="Suppliers" />
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-background">
            <v-card
                elevation="0"
                rounded="xl"
                class="border-sm shadow-sm bg-surface"
            >
                <v-toolbar flat class="border-b px-4 py-2 bg-surface">
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
                    class="pa-2 bg-surface"
                >
                    <template v-slot:item.phone="{ item }">
                        <div class="d-flex align-center">
                            <v-icon color="success" size="small" class="me-2"
                                >mdi-whatsapp</v-icon
                            >
                            <span
                                class="text-body-2 font-weight-medium text-medium-emphasis"
                            >
                                {{ item.phone || "-" }}
                            </span>
                        </div>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex ga-2 justify-end">
                            <v-btn
                                icon="mdi-whatsapp"
                                variant="tonal"
                                color="success"
                                size="small"
                                class="rounded-lg"
                                title="Download PO & Chat WA"
                                @click="generateAndChat(item)"
                            ></v-btn>

                            <v-btn
                                icon="mdi-pencil-outline"
                                variant="text"
                                color="blue"
                                size="small"
                                title="Edit Data"
                                @click="editItem(item)"
                            ></v-btn>

                            <v-btn
                                icon="mdi-delete-outline"
                                variant="text"
                                color="red"
                                size="small"
                                title="Hapus Supplier"
                                @click="deleteItem(item.id)"
                            ></v-btn>
                        </div>
                    </template>
                </v-data-table>
            </v-card>
        </v-container>

        <v-dialog v-model="dialog" max-width="500px" persistent>
            <v-card rounded="xl" class="bg-surface">
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
                        placeholder="62812xxx atau 0812xxx"
                        hint="Format: 628xxx (Otomatis ke WA)"
                        persistent-hint
                        class="mb-4"
                        :error-messages="form.errors.phone"
                    ></v-text-field>
                    <v-textarea
                        v-model="form.address"
                        label="Alamat"
                        variant="outlined"
                        rows="2"
                    ></v-textarea>
                </v-card-text>
                <v-divider></v-divider>
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
import { useForm, router,Head } from "@inertiajs/vue3";
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

const generateAndChat = (supplier) => {
    if (!supplier.phone) {
        alert("Nomor WA supplier ini kosong.");
        return;
    }

    const downloadUrl = route("po.generate", { supplier: supplier.id });
    const link = document.createElement("a");
    link.href = downloadUrl;
    link.setAttribute("download", "");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    let phone = supplier.phone.replace(/\D/g, "");
    if (phone.startsWith("0")) {
        phone = "62" + phone.slice(1);
    }

    const message =
        `Halo ${supplier.name}, saya mau order barang.%0A%0A` +
        `File Purchase Order (PO) saya lampirkan di sini.%0A` +
        `Mohon diproses ya, Terima kasih!`;

    const waUrl = `https://wa.me/${phone}?text=${message}`;

    window.open(waUrl, "_blank");
};
</script>

<style scoped>
/* Header Tabel agar adaptif warnanya */
.v-data-table :deep(thead th) {
    font-weight: 700 !important;
    text-transform: uppercase;
    font-size: 0.75rem;
    color: rgba(var(--v-theme-on-surface), 0.6) !important;
}

.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}

.shadow-sm {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}
</style>
