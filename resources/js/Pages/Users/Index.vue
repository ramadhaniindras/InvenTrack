<template>
    <AuthenticatedLayout>
        <v-container fluid class="px-6 py-10">
            <v-card elevation="1" rounded="xl">
                <v-toolbar color="white" flat class="border-b px-4">
                    <v-icon
                        icon="mdi-account-group"
                        color="primary"
                        class="me-3"
                    ></v-icon>
                    <v-toolbar-title class="font-weight-bold"
                        >Manajemen User</v-toolbar-title
                    >
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
                                    :color="
                                        user.role === 'admin'
                                            ? 'purple'
                                            : 'blue'
                                    "
                                    size="small"
                                    variant="flat"
                                    class="text-uppercase font-weight-bold"
                                >
                                    {{ user.role }}
                                </v-chip>
                            </td>
                            <td class="text-right">
                                <v-btn
                                    v-if="user.id !== $page.props.auth.user.id"
                                    color="orange-darken-2"
                                    variant="tonal"
                                    size="small"
                                    prepend-icon="mdi-swap-horizontal"
                                    class="me-2"
                                    @click="changeRole(user)"
                                >
                                    Ganti Role
                                </v-btn>

                                <v-btn
                                    v-if="user.id !== $page.props.auth.user.id"
                                    icon="mdi-delete"
                                    variant="text"
                                    color="red"
                                    size="small"
                                    @click="deleteUser(user)"
                                ></v-btn>
                            </td>
                        </tr>
                    </tbody>
                </v-table>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    users: Array,
});

const changeRole = (user) => {
    const newRole = user.role === "admin" ? "staff" : "admin";
    if (confirm(`Yakin mau ubah ${user.name} jadi ${newRole}?`)) {
        router.patch(route("users.update", user.id), {
            role: newRole,
        });
    }
};

const deleteUser = (user) => {
    if (confirm(`Yakin mau hapus user ${user.name}?`)) {
        router.delete(route("users.destroy", user.id));
    }
};
</script>
