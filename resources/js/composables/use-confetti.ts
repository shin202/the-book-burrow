import {onBeforeMount} from "vue";
import confetti from "canvas-confetti";

export const useConfetti = (options?: confetti.Options) => {
    onBeforeMount(() => {
        confetti(options);
    });
};
