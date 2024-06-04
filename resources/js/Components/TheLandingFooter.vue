<script lang="ts" setup>
import LogoText from "@/Components/LogoText.vue";
import CopyrightFooter from "@/Components/CopyrightFooter.vue";
import {useSetting} from "@/composables";

const {setting} = useSetting();
</script>

<template>
    <footer class="footer">
        <div class="footer__inner">
            <div class="flex flex-col md:flex-row gap-10">
                <div class="flex flex-col justify-center items-start">
                    <LogoText/>
                    <div class="w-62 flex flex-col mt-8 text-sm">
                        <p class="mb-6" v-text="setting('site.address')"/>
                        <div class="mb-6">
                            <p>
                                Monday - Friday: 9:00 AM - 5:00 PM
                            </p>
                            <p>
                                Saturday - Sunday: Closed
                            </p>
                        </div>
                        <p class="text-lg hover:text-primary-900">
                            <a :href="`mailto:${setting('site.email')}`" v-text="setting('site.email')"/>
                        </p>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold mb-4 text-md">Follow us</h2>
                    <ul class="flex flex-col lg:flex-row gap-4 justify-center">
                        <li>
                            <a :href="setting('site.socials.facebook')" class="flex items-center gap-2" target="_blank">
                                <i class="pi pi-facebook"/>
                                <span>Facebook</span>
                            </a>
                        </li>
                        <li>
                            <a :href="setting('site.socials.twitter')" class="flex items-center gap-2" target="_blank">
                                <i class="pi pi-twitter"/>
                                <span>Twitter</span>
                            </a>
                        </li>
                        <li>
                            <a :href="setting('site.socials.instagram')" class="flex items-center gap-2"
                               target="_blank">
                                <i class="pi pi-instagram"/>
                                <span>Instagram</span>
                            </a>
                        </li>
                        <li>
                            <a :href="setting('site.socials.youtube')" class="flex items-center gap-2" target="_blank">
                                <i class="pi pi-youtube"/>
                                <span>Youtube</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <CopyrightFooter/>
        </div>
    </footer>
</template>

<style lang="scss" scoped>
.footer {
    @apply w-full bg-primary-300 relative;

    &__inner {
        @apply py-20 px-8 flex flex-col divide-y divide-surface-500;
    }
}
</style>
