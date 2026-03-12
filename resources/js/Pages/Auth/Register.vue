<template>
  <v-app>
    <v-main class="bg-grey-lighten-4 d-flex align-center justify-center">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" sm="8" md="5">
            
            <div class="text-center mb-6">
              <v-icon icon="mdi-account-plus-outline" size="64" color="primary" class="mb-2"></v-icon>
              <h1 class="text-h4 font-weight-black text-grey-darken-3">Daftar Akun</h1>
              <p class="text-grey-darken-1">Mulai kelola inventaris Anda sekarang</p>
            </div>

            <v-card elevation="2" rounded="xl" class="pa-6">
              <v-form @submit.prevent="submit">
                
                <v-text-field
                  v-model="form.name"
                  label="Nama Lengkap"
                  prepend-inner-icon="mdi-account-outline"
                  variant="outlined"
                  density="comfortable"
                  class="mb-2"
                  :error-messages="form.errors.name"
                  required
                  autofocus
                ></v-text-field>

                <v-text-field
                  v-model="form.email"
                  label="Email Address"
                  prepend-inner-icon="mdi-email-outline"
                  variant="outlined"
                  density="comfortable"
                  class="mb-2"
                  :error-messages="form.errors.email"
                  required
                ></v-text-field>

                <v-text-field
                  v-model="form.password"
                  label="Password"
                  prepend-inner-icon="mdi-lock-outline"
                  :type="showPassword ? 'text' : 'password'"
                  :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                  @click:append-inner="showPassword = !showPassword"
                  variant="outlined"
                  density="comfortable"
                  class="mb-2"
                  :error-messages="form.errors.password"
                  required
                ></v-text-field>

                <v-text-field
                  v-model="form.password_confirmation"
                  label="Konfirmasi Password"
                  prepend-inner-icon="mdi-lock-check-outline"
                  type="password"
                  variant="outlined"
                  density="comfortable"
                  class="mb-4"
                  :error-messages="form.errors.password_confirmation"
                  required
                ></v-text-field>

                <v-btn
                  type="submit"
                  color="primary"
                  block
                  size="large"
                  variant="flat"
                  rounded="lg"
                  class="text-none font-weight-bold"
                  :loading="form.processing"
                >
                  Daftar Sekarang
                </v-btn>
              </v-form>

              <div class="text-center mt-6">
                <span class="text-grey-darken-1 text-body-2">Sudah punya akun? </span>
                <v-btn 
                  variant="text" 
                  color="primary" 
                  class="text-none font-weight-bold px-1" 
                  @click="$inertia.visit(route('login'))"
                >
                  Masuk di sini
                </v-btn>
              </div>
            </v-card>

            <p class="text-center mt-6 text-grey-darken-1 text-caption">
              © 2026 InvenTrack System. Secure & Reliable.
            </p>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const showPassword = ref(false)

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>