<template>
    <v-app>
        <v-app-bar
            flat
            color="rgba(255, 255, 255, 0.8)"
            border
            class="blur-backdrop"
        >
            <v-container class="d-flex align-center">
                <v-avatar color="primary" size="36" class="me-3">
                    <v-icon
                        icon="mdi-warehouse"
                        color="white"
                        size="20"
                    ></v-icon>
                </v-avatar>
                <v-toolbar-title
                    class="font-weight-black text-primary text-uppercase tracking-wider"
                    >InvenTrack</v-toolbar-title
                >
                <v-spacer></v-spacer>

                <div v-if="canLogin" class="d-none d-sm-flex">
                    <v-btn
                        v-if="$page.props.auth.user"
                        @click="$inertia.visit(route('dashboard'))"
                        variant="flat"
                        color="primary"
                        rounded="lg"
                        class="text-none px-6 shadow-sm"
                    >
                        Ke Dashboard
                    </v-btn>
                    <template v-else>
                        <v-btn
                            @click="$inertia.visit(route('login'))"
                            variant="text"
                            class="text-none me-2 font-weight-bold"
                            >Masuk</v-btn
                        >
                        <v-btn
                            @click="$inertia.visit(route('register'))"
                            variant="flat"
                            color="primary"
                            class="text-none shadow-sm"
                            rounded="lg"
                            >Daftar Sekarang</v-btn
                        >
                    </template>
                </div>
            </v-container>
        </v-app-bar>

        <v-main class="bg-white">
            <section class="hero-section py-24 bg-white">
                <v-container>
                    <v-row align="center">
                        <v-col
                            cols="12"
                            md="4"
                            class="text-center text-md-left position-relative"
                            style="z-index: 2"
                        >
                            <v-chip
                                color="primary"
                                variant="flat"
                                class="mb-5 px-4 font-weight-bold animate-bounce"
                            >
                                🚀 InvenTrack v1.0
                            </v-chip>
                            <h1
                                class="display-title font-weight-black mb-6 text-grey-darken-4"
                                style="line-height: 1.1"
                            >
                                Kendali Stok <br />
                                <span class="text-gradient">Lebih mudah.</span>
                            </h1>
                            <p
                                class="text-subtitle-1 text-grey-darken-1 mb-10 font-weight-regular leading-relaxed pr-md-6"
                            >
                                Solusi inventori untuk memantau setiap
                                barang, kategori, dan history transaksi secara
                                real-time.
                            </p>
                            <div
                                class="d-flex ga-4 justify-center justify-md-start"
                            >
                                <v-btn
                                    size="x-large"
                                    color="primary"
                                    variant="flat"
                                    rounded="xl"
                                    class="text-none px-12 shadow-lg"
                                    @click="$inertia.visit(route('login'))"
                                >
                                    Mulai Sekarang
                                </v-btn>
                            </div>
                        </v-col>

                        <v-col
                            cols="12"
                            md="8"
                            class="position-relative mt-12 mt-md-0"
                        >
                            <div class="hero-blob-shadow"></div>

                            <v-card
                                flat
                                class="bg-transparent overflow-hidden animate-float-scale"
                            >
                                <v-img
                                    src="/images/hero-warehouse.jpg"
                                    alt="Isometrik Warehouse Solution"
                                    cover
                                    class="rounded-xl shadow-hero image-hover-zoom"
                                >
                                    <div
                                        class="fill-height d-flex align-center justify-center"
                                        style="
                                            background: linear-gradient(
                                                135deg,
                                                rgba(24, 103, 192, 0.02) 0%,
                                                rgba(255, 255, 255, 0) 100%
                                            );
                                        "
                                    ></div>
                                </v-img>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <v-container class="py-16">
                <v-row>
                    <v-col
                        v-for="(feature, i) in features"
                        :key="i"
                        cols="12"
                        md="4"
                    >
                        <v-card
                            border
                            flat
                            class="pa-8 feature-card h-100"
                            rounded="xl"
                        >
                            <v-avatar
                                :color="feature.color"
                                variant="tonal"
                                size="64"
                                class="mb-6 feature-icon"
                            >
                                <v-icon :icon="feature.icon" size="32"></v-icon>
                            </v-avatar>
                            <h3 class="text-h5 font-weight-bold mb-3">
                                {{ feature.title }}
                            </h3>
                            <p
                                class="text-body-1 text-grey-darken-1 leading-relaxed"
                            >
                                {{ feature.desc }}
                            </p>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <v-footer class="bg-grey-darken-4 py-16">
            <v-container>
                <v-row>
                    <v-col cols="12" md="6">
                        <div class="font-weight-black text-h4 mb-4 text-white">
                            Inven<span class="text-primary">Track</span>
                        </div>
                        <p class="text-grey-lighten-1 mb-6 max-width-400">
                            aplikasi inventori sederhana yang dibuat untuk
                            selesaikan masalah stok di gudang.
                        </p>
                    </v-col>
                    <v-col cols="12" md="6" class="text-md-right">
                        <div class="text-h6 text-white mb-4">
                            Mulai Sekarang
                        </div>
                        <v-btn
                            icon="mdi-github"
                            variant="tonal"
                            class="me-2"
                            color="white"
                        ></v-btn>
                        <v-btn
                            icon="mdi-linkedin"
                            variant="tonal"
                            class="me-2"
                            color="white"
                        ></v-btn>
                        <v-btn
                            icon="mdi-instagram"
                            variant="tonal"
                            color="white"
                        ></v-btn>
                    </v-col>
                </v-row>
                <v-divider color="grey-darken-2" class="my-10"></v-divider>
                <div class="text-center text-caption text-grey">
                    &copy; 2026 InvenTrack System. Crafted with ⚡ using Laravel
                    & Vuetify.
                </div>
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
        title: "Real-time Tracking",
        desc: "Pantau stok barang masuk dan keluar secara instan tanpa perlu refresh halaman. Data lu selalu up-to-date.",
        icon: "mdi-lightning-bolt",
        color: "amber-darken-2",
    },
    {
        title: "Audit History",
        desc: "Setiap pergerakan stok dicatat lengkap dengan referensi dan catatan admin. Traceability 100%.",
        icon: "mdi-history",
        color: "blue",
    },
    {
        title: "Stock Analytics",
        desc: "Dapatkan laporan nilai aset dan peringatan stok kritis secara otomatis lewat grafik interaktif.",
        icon: "mdi-chart-areaspline",
        color: "success",
    },
];
</script>

<style scoped>
.blur-backdrop {
    backdrop-filter: blur(10px);
}

.text-gradient {
    background: linear-gradient(to right, #1867c0, #5cbbff);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
}

.hero-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(180deg, #f5f7fa 0%, #ffffff 100%);
}

.display-title {
    font-size: clamp(2.5rem, 5vw, 4.5rem);
}

.shape-1 {
    position: absolute;
    top: -100px;
    right: -100px;
    width: 400px;
    height: 400px;
    background: radial-gradient(
        circle,
        rgba(24, 103, 192, 0.05) 0%,
        rgba(255, 255, 255, 0) 70%
    );
    border-radius: 50%;
}

.shape-2 {
    position: absolute;
    bottom: 50px;
    left: -50px;
    width: 300px;
    height: 300px;
    background: radial-gradient(
        circle,
        rgba(24, 103, 192, 0.03) 0%,
        rgba(255, 255, 255, 0) 70%
    );
    border-radius: 50%;
}

/* ANIMATIONS */
.animate-float {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
    100% {
        transform: translateY(0px);
    }
}

.hero-card-preview {
    border: 8px solid white !important;
    transform: perspective(1000px) rotateY(-5deg);
    transition: 0.5s;
}

.feature-card {
    transition: all 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
    border-color: #1867c0 !important;
    box-shadow: 0 20px 40px rgba(24, 103, 192, 0.1) !important;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-icon {
    transition: all 0.3s ease;
}

.leading-relaxed {
    line-height: 1.6 !important;
}

.max-width-400 {
    max-width: 400px;
}
.animate-float {
    animation: floatImage 4s ease-in-out infinite;
    transition: transform 0.3s ease;
}

@keyframes floatImage {
    0% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-15px) rotate(1deg);
    }
    100% {
        transform: translateY(0px) rotate(0deg);
    }
}

.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
}

@media (max-width: 600px) {
    .display-title {
        font-size: 2.5rem !important;
    }
}

.animate-float-scale {
    animation: floatScale 6s ease-in-out infinite;
    transition: transform 0.3s ease;
}

@keyframes floatScale {
    0% { transform: translateY(0px) rotate(0deg) scale(1); }
    50% { transform: translateY(-20px) rotate(0.5deg) scale(1.02); } /* Naik dikit, gede dikit */
    100% { transform: translateY(0px) rotate(0deg) scale(1); }
}

.shadow-hero {
    box-shadow: 0 40px 100px -20px rgba(0, 100, 200, 0.15) !important; /* Bayangan biru biar sinkron */
    border: 1px solid rgba(255,255,255,0.3) !important;
}

.hero-blob-shadow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(24, 103, 192, 0.05) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    filter: blur(50px);
    z-index: 1;
}

@media (max-width: 600px) {
    .display-title {
        font-size: 2.8rem !important;
    }
    .text-subtitle-1 {
        font-size: 1rem !important;
    }
    .mt-md-0 {
        margin-top: 100px !important; /* Biar ada jarak sama teks */
    }
}
</style>
