<template>
    <Head title="Masuk Ke Sistem" />

    <v-app>
        <v-main class="bg-background d-flex align-center justify-center py-10">
            <v-container>
                <v-row justify="center">
                    <v-col cols="12" sm="8" md="6" lg="4">
                        <div
                            class="d-flex flex-column align-center text-center mb-8"
                        >
                            <v-avatar
                                color="primary"
                                size="72"
                                class="elevation-8 mb-4 rotate-hover"
                                @click="$inertia.visit('/')"
                                style="cursor: pointer"
                            >
                                <v-icon
                                    icon="mdi-warehouse"
                                    size="40"
                                    color="white"
                                ></v-icon>
                            </v-avatar>

                            <h1
                                class="text-h4 font-weight-black tracking-tighter text-uppercase"
                            >
                                Inven<span class="text-primary">Track</span>
                            </h1>
                            <p
                                class="text-body-2 text-medium-emphasis font-weight-medium"
                            >
                                Sistem Manajemen Inventori Modern
                            </p>
                        </div>

                        <v-card
                            elevation="0"
                            rounded="xl"
                            class="pa-8 border-sm shadow-soft bg-surface"
                        >
                            <div class="text-center mb-8">
                                <h2 class="text-h5 font-weight-bold">
                                    Masukkan akun anda
                                </h2>
                                <p class="text-caption text-medium-emphasis">
                                    lengkapi data di bawah untuk akses penuh.
                                </p>
                                <v-divider
                                    class="mt-4 mx-auto border-opacity-100"
                                    width="40"
                                    thickness="3"
                                    color="primary"
                                ></v-divider>
                            </div>

                            <v-alert
                                v-if="status"
                                type="success"
                                variant="tonal"
                                density="compact"
                                class="mb-6 text-caption"
                            >
                                {{ status }}
                            </v-alert>

                            <v-form @submit.prevent="submit">
                                <v-text-field
                                    v-model="form.email"
                                    label="Email Address"
                                    prepend-inner-icon="mdi-email-outline"
                                    variant="outlined"
                                    density="comfortable"
                                    class="mb-2"
                                    rounded="lg"
                                    color="primary"
                                    :error-messages="form.errors.email"
                                    required
                                    autofocus
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.password"
                                    label="Password"
                                    prepend-inner-icon="mdi-lock-outline"
                                    :type="showPassword ? 'text' : 'password'"
                                    :append-inner-icon="
                                        showPassword ? 'mdi-eye-off' : 'mdi-eye'
                                    "
                                    @click:append-inner="
                                        showPassword = !showPassword
                                    "
                                    variant="outlined"
                                    density="comfortable"
                                    class="mb-2"
                                    rounded="lg"
                                    color="primary"
                                    :error-messages="form.errors.password"
                                    required
                                ></v-text-field>

                                <div
                                    class="d-flex align-center justify-space-between mb-8"
                                >
                                    <v-checkbox
                                        v-model="form.remember"
                                        label="Ingat Saya"
                                        hide-details
                                        color="primary"
                                        density="compact"
                                        class="text-caption"
                                    ></v-checkbox>

                                    <Link
                                        v-if="canResetPassword"
                                        :href="route('password.request')"
                                        class="text-caption text-primary text-decoration-none font-weight-bold hover-link"
                                    >
                                        Lupa Password?
                                    </Link>
                                </div>

                                <v-btn
                                    type="submit"
                                    color="primary"
                                    block
                                    size="x-large"
                                    variant="flat"
                                    rounded="lg"
                                    class="text-none font-weight-black mb-4"
                                    :loading="form.processing"
                                >
                                    Masuk Ke Dashboard
                                </v-btn>

                                <div class="text-center">
                                    <span
                                        class="text-caption text-medium-emphasis"
                                        >Belum punya akun?
                                    </span>
                                    <Link
                                        :href="route('register')"
                                        class="text-caption font-weight-bold text-primary text-decoration-none hover-link"
                                    >
                                        Daftar Sekarang
                                    </Link>
                                </div>
                            </v-form>
                        </v-card>

                        <p
                            class="text-center mt-10 text-medium-emphasis text-caption"
                        >
                            &copy; 2026
                            <strong>InvenTrack</strong>.<br />
                            Manage with ease.
                        </p>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const showPassword = ref(false);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>
<style scoped>
/* 1. Paksa background field jadi transparan atau gelap */
:deep(.v-field__confirm),
:deep(.v-field__outline),
:deep(.v-field__field),
:deep(.v-field__input),
:deep(.v-field) {
    background-color: transparent !important; /* Hapus warna putih paksaan */
    --v-field-padding-bottom: 0px;
}

/* 2. Paksa warna teks jadi putih (atau ngikutin tema) */
:deep(input) {
    color: inherit !important;
    opacity: 1 !important;
}

/* 3. Warna Label (Nama Lengkap, Email, dll) */
:deep(.v-label) {
    color: rgba(var(--v-theme-on-surface), 0.7) !important;
}

/* 4. Border tipis adaptif */
.border-sm {
    border: 1px solid rgba(var(--v-border-color), 0.12) !important;
}

.shadow-soft {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
}

.tracking-tighter {
    letter-spacing: -1.5px !important;
}

/* Animasi Logo */
.rotate-hover {
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.rotate-hover:hover {
    transform: rotate(-10deg) scale(1.1);
}

.hover-link:hover {
    text-decoration: underline !important;
    opacity: 0.8;
}

@media (prefers-color-scheme: dark) {
    .shadow-soft {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3) !important;
    }
}
</style>
