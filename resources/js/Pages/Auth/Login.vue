<template>
  <v-app>
    <v-main class="bg-grey-lighten-4 d-flex align-center justify-center">
      <v-container>
        <v-row justify="center">
          <v-col cols="12" sm="8" md="4">
            
            <div class="text-center mb-6">
              <v-icon icon="mdi-warehouse" size="64" color="primary" class="mb-2"></v-icon>
              <h1 class="text-h4 font-weight-black text-grey-darken-3">InvenTrack</h1>
              <p class="text-grey-darken-1">Silakan masuk ke sistem gudang</p>
            </div>

            <v-card elevation="2" rounded="xl" class="pa-6">
              <v-form @submit.prevent="submit">
                
                <v-text-field
                  v-model="form.email"
                  label="Email Address"
                  prepend-inner-icon="mdi-email-outline"
                  variant="outlined"
                  density="comfortable"
                  class="mb-2"
                  :error-messages="form.errors.email"
                  required
                  autofocus
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

                <div class="d-flex align-center justify-space-between mb-4">
                  <v-checkbox
                    v-model="form.remember"
                    label="Ingat Saya"
                    hide-details
                    color="primary"
                    density="compact"
                  ></v-checkbox>
                  
                  <a href="#" class="text-caption text-primary text-decoration-none font-weight-bold">
                    Lupa Password?
                  </a>
                </div>

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
                  Masuk Sekarang
                </v-btn>
              </v-form>
            </v-card>

            <p class="text-center mt-6 text-grey-darken-1 text-caption">
              © 2026 InvenTrack System. All Rights Reserved.
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
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>