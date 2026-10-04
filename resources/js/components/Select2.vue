<script setup lang="ts">
import { ChevronDown } from '@lucide/vue';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    useId,
    watch,
} from 'vue';

export type Select2Option = {
    value: string | number;
    label: string;
};

const props = withDefaults(
    defineProps<{
        options: Select2Option[];
        placeholder?: string;
        disabled?: boolean;
        invalid?: boolean;
        searchable?: boolean;
        emptyText?: string;
    }>(),
    {
        placeholder: 'Select...',
        disabled: false,
        invalid: false,
        searchable: true,
        emptyText: 'No matches.',
    },
);

const model = defineModel<string | number>();

const open = ref(false);
const search = ref('');
const activeIndex = ref(0);
const rootRef = ref<HTMLDivElement | null>(null);
const triggerRef = ref<HTMLButtonElement | null>(null);
const searchRef = ref<HTMLInputElement | null>(null);
const listId = `select2-${useId()}`;

const selected = computed(() =>
    props.options.find(
        (option) => String(option.value) === String(model.value ?? ''),
    ),
);

const display = computed(() => selected.value?.label ?? props.placeholder);

const isPlaceholder = computed(() => selected.value === undefined);

const filtered = computed(() => {
    const query = search.value.trim().toLowerCase();

    if (query === '') {
        return props.options;
    }

    return props.options.filter((option) =>
        option.label.toLowerCase().includes(query),
    );
});

const showSearch = computed(() => props.searchable);

const dropUp = ref(false);

watch(filtered, () => {
    activeIndex.value = 0;
});

watch(activeIndex, () => {
    const root = rootRef.value;
    if (!root) {
        return;
    }

    root.querySelector('[data-active="true"]')?.scrollIntoView({
        block: 'nearest',
    });
});

function openPanel(): void {
    if (props.disabled || open.value) {
        return;
    }

    open.value = true;
    search.value = '';
    activeIndex.value = 0;

    nextTick(() => {
        const trigger = triggerRef.value;

        if (trigger) {
            const rect = trigger.getBoundingClientRect();
            dropUp.value = rect.bottom + 260 > window.innerHeight;
        }

        if (showSearch.value) {
            searchRef.value?.focus();
        }
    });
}

function close(focusTrigger = false): void {
    if (!open.value) {
        return;
    }

    open.value = false;
    search.value = '';

    if (focusTrigger) {
        triggerRef.value?.focus();
    }
}

function toggle(): void {
    if (open.value) {
        close();
    } else {
        openPanel();
    }
}

function select(option: Select2Option): void {
    model.value = option.value;
    close(true);
}

function move(delta: number): void {
    if (!open.value) {
        openPanel();
        return;
    }

    const count = filtered.value.length;
    if (count === 0) {
        return;
    }

    activeIndex.value = (activeIndex.value + delta + count) % count;
}

function confirmActive(): void {
    if (!open.value) {
        openPanel();
        return;
    }

    const option = filtered.value[activeIndex.value];
    if (option) {
        select(option);
    }
}

function onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        if (open.value) {
            event.preventDefault();
            close(true);
        }
        return;
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        move(1);
        return;
    }

    if (event.key === 'ArrowUp') {
        event.preventDefault();
        move(-1);
        return;
    }

    if (event.key === 'Enter') {
        event.preventDefault();
        confirmActive();
        return;
    }

    if (event.key === ' ' && event.target === triggerRef.value) {
        event.preventDefault();
        toggle();
        return;
    }

    if (event.key === 'Tab' && open.value) {
        close();
    }
}

function onDocumentPointer(event: MouseEvent): void {
    if (!open.value) {
        return;
    }

    if (rootRef.value && !rootRef.value.contains(event.target as Node)) {
        close();
    }
}

onMounted(() => document.addEventListener('mousedown', onDocumentPointer));

onBeforeUnmount(() =>
    document.removeEventListener('mousedown', onDocumentPointer),
);
</script>

<template>
    <div ref="rootRef" class="relative" @keydown="onKeydown">
        <button
            ref="triggerRef"
            type="button"
            :disabled="disabled"
            aria-haspopup="listbox"
            :aria-expanded="open"
            :aria-controls="listId"
            class="flex w-full items-center justify-between gap-2 rounded-md border bg-card px-3 py-2 text-left text-sm shadow-sm transition focus:border-primary focus:ring-2 focus:ring-primary/30 disabled:cursor-not-allowed disabled:bg-muted disabled:text-muted-foreground"
            :class="
                invalid
                    ? 'border-danger text-foreground'
                    : 'border-line text-foreground'
            "
            @click="toggle"
        >
            <span
                class="truncate"
                :class="isPlaceholder ? 'text-gray-400' : ''"
            >
                {{ display }}
            </span>
            <ChevronDown class="h-4 w-4 shrink-0 text-gray-400" />
        </button>

        <div
            v-if="open"
            :id="listId"
            role="listbox"
            class="absolute z-50 w-full overflow-hidden rounded-md border border-line bg-card shadow-lg"
            :class="dropUp ? 'bottom-full mb-1' : 'mt-1'"
        >
            <input
                v-if="showSearch"
                ref="searchRef"
                v-model="search"
                type="text"
                aria-label="Search options"
                class="w-full border-b border-line bg-transparent px-3 py-2 text-sm text-foreground placeholder:text-gray-400 focus:outline-none"
                placeholder="Search..."
            />
            <ul class="max-h-60 overflow-auto py-1">
                <li
                    v-for="(option, index) in filtered"
                    :key="String(option.value)"
                    role="option"
                    :data-active="index === activeIndex"
                    :aria-selected="
                        selected !== undefined &&
                        String(selected.value) === String(option.value)
                    "
                    class="cursor-pointer px-3 py-2 text-sm"
                    :class="
                        index === activeIndex
                            ? 'bg-emerald-soft text-primary dark:text-primary'
                            : 'text-foreground/80 hover:bg-background'
                    "
                    @mouseenter="activeIndex = index"
                    @click="select(option)"
                >
                    {{ option.label }}
                </li>
                <li
                    v-if="filtered.length === 0"
                    class="px-3 py-2 text-sm text-muted-foreground"
                >
                    {{ emptyText }}
                </li>
            </ul>
        </div>
    </div>
</template>
