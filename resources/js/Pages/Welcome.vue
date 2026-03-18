<template>
    <v-app>
        <v-app-bar 
            flat 
            height="70" 
            color="rgba(255, 255, 255, 0.8)" 
            class="blur-backdrop border-b px-md-10"
        >
            <div class="d-flex align-center cursor-pointer" @click="$inertia.visit('/')">
                <v-icon icon="mdi-warehouse" color="primary" size="30" class="me-3"></v-icon>
                <v-toolbar-title class="font-weight-black text-grey-darken-4 tracking-tighter text-h5 text-uppercase">
                    Inven<span class="text-primary">Track</span>
                </v-toolbar-title>
            </div>

            <v-spacer></v-spacer>

            <div v-if="canLogin" class="d-none d-sm-flex align-center ga-5">
                <v-btn 
                    v-if="$page.props.auth.user" 
                    @click="$inertia.visit(route('dashboard'))" 
                    variant="flat" 
                    color="primary" 
                    rounded="lg" 
                    class="px-6 font-weight-bold text-none"
                >
                    Dashboard
                </v-btn>
                <template v-else>
                    <v-btn @click="$inertia.visit(route('login'))" variant="text" class="font-weight-bold text-none text-grey-darken-1">Masuk</v-btn>
                    <v-btn @click="$inertia.visit(route('register'))" variant="flat" color="primary" rounded="lg" class="px-6 font-weight-bold text-none shadow-sm">
                        Daftar
                    </v-btn>
                </template>
            </div>
        </v-app-bar>

        <v-main class="bg-white">
            <section class="hero-section position-relative d-flex align-center justify-center">
                <div class="hero-bg-wrapper">
                    <v-img src="/images/hero-warehouse.jpg" cover class="hero-img"></v-img>
                    <div class="hero-gradient-overlay"></div>
                </div>

                <v-container class="position-relative" style="z-index: 5">
                    <v-row justify="center">
                        <v-col cols="12" md="10" lg="8" class="text-center">
                            <v-fade-transition appear>
                                <div class="glass-content pa-6 pa-md-12 rounded-xl">
                                    <v-chip color="white" variant="outlined" class="mb-6 px-4 font-weight-bold text-uppercase tracking-widest" size="small">
                                        Professional Inventory System
                                    </v-chip>

                                    <h1 class="display-title font-weight-black mb-6 text-white leading-tight">
                                        Optimalisasi Aset & <br />
                                        <span class="text-primary-light">Akurasi Inventori.</span>
                                    </h1>

                                    <p class="text-h6 text-grey-lighten-3 mb-10 leading-relaxed font-weight-light max-width-700 mx-auto">
                                        Pantau setiap pergerakan barang secara presisi, real-time, dan efisien dengan infrastruktur manajemen stok modern.
                                    </p>

                                    <div class="d-flex flex-column flex-sm-row justify-center ga-4">
                                        <v-btn size="x-large" color="primary" variant="flat" rounded="xl" class="px-12 font-weight-black text-none btn-primary-glow" @click="$inertia.visit(route('login'))">
                                            Mulai Sekarang
                                        </v-btn>
                                        <v-btn size="x-large" variant="outlined" color="white" rounded="xl" class="px-10 font-weight-bold text-none border-2">
                                            Pelajari Fitur
                                        </v-btn>
                                    </div>
                                </div>
                            </v-fade-transition>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <v-container class="py-16">
                <v-row justify="center">
                    <v-col v-for="(feature, i) in features" :key="i" cols="12" md="4">
                        <v-card flat class="pa-10 feature-card-minimal h-100 rounded-xl text-center">
                            <v-avatar color="primary" variant="tonal" size="64" class="mb-6">
                                <v-icon :icon="feature.icon" size="32"></v-icon>
                            </v-avatar>
                            <h3 class="text-h5 font-weight-bold mb-4 text-grey-darken-4">{{ feature.title }}</h3>
                            <p class="text-body-1 text-grey-darken-1 leading-relaxed">{{ feature.desc }}</p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <v-footer class="bg-grey-darken-4 py-12 border-t border-grey-darken-3">
            <v-container>
                <v-row align="center" justify="space-between" no-gutters>
                    <v-col cols="12" md="6" class="text-center text-md-left mb-6 mb-md-0">
                        <div class="d-flex align-center justify-center justify-md-start mb-2">
                            <v-icon icon="mdi-warehouse" color="primary" size="24" class="me-2"></v-icon>
                            <span class="text-h6 font-weight-black text-white tracking-tighter text-uppercase">
                                Inven<span class="text-primary">Track</span>
                            </span>
                        </div>
                        <div class="text-body-2 text-grey-lighten-1">
                            Sistem Manajemen Inventori Terintegrasi & Real-time.
                        </div>
                    </v-col>

                    <v-col cols="12" md="6" class="text-center text-md-right">
                        <div class="d-flex justify-center justify-md-end ga-6 mb-4">
                            <a @click="$inertia.visit(route('login'))" class="dark-footer-link">Masuk Admin</a>
                            <a href="#" class="dark-footer-link">Kebijakan Privasi</a>
                            <a href="#" class="dark-footer-link">Bantuan</a>
                        </div>
                        <div class="text-caption text-grey-lighten-1">
                            &copy; 2026 <strong>InvenTrack</strong>. All Rights Reserved.
                        </div>
                    </v-col>
                </v-row>
            </v-container>
        </v-footer>
    </v-app>
</template>

<script setup>
defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const features = [
    {
        title: "Automated Tracking",
        desc: "Sistem identifikasi barang menggunakan teknologi QR Code untuk akurasi data maksimal.",
        icon: "mdi-barcode-scan",
    },
    {
        title: "Real-time Analytics",
        desc: "Laporan stok dan nilai aset yang diperbarui secara instan untuk keputusan bisnis cepat.",
        icon: "mdi-chart-areaspline",
    },
    {
        title: "Secure Audit Trail",
        desc: "Pencatatan mutasi stok secara kronologis yang menjamin transparansi operasional.",
        icon: "mdi-security",
    },
];
</script>

<style scoped>
/* Core Utilities */
.blur-backdrop { backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
.leading-relaxed { line-height: 1.7 !important; }
.tracking-tighter { letter-spacing: -1px !important; }
.cursor-pointer { cursor: pointer; }

/* Hero Section Styles */
.hero-section {
    height: 85vh;
    min-height: 600px;
    overflow: hidden;
}

.hero-bg-wrapper {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    z-index: 1;
}

.hero-img {
    height: 100%;
    width: 100%;
    filter: brightness(0.6) contrast(1.1);
}

.hero-gradient-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(135deg, rgba(18, 25, 35, 0.8) 0%, rgba(18, 25, 35, 0.4) 100%);
}

.glass-content {
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.display-title {
    font-size: clamp(2.5rem, 6vw, 4.5rem);
    letter-spacing: -2px !important;
    text-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.text-primary-light {
    color: #5cbbff;
}

/* Button & Card Interactions */
.btn-primary-glow {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    box-shadow: 0 10px 25px rgba(24, 103, 192, 0.4) !important;
}

.btn-primary-glow:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(24, 103, 192, 0.6) !important;
    background-color: #1e88e5 !important;
}

.border-2 { border-width: 2px !important; }

.feature-card-minimal {
    transition: all 0.4s ease;
    background: #ffffff;
}

.feature-card-minimal:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06) !important;
}

/* Footer Links Section */
.dark-footer-link {
    color: #BDBDBD;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
    cursor: pointer;
}

.dark-footer-link:hover {
    color: #5cbbff;
}

.max-width-700 { max-width: 700px; }
</style>