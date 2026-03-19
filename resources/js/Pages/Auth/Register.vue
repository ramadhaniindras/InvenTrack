<template>
    <v-app>
        <v-main
            class="bg-grey-lighten-5 d-flex align-center justify-center py-10"
        >
            <v-container>
                <v-row justify="center">
                    <v-col cols="12" sm="8" md="6" lg="4">
                        <div
                            class="d-flex flex-column align-center text-center mb-8"
                        >
                            <v-avatar
                                color="primary"
                                size="72"
                                class="elevation-4 mb-4"
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
                                class="text-h4 font-weight-black text-grey-darken-4 tracking-tighter text-uppercase"
                            >
                                Inven<span class="text-primary">Track</span>
                            </h1>
                            <p
                                class="text-body-2 text-grey-darken-1 font-weight-medium"
                            >
                                Buat Akun Administrator Baru
                            </p>
                        </div>

                        <v-card
                            elevation="0"
                            rounded="xl"
                            class="pa-8 border shadow-soft"
                        >
                            <div class="text-center mb-8">
                                <h2
                                    class="text-h5 font-weight-bold text-grey-darken-4"
                                >
                                    Daftar Sistem
                                </h2>
                                <p class="text-caption text-grey-darken-1">
                                    Lengkapi data di bawah untuk akses penuh.
                                </p>
                                <v-divider
                                    class="mt-4 mx-auto"
                                    width="50"
                                    thickness="3"
                                    color="primary"
                                ></v-divider>
                            </div>

                            <v-form @submit.prevent="submit">
                                <v-text-field
                                    v-model="form.name"
                                    label="Nama Lengkap"
                                    placeholder=""
                                    prepend-inner-icon="mdi-account-outline"
                                    variant="outlined"
                                    density="comfortable"
                                    class="mb-2"
                                    rounded="lg"
                                    color="primary"
                                    :error-messages="form.errors.name"
                                    required
                                    autofocus
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.email"
                                    label="Email Address"
                                    placeholder=""
                                    prepend-inner-icon="mdi-email-outline"
                                    variant="outlined"
                                    density="comfortable"
                                    class="mb-2"
                                    rounded="lg"
                                    color="primary"
                                    :error-messages="form.errors.email"
                                    required
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.password"
                                    label="Password"
                                    placeholder=""
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

                                <v-text-field
                                    v-model="form.password_confirmation"
                                    label="Konfirmasi Password"
                                    placeholder=""
                                    prepend-inner-icon="mdi-lock-check-outline"
                                    :type="showPassword ? 'text' : 'password'"
                                    variant="outlined"
                                    density="comfortable"
                                    class="mb-6"
                                    rounded="lg"
                                    color="primary"
                                    :error-messages="
                                        form.errors.password_confirmation
                                    "
                                    required
                                ></v-text-field>

                                <v-btn
                                    type="submit"
                                    color="primary"
                                    block
                                    size="x-large"
                                    variant="flat"
                                    rounded="lg"
                                    class="text-none font-weight-black elevation-0 mb-4"
                                    :loading="form.processing"
                                >
                                    Buat Akun Sekarang
                                </v-btn>

                                <div class="text-center">
                                    <span class="text-caption text-grey"
                                        >Sudah punya akun?
                                    </span>
                                    <Link
                                        :href="route('login')"
                                        class="text-caption font-weight-bold text-primary text-decoration-none"
                                    >
                                        Masuk
                                    </Link>
                                </div>
                            </v-form>
                        </v-card>

                        <p
                            class="text-center mt-10 text-grey-darken-1 text-caption"
                        >
                            &copy; 2026 <strong>InvenTrack System</strong>. All
                            Rights Reserved.
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

const showPassword = ref(false);

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<style scoped>
.bg-grey-lighten-5 {
    background-color: #f8fafc !important;
}

.border {
    border: 1px solid #e2e8f0 !important;
}

.shadow-soft {
    box-shadow:
        0 10px 25px -5px rgba(0, 0, 0, 0.05),
        0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
}

.tracking-tighter {
    letter-spacing: -1.5px !important;
}

.text-none {
    text-transform: none !important;
}
</style>
