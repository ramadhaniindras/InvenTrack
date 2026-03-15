<template>
    <v-app>
        <v-navigation-drawer v-model="drawer" elevation="0" border>
            <v-list-item
                prepend-icon="mdi-warehouse"
                title="InvenTrack"
                subtitle="Manage with ease"
                class="pa-4 text-primary font-weight-bold"
            ></v-list-item>

            <v-divider></v-divider>

            <v-list density="comfortable" nav class="mt-2">
                <v-list-item
                    prepend-icon="mdi-view-dashboard-outline"
                    title="Dashboard"
                    @click="$inertia.visit(route('dashboard'))"
                    :active="route().current('dashboard')"
                    rounded="lg"
                ></v-list-item>

                <v-list-item
                    rounded="lg"
                    prepend-icon="mdi-package-variant"
                    title="Data Produk"
                    @click="$inertia.visit(route('products.index'))"
                    :active="route().current('products.*')"
                >
                    <template
                        v-slot:append
                        v-if="$page.props.low_stock_count > 0"
                    >
                        <v-badge
                            color="error"
                            :content="$page.props.low_stock_count"
                            inline
                        ></v-badge>
                    </template>
                </v-list-item>

                <v-list-item
                    prepend-icon="mdi-history"
                    title="History Stok"
                    @click="$inertia.visit(route('history.index'))"
                    :active="route().current('history.*')"
                    rounded="lg"
                ></v-list-item>

                <v-list-item
                    v-if="$page.props.auth.user.role === 'admin'"
                    prepend-icon="mdi-tag-multiple"
                    title="Kategori"
                    @click="$inertia.visit(route('categories.index'))"
                    :active="route().current('categories.*')"
                    rounded="lg"
                ></v-list-item>

                <v-list-item
                    v-if="$page.props.auth.user.role === 'admin'"
                    prepend-icon="mdi-file-chart-outline"
                    title="Laporan"
                    @click="$inertia.visit(route('reports.index'))"
                    :active="route().current('reports.*')"
                    rounded="lg"
                ></v-list-item>
            </v-list>

            <template v-slot:append>
                <div class="pa-4">
                    <v-list-item
                        v-if="$page.props.auth.user.role === 'admin'"
                        prepend-icon="mdi-account-cog"
                        title="Manajemen User"
                        @click="$inertia.visit(route('users.index'))"
                        :active="route().current('users.*')"
                        rounded="lg"
                        class="mb-2"
                    ></v-list-item>
                    <v-btn
                        block
                        color="error"
                        variant="tonal"
                        prepend-icon="mdi-logout"
                        class="text-none"
                        @click="logout"
                        rounded="lg"
                    >
                        Logout
                    </v-btn>
                </div>
            </template>
        </v-navigation-drawer>

        <v-app-bar flat border color="white">
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title class="text-body-1 font-weight-bold">
                {{ pageTitle }}
            </v-toolbar-title>
            <v-spacer></v-spacer>

            <div class="me-4 text-caption text-grey-darken-1 d-none d-sm-block">
                Logged in as:
                <span class="font-weight-bold text-primary">{{
                    $page.props.auth.user.name
                }}</span>
                <v-chip
                    size="x-small"
                    class="ms-2 text-uppercase"
                    color="primary"
                    variant="tonal"
                >
                    {{ $page.props.auth.user.role }}
                </v-chip>
            </div>
        </v-app-bar>

        <v-main class="bg-grey-lighten-5">
            <slot />
        </v-main>

        </v-app>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const drawer = ref(true);

const pageTitle = computed(() => {
    if (route().current("dashboard")) return "Dashboard Overview";
    if (route().current("products.*")) return "Stock Management";
    if (route().current("history.*")) return "Transaction Logs";
    if (route().current("categories.*")) return "Category Management";
    if (route().current("reports.*")) return "Reports Center";
    if (route().current("users.*")) return "User Settings";
    return "InvenTrack System";
});

const logout = () => {
    Swal.fire({
        title: "Mau keluar?",
        text: "Sesi lu bakal berakhir di sini bro.",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, Logout",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("logout"));
        }
    });
};
</script>