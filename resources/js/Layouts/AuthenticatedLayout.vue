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
                    @click="router.visit(route('dashboard'))"
                    :active="route().current('dashboard')"
                    rounded="lg"
                ></v-list-item>

                <v-list-item
                    rounded="lg"
                    prepend-icon="mdi-package-variant"
                    title="Data Produk"
                    @click="router.visit(route('products.index'))"
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
                    title="Riwayat Stok"
                    @click="router.visit(route('history.index'))"
                    :active="route().current('history.*')"
                    rounded="lg"
                ></v-list-item>

                <template v-if="$page.props.auth.user.role === 'admin'">
                    <v-list-subheader
                        class="text-uppercase text-caption font-weight-bold"
                        >Admin Area</v-list-subheader
                    >

                    <v-list-item
                        prepend-icon="mdi-tag-multiple"
                        title="Kategori"
                        @click="router.visit(route('categories.index'))"
                        :active="route().current('categories.*')"
                        rounded="lg"
                    ></v-list-item>

                    <v-list-item
                        prepend-icon="mdi-truck-delivery-outline"
                        title="Suppliers"
                        @click="router.visit(route('suppliers.index'))"
                        :active="route().current('suppliers.*')"
                        rounded="lg"
                    ></v-list-item>

                    <v-list-item
                        prepend-icon="mdi-file-chart-outline"
                        title="Laporan"
                        @click="router.visit(route('reports.index'))"
                        :active="route().current('reports.*')"
                        rounded="lg"
                    ></v-list-item>
                </template>
            </v-list>

            <template v-slot:append>
                <div class="pa-4">
                    <v-list-item
                        v-if="$page.props.auth.user.role === 'admin'"
                        prepend-icon="mdi-account-cog-outline"
                        title="Manajemen User"
                        @click="router.visit(route('users.index'))"
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

        <v-app-bar flat border-b>
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-toolbar-title class="text-body-1 font-weight-bold">
                {{ pageTitle }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn
                :icon="
                    theme.global.current.value.dark
                        ? 'mdi-weather-sunny'
                        : 'mdi-weather-night'
                "
                @click="toggleTheme"
                variant="text"
                color="primary"
                class="me-2"
            ></v-btn>

            <div class="me-4 d-none d-sm-flex align-center">
                <div class="text-end me-3">
                    <div class="text-caption font-weight-bold text-primary">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="text-overline" style="line-height: 1">
                        {{ $page.props.auth.user.role }}
                    </div>
                </div>
                <v-avatar color="primary" variant="tonal" size="32">
                    <v-icon size="20">mdi-account</v-icon>
                </v-avatar>
            </div>
        </v-app-bar>

        <v-main
            :class="theme.global.current.value.dark ? '' : 'bg-grey-lighten-5'"
        >
            <v-container fluid class="pa-6">
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
import { useTheme } from "vuetify";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const drawer = ref(true);
const theme = useTheme();

const pageTitle = computed(() => {
    if (route().current("dashboard")) return "Dashboard Overview";
    if (route().current("products.*")) return "Inventory Management";
    if (route().current("history.*")) return "Stock Movement History";
    if (route().current("categories.*")) return "Product Categories";
    if (route().current("reports.*")) return "Business Reports";
    if (route().current("users.*")) return "User Management";
    if (route().current("suppliers.*")) return "Supplier List";
    return "InvenTrack System";
});

const toggleTheme = () => {
    theme.global.name.value = theme.global.current.value.dark
        ? "light"
        : "dark";
};

const logout = () => {
    Swal.fire({
        title: "Mau keluar, Bro?",
        text: "Pastikan kerjaan lu udah disimpan semua ya.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF5350",
        cancelButtonColor: "#607D8B",
        confirmButtonText: "Ya, Keluar!",
        cancelButtonText: "Batal",
        background: theme.global.current.value.dark ? "#1E1E1E" : "#FFFFFF",
        color: theme.global.current.value.dark ? "#FFFFFF" : "#000000",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("logout"));
        }
    });
};
</script>

<style>
/* Biar transisi warna background halus pas ganti tema */
.v-application {
    transition: background 0.3s ease-in-out !important;
}
.v-main {
    transition: background-color 0.3s ease-in-out !important;
}
</style>
