<template>
    <Head title="Manajemen User" />
    <AuthenticatedLayout>
        <v-container fluid class="pa-4 pa-md-8 bg-background">
            <v-card
                elevation="0"
                rounded="xl"
                class="border-sm shadow-sm bg-surface"
            >
                <v-toolbar flat class="border-b px-4 py-2 bg-surface">
                    <v-icon
                        icon="mdi-account-group-outline"
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
                        prepend-icon="mdi-account-plus"
                        rounded="lg"
                        @click="openAddDialog"
                    >
                        Tambah User
                    </v-btn>
                </v-toolbar>

                <v-table hover class="bg-surface">
                    <thead>
                        <tr>
                            <th class="text-overline font-weight-black">
                                User
                            </th>
                            <th class="text-overline font-weight-black">
                                Email
                            </th>
                            <th
                                class="text-overline font-weight-black text-center"
                            >
                                Role
                            </th>
                            <th
                                class="text-overline font-weight-black text-right"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in users"
                            :key="user.id"
                            class="list-item-hover"
                        >
                            <td class="py-3">
                                <div class="d-flex align-center">
                                    <v-avatar
                                        size="32"
                                        color="primary"
                                        variant="tonal"
                                        class="me-3 font-weight-bold text-caption"
                                    >
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </v-avatar>
                                    <span class="font-weight-bold">{{
                                        user.name
                                    }}</span>
                                </div>
                            </td>
                            <td class="text-medium-emphasis text-body-2">
                                {{ user.email }}
                            </td>
                            <td class="text-center">
                                <v-chip
                                    :color="
                                        user.role === 'admin'
                                            ? 'purple'
                                            : 'blue'
                                    "
                                    size="x-small"
                                    variant="flat"
                                    class="text-uppercase font-weight-black px-3"
                                >
                                    {{ user.role }}
                                </v-chip>
                            </td>
                            <td class="text-right">
                                <template
                                    v-if="user.id !== $page.props.auth.user.id"
                                >
                                    <v-btn
                                        color="orange-darken-2"
                                        variant="tonal"
                                        size="small"
                                        prepend-icon="mdi-swap-horizontal"
                                        class="me-2 text-none"
                                        rounded="lg"
                                        @click="handleChangeRole(user)"
                                    >
                                        Ganti Role
                                    </v-btn>

                                    <v-btn
                                        icon="mdi-delete-outline"
                                        variant="text"
                                        color="error"
                                        size="small"
                                        @click="handleDeleteUser(user)"
                                    ></v-btn>
                                </template>
                                <v-chip
                                    v-else
                                    size="x-small"
                                    variant="tonal"
                                    color="success"
                                    class="font-weight-bold"
                                >
                                    <v-icon start size="12"
                                        >mdi-check-decagram</v-icon
                                    >
                                    Akun Anda
                                </v-chip>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card>

            <v-dialog v-model="addDialog" max-width="500px" persistent>
                <v-card rounded="xl" class="bg-surface">
                    <v-card-title class="pa-4 bg-primary text-white">
                        <v-icon icon="mdi-account-plus" class="me-2"></v-icon>
                        Tambah User Baru
                    </v-card-title>
                    <v-card-text class="pt-6">
                        <v-text-field
                            v-model="form.name"
                            label="Nama Lengkap"
                            variant="outlined"
                            density="comfortable"
                            :error-messages="form.errors.name"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.email"
                            label="Email"
                            variant="outlined"
                            density="comfortable"
                            :error-messages="form.errors.email"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.password"
                            label="Password"
                            type="password"
                            variant="outlined"
                            density="comfortable"
                            :error-messages="form.errors.password"
                        ></v-text-field>
                        <v-text-field
                            v-model="form.password_confirmation"
                            label="Konfirmasi Password"
                            type="password"
                            variant="outlined"
                            density="comfortable"
                        ></v-text-field>
                        <v-select
                            v-model="form.role"
                            label="Role Akses"
                            :items="['admin', 'staff']"
                            variant="outlined"
                            density="comfortable"
                        ></v-select>
                    </v-card-text>
                    <v-divider></v-divider>
                    <v-card-actions class="pa-4">
                        <v-spacer></v-spacer>
                        <v-btn variant="text" @click="addDialog = false"
                            >Batal</v-btn
                        >
                        <v-btn
                            color="primary"
                            variant="flat"
                            @click="submitUser"
                            :loading="form.processing"
                            rounded="lg"
                            class="px-6"
                            >Simpan</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, router,Head } from "@inertiajs/vue3";
import { ref } from "vue";
import { notify, confirmDelete } from "@/Utils/alert";
import Swal from "sweetalert2";
import { useTheme } from "vuetify";


const props = defineProps({ users: Array });
const theme = useTheme();
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
    const isDark = theme.global.current.value.dark;
    const newRole = user.role === "admin" ? "staff" : "admin";

    Swal.fire({
        title: "Ganti Role User?",
        text: `Ubah role ${user.name} menjadi ${newRole.toUpperCase()}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#fb8c00",
        confirmButtonText: "Ya, Ubah!",
        cancelButtonText: "Batal",
        background: isDark ? "#1E1E1E" : "#FFFFFF",
        color: isDark ? "#FFFFFF" : "#000000",
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(
                route("users.update", user.id),
                {
                    role: newRole,
                },
                {
                    onSuccess: () =>
                        notify(`Role ${user.name} sekarang: ${newRole} ✅`),
                },
            );
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

<style scoped>
.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}
.shadow-sm {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}
.text-overline {
    font-size: 0.7rem !important;
    letter-spacing: 1px !important;
    color: rgba(var(--v-theme-on-surface), 0.6);
}
.list-item-hover:hover {
    background-color: rgba(var(--v-theme-primary), 0.02);
}
</style>
