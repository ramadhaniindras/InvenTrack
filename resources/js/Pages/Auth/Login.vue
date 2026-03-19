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
                                Sistem Manajemen Inventori Modern
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
                                    Selamat Datang
                                </h2>
                                <p class="text-caption text-grey-darken-1">
                                    Masukkan kredensial untuk akses gudang.
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
                                    autofocus
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
                                        class="text-caption text-primary text-decoration-none font-weight-bold"
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
                                    class="text-none font-weight-black elevation-0 mb-4"
                                    :loading="form.processing"
                                >
                                    Masuk Ke Dashboard
                                </v-btn>

                                <div class="text-center">
                                    <span class="text-caption text-grey"
                                        >Belum punya akun?
                                    </span>
                                    <Link
                                        :href="route('register')"
                                        class="text-caption font-weight-bold text-primary text-decoration-none"
                                    >
                                        Daftar
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
.bg-grey-lighten-5 {
    background-color: #f8fafc !important;
}
.border {
    border: 1px solid #e2e8f0 !important;
}
.shadow-soft {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05) !important;
}
.tracking-tighter {
    letter-spacing: -1.5px !important;
}
.text-none {
    text-transform: none !important;
}
</style>
