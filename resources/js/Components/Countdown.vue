<script lang="ts" setup>
import {computed, ref} from "vue";
import {useIntervalFn} from "@vueuse/core";

const props = defineProps<{
    from: Date | string,
    to: Date | string
}>();

const from = ref(new Date(props.from));
const to = ref(new Date(props.to));

const diffInDays = computed(() => {
    return window.dayjs(to.value).diff(from.value, "days");
});

const diffInHours = computed(() => {
    return window.dayjs(to.value).diff(from.value, "hours") % 24;
});

const diffInMinutes = computed(() => {
    return window.dayjs(to.value).diff(from.value, "minutes") % 60;
});

const diffInSeconds = computed(() => {
    return window.dayjs(to.value).diff(from.value, "seconds") % 60;
});

useIntervalFn(() => {
    from.value = new Date();
}, 1000);
</script>

<template>
    <div class="flex justify-center items-center text-lime-600">
        <div class="days mr-2 relative">
            <div class="h-10">{{ diffInDays }}</div>
            <div class="text-sm absolute bottom-0 left-2/4 -translate-x-2/4">d</div>
        </div>
        <span class="leading-snug -translate-y-2">:</span>
        <div class="hours mx-2 mr-2 relative">
            <div class="h-10">{{ diffInHours }}</div>
            <div class="text-sm absolute bottom-0 left-2/4 -translate-x-2/4">h</div>
        </div>
        <span class="leading-snug -translate-y-2">:</span>
        <div class="minutes mx-2 mr-2 relative">
            <div class="h-10"> {{ diffInMinutes }}</div>
            <div class="text-sm absolute bottom-0 left-2/4 -translate-x-2/4">m</div>
        </div>
        <span class="leading-snug -translate-y-2">:</span>
        <div class="seconds mx-2 relative">
            <div class="h-10">{{ diffInSeconds }}</div>
            <div class="text-sm absolute bottom-0 left-2/4 -translate-x-2/4">s</div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>
