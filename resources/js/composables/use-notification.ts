import {useWebNotification} from "@vueuse/core";
import {usePage} from "@inertiajs/vue3";
import {computed, onBeforeMount} from "vue";
import {EventEnum, getEventIcon} from "@/common/enums";
import {Notification} from "@/types";
import {useToast} from "primevue/usetoast";
import {useSetting} from "@/composables/use-setting";

export const useNotification = () => {
    const page = usePage();
    const toast = useToast();
    const {show, isSupported} = useWebNotification();
    const {setting} = useSetting();

    const user = computed(() => page.props.auth.user);

    const unReadNotifications = computed(() => {
        return user.value?.unreadNotifications.map(notification => {
            return {
                ...notification,
                icon: getEventIcon(notification.event),
            };
        });
    });

    const unReadNotificationCount = computed(() => {
        if (unReadNotifications.value.length === 0) return undefined;
        return unReadNotifications.value.length < 10 ? `${unReadNotifications.value.length}` : "9+";
    });

    const addNewNotification = (data: Notification) => {
        data.created_at = window.dayjs().fromNow();

        // Limit the unread notifications to 10
        if (unReadNotificationCount.value === "9+") {
            user.value.unreadNotifications.pop();
        }

        if (user.value.unreadNotifications.some((n: Notification) => n.id === data.id)) return;

        user.value.unreadNotifications.unshift(data);
    };

    const showNotification = (data: Notification) => {
        addNewNotification(data);

        toast.removeAllGroups();

        toast.add({
            severity: "info",
            group: "realtime-notification",
            detail: {
                from: data?.from,
                summary: data.summary,
                createdAt: window.dayjs().fromNow(),
                viewUrl: route("dashboard.notifications.show", data.id),
            }
        });

        if (isSupported.value) {
            show({
                title: data.title,
                body: data?.summary,
                lang: "en",
                dir: "auto",
                tag: data.event,
                icon: setting("site.logo")
            });
        }
    };

    const listenForRealtimeNotifications = () => {
        const channel = window.Echo.private(`admin-notification`);
        channel
            .listen(`.${EventEnum.IMPORT_COMPLETED}`, (data: Notification) => {
                showNotification(data);
            })
            .listen(`.${EventEnum.ORDER_PLACED}`, (data: Notification) => {
                showNotification(data);
            });
    };

    onBeforeMount(() => {
        listenForRealtimeNotifications();
    });

    return {
        unReadNotifications,
        unReadNotificationCount,
        showNotification
    };
};
