<script lang="ts" setup>
import {computed} from "vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps<{
    href: string
}>();

const currentSubdomain = computed(() => {
    return window.location.hostname.split(".")[1] ? window.location.hostname.split(".")[0] : null;
});

const linkedSubdomain = computed(() => {
    const linkedSubdomain = props.href.split(".")[1] ? props.href.split(".")[0] : null;
    return linkedSubdomain?.replace(/https?\:\/{2}/, "");
});

const isSameSubdomain = computed(() => {
    return currentSubdomain.value === linkedSubdomain.value;
});

const isExternal = computed(() => {
    return !isSameSubdomain.value;
});

const visit = (e: any) => {
    e.preventDefault();
    window.location.href = props.href;
};
</script>

<template>
    <Link v-if="!isExternal" :href="href">
        <slot/>
    </Link>
    <a v-else :href="href" @click="visit">
        <slot/>
    </a>
</template>

<style lang="scss" scoped>

</style>
