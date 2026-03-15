<template>
    <AuthenticatedLayout>
        <v-container fluid class="px-6 py-10">
            
            <v-card elevation="1" rounded="xl">
                <v-toolbar color="white" flat class="border-b px-4 py-2">
                    <v-icon
                        icon="mdi-account-group"
                        color="primary"
                        class="me-3"
                    ></v-icon>
                    <v-toolbar-title class="font-weight-bold"
                        >Manajemen User</v-toolbar-title
                    >
                    <v-spacer></v-spacer>
                    
                    <v-btn
                        color="primary"
                        variant="flat"
                        prepend-icon="mdi-plus"
                        rounded="lg"
                        @click="openAddDialog"
                    >
                        Tambah User
                    </v-btn>
                </v-toolbar>

                <v-table hover class="pa-4">
                    <thead>
                        <tr>
                            <th class="font-weight-bold">Nama</th>
                            <th class="font-weight-bold">Email</th>
                            <th class="font-weight-bold text-center">
                                Role saat ini
                            </th>
                            <th class="font-weight-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id">
                            <td>{{ user.name }}</td>
                            <td>{{ user.email }}</td>
                            <td class="text-center">
                                <v-chip
                                    :color="user.role === 'admin' ? 'purple' : 'blue'"
                                    size="small"
                                    variant="flat"
                                    class="text-uppercase font-weight-bold"
                                >
                                    {{ user.role }}
                                </v-chip>
                            </td>
                            <td class="text-right">
                                <template v-if="user.id !== $page.props.auth.user.id">
                                    <v-btn
                                        color="orange-darken-2"
                                        variant="tonal"
                                        size="small"
                                        prepend-icon="mdi-swap-horizontal"
                                        class="me-2"
                                        rounded="lg"
                                        @click="handleChangeRole(user)"
                                    >
                                        Ganti Role
                                    </v-btn>

                                    <v-btn
                                        icon="mdi-delete"
                                        variant="text"
                                        color="red"
                                        size="small"
                                        @click="handleDeleteUser(user)"
                                    ></v-btn>
                                </template>
                                <v-chip v-else size="x-small" variant="outlined" color="grey">Akun Anda</v-chip>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card>

            <v-dialog v-model="addDialog" max-width="500px" persistent>
                <v-card rounded="xl">
                    <v-card-title class="pa-4 bg-primary text-white">
                        <v-icon icon="mdi-account-plus" class="me-2"></v-icon>
                        Tambah User Baru
                    </v-card-title>
                    <v-card-text class="pt-6">
                        <v-text-field
                            v-model="form.name"
                            label="Nama"
                            variant="outlined"
                            :error-messages="form.errors.name"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.email"
                            label="Email"
                            variant="outlined"
                            :error-messages="form.errors.email"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.password"
                            label="Password"
                            type="password"
                            variant="outlined"
                            :error-messages="form.errors.password"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.password_confirmation"
                            label="Konfirmasi Password"
                            type="password"
                            variant="outlined"
                        ></v-text-field>
                        <v-select
                            v-model="form.role"
                            label="Role"
                            :items="['admin', 'staff']"
                            variant="outlined"
                        ></v-select>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="addDialog = false">Batal</v-btn>
                        <v-btn 
                            color="primary" 
                            variant="flat" 
                            @click="submitUser" 
                            :loading="form.processing"
                            rounded="lg"
                        >Simpan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { notify, confirmDelete } from "@/Utils/alert"; 
import Swal from 'sweetalert2';

const props = defineProps({
    users: Array,
});

const addDialog = ref(false);

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "staff",
});

const openAddDialog = () => {
    form.reset();
    form.clearErrors();
    addDialog.value = true;
};

const submitUser = () => {
    form.post(route("users.store"), {
        onSuccess: () => {
            addDialog.value = false;
            notify("User baru berhasil ditambahkan! 🚀");
        },
    });
};

const handleChangeRole = (user) => {
    const newRole = user.role === "admin" ? "staff" : "admin";
    
    Swal.fire({
        title: 'Ganti Role User?',
        text: `Ubah role ${user.name} menjadi ${newRole.toUpperCase()}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#fb8c00',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(route("users.update", user.id), {
                role: newRole,
            }, {
                onSuccess: () => notify(`Role ${user.name} sekarang: ${newRole} ✅`),
            });
        }
    });
};

const handleDeleteUser = (user) => {
    confirmDelete(() => {
        router.delete(route("users.destroy", user.id), {
            onSuccess: () => notify("User berhasil dihapus! 🗑️"),
        });
    });
};
</script>