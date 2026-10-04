import { reactive } from 'vue';

type Toast = {
    id: number;
    message: string;
};

export const toasts = reactive<Toast[]>([]);

let nextId = 1;

export function toast(message: string): void {
    const id = nextId++;
    toasts.push({ id, message });

    setTimeout(() => {
        const index = toasts.findIndex((item) => item.id === id);
        if (index !== -1) {
            toasts.splice(index, 1);
        }
    }, 3500);
}
